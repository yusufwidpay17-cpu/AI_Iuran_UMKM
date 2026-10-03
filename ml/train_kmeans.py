import os
import sys
import json
import pickle
import numpy as np
import pandas as pd
from datetime import datetime
import mysql.connector
from decimal import Decimal
from sklearn.preprocessing import StandardScaler
from sklearn.cluster import KMeans
from sklearn.decomposition import PCA
from sklearn.metrics import silhouette_score

def load_env(base_dir):
    """Parses Laravel's .env file for database configurations."""
    env_path = os.path.join(base_dir, '.env')
    config = {}
    if not os.path.exists(env_path):
        print(f"Error: .env file not found at {env_path}")
        return config
        
    with open(env_path, 'r') as f:
        for line in f:
            line = line.strip()
            if not line or line.startswith('#') or '=' not in line:
                continue
            key, val = line.split('=', 1)
            # Remove quotes if present
            val = val.strip().strip('"').strip("'")
            config[key.strip()] = val
    return config

def main():
    # Base directory is the parent of the ml directory
    base_dir = os.path.abspath(os.path.join(os.path.dirname(__file__), '..'))
    
    # Load environment configs
    env = load_env(base_dir)
    db_host = env.get('DB_HOST', '127.0.0.1')
    db_port = int(env.get('DB_PORT', '3306'))
    db_name = env.get('DB_DATABASE', 'db_tagihan_pasar')
    db_user = env.get('DB_USERNAME', 'root')
    db_pass = env.get('DB_PASSWORD', '')

    print("Connecting to database...")
    try:
        conn = mysql.connector.connect(
            host=db_host,
            port=db_port,
            database=db_name,
            user=db_user,
            password=db_pass
        )
        cursor = conn.cursor(dictionary=True, buffered=True)
    except Exception as e:
        print(f"Database connection error: {e}")
        sys.exit(1)

    # 1. Fetch features from fitur_pola_bayar for the latest batch calculation
    print("Fetching merchant payment features...")
    query = """
        SELECT * FROM fitur_pola_bayar 
        WHERE dihitung_pada = (SELECT MAX(dihitung_pada) FROM fitur_pola_bayar)
    """
    cursor.execute(query)
    rows = cursor.fetchall()
    
    if not rows:
        print("No features found in fitur_pola_bayar table. Please run feature extraction first.")
        conn.close()
        sys.exit(1)
        
    df = pd.DataFrame(rows)
    n_samples = len(df)
    print(f"Found {n_samples} merchant records to analyze.")

    # 2. Extract feature matrix
    feature_cols = [
        'total_hari_tagihan', 'jumlah_lunas', 'jumlah_terlambat', 
        'jumlah_bayar_sebagian', 'rata_rata_keterlambatan_hari', 
        'rasio_ketepatan_bayar', 'total_nominal_tagihan', 
        'total_nominal_dibayar', 'total_tunggakan'
    ]
    
    X = df[feature_cols].values
    
    # 3. Standardize features
    print("Normalizing features...")
    scaler = StandardScaler()
    X_scaled = scaler.fit_transform(X)
    
    # 4. Dimensionality reduction (PCA) for 2D scatter plotting
    print("Performing Principal Component Analysis (PCA)...")
    pca = PCA(n_components=2)
    X_pca = pca.fit_transform(X_scaled)
    
    # 5. Fit KMeans
    # We dynamically limit clusters if the dataset is extremely small (e.g. during early development)
    n_clusters = min(3, n_samples)
    if n_clusters < 2:
        print("Warning: Too few merchants to form distinct clusters. Assigning all to a single group.")
        labels = np.zeros(n_samples, dtype=int)
        score = 0.0
    else:
        print(f"Training K-Means with {n_clusters} clusters...")
        kmeans = KMeans(n_clusters=n_clusters, random_state=42, n_init=10)
        labels = kmeans.fit_predict(X_scaled)
        
        # Calculate silhouette score
        score = float(silhouette_score(X_scaled, labels))
        print(f"Clustering complete. Silhouette Score: {score:.4f}")

    # 6. Map cluster indices to merchant payment behaviors consistently
    # Let's map clusters by ordering them by mean rasio_ketepatan_bayar
    df['cluster_index'] = labels
    df['pc1'] = X_pca[:, 0]
    df['pc2'] = X_pca[:, 1]
    
    cluster_means = df.groupby('cluster_index')['rasio_ketepatan_bayar'].mean().reset_index()
    # Sort clusters by payment accuracy ratio ascending (lowest ratio = most risky, highest = most disciplined)
    cluster_means_sorted = cluster_means.sort_values(by='rasio_ketepatan_bayar').reset_index(drop=True)
    
    # Map index to text labels based on ranking
    label_mapping = {}
    if len(cluster_means_sorted) == 3:
        label_mapping[cluster_means_sorted.loc[0, 'cluster_index']] = "Berisiko"
        label_mapping[cluster_means_sorted.loc[1, 'cluster_index']] = "Cukup Rajin"
        label_mapping[cluster_means_sorted.loc[2, 'cluster_index']] = "Pembayar Rajin"
    elif len(cluster_means_sorted) == 2:
        label_mapping[cluster_means_sorted.loc[0, 'cluster_index']] = "Berisiko"
        label_mapping[cluster_means_sorted.loc[1, 'cluster_index']] = "Pembayar Rajin"
    else:
        label_mapping[0] = "Pembayar Rajin"
        
    df['label_segmen'] = df['cluster_index'].map(label_mapping)

    # 7. Save model and scaler objects using pickle
    print("Saving serialized model artifacts...")
    model_dir = os.path.join(base_dir, 'storage', 'app', 'ml', 'models')
    os.makedirs(model_dir, exist_ok=True)
    
    timestamp_str = datetime.now().strftime('%Y%m%d_%H%M%S')
    model_filename = f"kmeans_model_{timestamp_str}.pkl"
    scaler_filename = f"scaler_{timestamp_str}.pkl"
    
    model_path = os.path.join(model_dir, model_filename)
    scaler_path = os.path.join(model_dir, scaler_filename)
    
    # For n_clusters >= 2, we save the trained KMeans model
    if n_clusters >= 2:
        with open(model_path, 'wb') as f:
            pickle.dump(kmeans, f)
    else:
        with open(model_path, 'wb') as f:
            pickle.dump(None, f)
            
    with open(scaler_path, 'wb') as f:
        pickle.dump(scaler, f)

    # 8. Save model metadata to database
    print("Writing metadata to db...")
    # Deactivate other active models
    cursor.execute("UPDATE ml_model SET is_active = 0")
    
    # Insert new model metadata
    model_insert_query = """
        INSERT INTO ml_model (
            nama_model, algoritma, jumlah_cluster, parameter, silhouette_score,
            path_model_pkl, path_scaler_pkl, periode_data_awal, periode_data_akhir,
            is_active, trained_at, created_at, updated_at
        ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
    """
    
    param_dict = {"init": "k-means++", "random_state": 42, "n_init": 10}
    periode_awal = df['periode_awal'].min().strftime('%Y-%m-d') if isinstance(df['periode_awal'].min(), datetime) else str(df['periode_awal'].min())
    periode_akhir = df['periode_akhir'].max().strftime('%Y-%m-d') if isinstance(df['periode_akhir'].max(), datetime) else str(df['periode_akhir'].max())
    
    model_values = (
        f"K-Means Segmentasi Pedagang {datetime.now().strftime('%d/%m/%Y %H:%M')}",
        "K-Means",
        n_clusters,
        json.dumps(param_dict),
        score,
        f"storage/app/ml/models/{model_filename}",
        f"storage/app/ml/models/{scaler_filename}",
        periode_awal,
        periode_akhir,
        1, # is_active
        datetime.now()
    )
    
    cursor.execute(model_insert_query, model_values)
    ml_model_id = cursor.lastrowid
    
    # 9. Save merchant cluster assignments to database
    print("Writing cluster assignments to database...")
    for _, row in df.iterrows():
        # Create features snapshot dictionary
        snapshot = {}
        for col in feature_cols:
            val = row[col]
            if isinstance(val, (np.integer, np.int64)):
                snapshot[col] = int(val)
            elif isinstance(val, (np.floating, np.float64)):
                snapshot[col] = float(val)
            elif isinstance(val, Decimal):
                snapshot[col] = float(val)
            else:
                snapshot[col] = val
        # Add PCA values for 2D visualization
        snapshot['pc1'] = float(row['pc1'])
        snapshot['pc2'] = float(row['pc2'])

        # Check if merchant already has a segment mapping in database
        # If yes, we overwrite it. Otherwise, create a new one.
        cursor.execute(
            "SELECT id FROM segmentasi_pedagang WHERE pedagang_id = %s", 
            (row['pedagang_id'],)
        )
        existing = cursor.fetchone()
        
        if existing:
            # Update
            update_query = """
                UPDATE segmentasi_pedagang 
                SET ml_model_id = %s, cluster_index = %s, label_segmen = %s, 
                    fitur_snapshot = %s, tanggal_analisis = NOW(), updated_at = NOW()
                WHERE pedagang_id = %s
            """
            cursor.execute(update_query, (
                ml_model_id, 
                int(row['cluster_index']), 
                row['label_segmen'], 
                json.dumps(snapshot), 
                row['pedagang_id']
            ))
        else:
            # Insert
            insert_query = """
                INSERT INTO segmentasi_pedagang (
                    pedagang_id, ml_model_id, cluster_index, label_segmen, 
                    fitur_snapshot, tanggal_analisis, created_at, updated_at
                ) VALUES (%s, %s, %s, %s, %s, NOW(), NOW(), NOW())
            """
            cursor.execute(insert_query, (
                row['pedagang_id'], 
                ml_model_id, 
                int(row['cluster_index']), 
                row['label_segmen'], 
                json.dumps(snapshot)
            ))

    conn.commit()
    cursor.close()
    conn.close()
    print("AI Segmentation pipeline completed successfully!")

if __name__ == '__main__':
    main()
