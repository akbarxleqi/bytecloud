---
name: Bytecloud Admin
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#3e4850'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#6f7881'
  outline-variant: '#bec8d1'
  surface-tint: '#00658f'
  primary: '#00658f'
  on-primary: '#ffffff'
  primary-container: '#24a1de'
  on-primary-container: '#00344c'
  inverse-primary: '#86cfff'
  secondary: '#515f74'
  on-secondary: '#ffffff'
  secondary-container: '#d5e3fd'
  on-secondary-container: '#57657b'
  tertiary: '#006591'
  on-tertiary: '#ffffff'
  tertiary-container: '#00a1e4'
  on-tertiary-container: '#00334c'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#c8e6ff'
  primary-fixed-dim: '#86cfff'
  on-primary-fixed: '#001e2e'
  on-primary-fixed-variant: '#004c6d'
  secondary-fixed: '#d5e3fd'
  secondary-fixed-dim: '#b9c7e0'
  on-secondary-fixed: '#0d1c2f'
  on-secondary-fixed-variant: '#3a485c'
  tertiary-fixed: '#c9e6ff'
  tertiary-fixed-dim: '#89ceff'
  on-tertiary-fixed: '#001e2f'
  on-tertiary-fixed-variant: '#004c6e'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  display-lg:
    fontFamily: Geist
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Geist
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Geist
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Geist
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Geist
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Geist
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Geist
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 14px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 4px
  gutter: 24px
  margin-page: 32px
  sidebar-width: 260px
  card-padding: 20px
---

## Brand & Style

The design system for this personal cloud dashboard focuses on the pillars of **Trust**, **Clarity**, and **Efficiency**. It is tailored for a tech-savvy user base that values a "prosumer" aesthetic—UI that feels powerful enough for heavy file management but remains accessible for daily personal use.

The chosen style is **Corporate Modern**, prioritizing a high-utility interface with a light, airy footprint. It utilizes significant white space to reduce cognitive load during file organization tasks. The aesthetic is defined by a rigorous adherence to a technical sans-serif typeface and a limited, high-contrast color palette that guides the user’s eye toward active states and primary calls to action.

## Colors

The palette is anchored by **Telegram Blue (#24A1DE)**, serving as the primary interactive color for buttons, progress indicators, and active selection states. This is balanced against a suite of **Deep Slate Grays** used for text and structural elements to provide a grounded, professional feel.

- **Primary:** Telegram Blue (#24A1DE) — used for the brand's main touchpoints and primary actions.
- **Secondary:** Slate 700 (#334155) — used for secondary text, icons, and sidebar labels.
- **Surface:** White (#FFFFFF) — used for primary content cards and backgrounds to maximize "cleanliness."
- **Background:** Slate 50 (#F8FAFC) — used as the foundational canvas color to provide subtle contrast against white cards.
- **Accents:** High-utility colors for system feedback, such as Emerald for "Sync Complete" and Amber for "Storage Warning."

## Typography

This design system utilizes **Geist** for its precision and technical clarity. The type hierarchy is designed to handle dense information—such as file lists and metadata—without feeling cluttered.

- **Headlines:** Use tighter letter spacing and semi-bold weights to create a strong visual anchor for page sections.
- **Body Text:** Set at 14px for standard UI elements to allow for high information density while maintaining readability.
- **Labels:** Small, uppercase labels are used for metadata like file sizes or dates to differentiate them from interactive text.
- **Mobile Adjustments:** Headlines scale down proportionally (e.g., Display LG moves to 28px) to ensure titles do not wrap excessively on smaller screens.

## Layout & Spacing

The layout follows a **fluid grid system** with a fixed-width sidebar for navigation. The sidebar remains at 260px on desktop to provide a consistent anchor, while the main content area expands to fill the viewport.

- **Grid:** A 12-column system is used for dashboard widgets, allowing for flexible layouts (e.g., 3-column stats or 2-column detail views).
- **Rhythm:** A 4px baseline grid ensures consistent vertical rhythm. Standard component spacing is set to 16px (4 units) or 24px (6 units) for larger groupings.
- **Responsive Behavior:** 
  - **Desktop:** Sidebar visible, 32px page margins.
  - **Tablet:** Sidebar collapses into an icon-only rail or hamburger menu; margins reduce to 24px.
  - **Mobile:** Single column stack; margins reduce to 16px to maximize horizontal space for file names.

## Elevation & Depth

Hierarchy is established through **tonal layers** and **ambient shadows** rather than heavy borders. The design system creates a sense of "physical" stacking to help users understand what elements are interactive or temporary.

- **Base Layer:** The light slate background (#F8FAFC) acts as the foundation.
- **Surface Layer:** White cards (#FFFFFF) sit on top of the background with a very subtle 1px border (#E2E8F0) and no shadow for a flat, clean look.
- **Raised State:** Interactive cards or dropdown menus use a soft, diffused shadow (`box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05)`) to indicate they are floating above the surface.
- **Overlay Layer:** Modals and dialogs use a more prominent shadow and a backdrop blur (glassmorphism) of 8px to focus the user’s attention on the task at hand.

## Shapes

The shape language is consistently **Rounded**, using an 8px radius as the base for most UI components. This softens the technical feel of the dashboard, making the product feel modern and "SaaS-premium."

- **Small Components (8px):** Buttons, input fields, and checkboxes use the base `rounded` setting.
- **Medium Components (12px):** Content cards and file preview thumbnails use `rounded-lg`.
- **Large Components (24px):** Modals and search bars use `rounded-xl` to feel distinct from the background grid.
- **Interactive Feedback:** Hover states on list items use a 6px radius to highlight the selection without overwhelming the content.

## Components

Components are designed to be minimal and functional, reducing visual noise to focus on user data.

- **Buttons:** 
  - *Primary:* Solid Telegram Blue with white text. 
  - *Secondary:* Ghost style with a Slate 200 border and Slate 700 text.
- **Input Fields:** Flat white background with a subtle border that transitions to Telegram Blue on focus. Labels are always Geist Medium, positioned above the field.
- **Storage Indicators:** Custom progress bars using a thick, 8px track. The "filled" portion uses a gradient of Telegram Blue to differentiate it from standard system bars.
- **File Cards:** Grid-view cards feature a large thumbnail preview, a 12px corner radius, and a subtle hover-up animation (2px lift) to provide tactile feedback.
- **Navigation:** The sidebar uses "active indicator pills"—a vertical blue bar on the left of the active menu item combined with a light blue background tint.
- **Icons:** Minimalist 24px stroke icons (e.g., Lucide or Phosphor) with a 1.5px stroke weight, consistently colored in Slate 500 for inactive and Primary Blue for active states.