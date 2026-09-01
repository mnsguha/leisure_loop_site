# Packages Catalog Page Design (packages.php)

This document outlines the UI/UX design specifications for the Tour Packages listing page (`packages.php`), maintaining the premium, cinematic aesthetic defined in the global Leisure Loop Design System.

## 1. Hero Banner
- **Background**: Cinematic, dark-themed luxury landscape image with a subtle parallax effect or cloud reveal overlay to maintain the "Extraordinary Escapes" motif.
- **Content**: 
  - Eyebrow label: `THE PORTFOLIO` or `THEME CURATION` (uppercase, wide tracking, gold).
  - Main Title: `Extraordinary Escapes.` (Massive serif typography, blending white and gold italics).
  - Subtitle: Elegant description text in `text-on-surface-variant` color.
  - Alignment: Left-aligned or centered within a max-width container, ensuring high readability over the background.

## 2. Page Layout (Main Container)
- **Structure**: A two-column layout (`md:flex-row`) with a gap between the filter sidebar and the main packages grid.
- **Spacing**: Generous padding (`py-20`, `gap-12`) to allow the layout to breathe, reflecting a luxury interface.

## 3. Left Column: Glassmorphic Filter & Sort Sidebar
- **Behavior**: Sticky positioning (`sticky top-28`) so filters remain accessible as the user scrolls through the catalog.
- **Styling**: `glass-card` styling with a semi-transparent dark background, heavy blur, and subtle white/gold borders (`rounded-[2rem]`, `p-8`, `shadow-2xl`).
- **Components**:
  - **Header**: "Filter by" title with a "Clear All" action button (gold text, minimal).
  - **Sort Options**: Custom radio buttons for "Curated Selection", "Price", and "Duration".
  - **Filter Groups**: Checkbox lists for Themes, Destinations, and Durations. Custom checkboxes with gold accent states when checked.
  - **Price Range**: Two numerical inputs for Min/Max price with a currency symbol (`₹`), accompanied by "Apply" and "Clear" buttons styled consistently with the UI.

## 4. Right Column: Packages Grid
- **Active Filters (Chips)**: A top row displaying currently active filters as sleek, dark pills with an "x" button to dismiss them.
- **Grid Structure**: Responsive grid (e.g., 1 column on mobile, 2 on tablet, 3 on large desktop) using standard gaps (`gap-8`).

### Package Card Design (`.catalog-card-item`)
- **Container**: `glass-card` aesthetic with `rounded-2xl` and `overflow-hidden`.
- **Hover Effects**: Smooth translateY lift (`transform: translateY(-8px)`), scale image slightly, and a subtle golden box-shadow on hover.
- **Image Section**:
  - Aspect ratio: Tall or square (e.g., `aspect-[4/3]`).
  - Overlay badge: "XX N / XX D" duration pill floating on the top-left or bottom-right corner of the image.
- **Content Section**:
  - Eyebrow: Destination name (e.g., "Sikkim Escapes") in uppercase gold.
  - Title: Bold, sans-serif or elegant serif title (`text-white`).
  - Footer/Pricing: Flex container with original price (strikethrough, muted) and selling price (prominent, gold or white), alongside a sleek arrow icon or "Explore" button.

## 5. Empty State
- **Trigger**: Displayed dynamically when filter criteria yield no results.
- **Visuals**: A central icon (e.g., an empty circle or compass), a polite message ("No bespoke collections match your selected parameters."), and a prominent gold "Reset Filters" button to recover the user journey.

## 6. Interactions & Animation
- **Filtering**: Client-side JS filtering for instantaneous UI updates without page reloads.
- **Transitions**: When filtering, cards should gracefully fade out and slide down (`translateY(15px)`, `opacity: 0`), and smoothly fade in when active.
