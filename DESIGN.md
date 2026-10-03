---
name: Nusantara SME Core
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#434654'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#737685'
  outline-variant: '#c3c6d6'
  surface-tint: '#0c56d0'
  primary: '#003d9b'
  on-primary: '#ffffff'
  primary-container: '#0052cc'
  on-primary-container: '#c4d2ff'
  inverse-primary: '#b2c5ff'
  secondary: '#006c49'
  on-secondary: '#ffffff'
  secondary-container: '#6cf8bb'
  on-secondary-container: '#00714d'
  tertiary: '#603b00'
  on-tertiary: '#ffffff'
  tertiary-container: '#805000'
  on-tertiary-container: '#ffc988'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dae2ff'
  primary-fixed-dim: '#b2c5ff'
  on-primary-fixed: '#001848'
  on-primary-fixed-variant: '#0040a2'
  secondary-fixed: '#6ffbbe'
  secondary-fixed-dim: '#4edea3'
  on-secondary-fixed: '#002113'
  on-secondary-fixed-variant: '#005236'
  tertiary-fixed: '#ffddb8'
  tertiary-fixed-dim: '#ffb95f'
  on-tertiary-fixed: '#2a1700'
  on-tertiary-fixed-variant: '#653e00'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  headline-lg:
    fontFamily: Work Sans
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Work Sans
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  headline-md:
    fontFamily: Work Sans
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 14px
    letterSpacing: 0.04em
  numeric-display:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  container-max: 1280px
  gutter: 16px
  margin-mobile: 16px
  margin-desktop: 32px
---

## Brand & Style

The brand personality is **reliable, empowering, and transparent**. It is designed specifically for Indonesian SME (UMKM) owners and BUMPes administrators who require a tool that feels as stable as a traditional bank but as agile as a modern startup. The visual language avoids over-decoration to ensure users—regardless of technical literacy—can focus on their financial health without distraction.

This design system utilizes a **Corporate / Modern** style with a focus on **Data-Centricity**. It prioritizes high legibility and clear information hierarchy. By blending a structured grid with soft organic touches, the UI evokes a sense of "digital growth" (Pertumbuhan Digital), making complex transaction monitoring feel manageable and trustworthy.

## Colors

The palette is rooted in trust and prosperity. 
- **Primary (Deep Navy Blue):** Represents institutional stability and financial security. It is used for core navigation, primary actions, and branding.
- **Secondary (Growth Green):** Symbolizes profit, success, and positive cash flow. Reserved for "Success" states, "Inflow" transactions, and growth indicators.
- **Tertiary (Alert Amber):** Used sparingly for pending transactions or items requiring attention.
- **Neutrals:** A range of cool grays that maintain a clean, "breathable" interface, preventing the data-heavy screens from feeling cluttered.

The default mode is **Light**, optimized for legibility in various lighting conditions typical of local business environments (stores, kiosks, and outdoor offices).

## Typography

Typography is the backbone of this design system, ensuring financial figures are unmistakable. 
- **Work Sans** is used for headings to provide a professional and grounded character. 
- **Inter** is utilized for all body copy and interface elements due to its exceptional readability and neutral, utilitarian tone. 
- **Numeric-Display:** Transaction amounts should use a slightly heavier weight and tabular lining (monospace numbers) where possible to ensure columns of figures align perfectly in tables and lists.
- **Accessibility:** Minimum body text size is kept at 14px (body-sm) to assist older business owners with visual clarity.

## Layout & Spacing

The design system employs a **Fluid Grid** with a 12-column structure for desktop and a 4-column structure for mobile. 
- **The 8pt Grid System:** All spacing, margins, and component heights are multiples of 8px (4px for minor adjustments). This ensures a rhythmic consistency across all pages.
- **Mobile-First Data:** Tables on desktop reflow into "Data Cards" on mobile devices to prevent horizontal scrolling of financial records.
- **Safe Areas:** Generous margins (24px-32px) are used on desktop to create a centered, focused workspace, while mobile margins are tighter (16px) to maximize screen real estate for transaction lists.

## Elevation & Depth

Visual hierarchy is established through **Tonal Layers** and **Low-contrast Outlines**. 
- **Surface Tiers:** The main background is a very light gray (`#F8FAFC`). Primary content containers (cards) use a pure white background with a subtle 1px border (`#E2E8F0`).
- **Soft Ambient Shadows:** Shadows are reserved for floating elements like dropdowns, modals, and the "Primary Action" button (e.g., "Add New Transaction"). These shadows use a deep navy tint with high diffusion (15-20% opacity) to feel modern and non-obstructive.
- **Interactive Depth:** On hover, cards may lift slightly using a secondary shadow level to indicate interactivity without using loud color changes.

## Shapes

The shape language is **Rounded**, striking a balance between the rigidity of traditional finance and the friendliness of modern mobile apps. 
- **Standard Radius (0.5rem):** Used for buttons, input fields, and standard cards. This creates a modern, accessible look.
- **Large Radius (1rem):** Used for primary dashboard containers and promotional banners.
- **Full Radius (Pill):** Used exclusively for Status Chips (e.g., "Paid", "Pending") and small "Action Indicators" to make them instantly distinguishable from square-ish functional buttons.

## Components

- **Buttons:** Primary buttons are solid Navy Blue with white text. Secondary buttons use an outline style. Touch targets are a minimum of 48px height for mobile accessibility.
- **Inputs:** Form fields feature persistent labels and clear "Placeholder" examples. On focus, the border transitions to Primary Blue with a soft 2px glow.
- **Transaction Cards:** On mobile, each transaction is a card with the "Amount" prominently displayed on the right and the "Category/Date" on the left.
- **Status Chips:** Use a light background tint of the status color (e.g., light green background with dark green text for "Selesai").
- **Data Tables:** Feature zebra-striping (subtle light gray alternates) to help users track rows of financial data across large screens.
- **Action Floating Action Button (FAB):** A prominent "+" button in the bottom right for mobile users to quickly record a sale or expense.
- **Empty States:** Simple, friendly illustrations with a clear "Primary Action" to guide the user on how to start recording data.