# Leisure Loop Design System

This document outlines the core design tokens, layout paradigms, and cinematic styling rules used in the Leisure Loop platform, modeled after the premium destination experience (e.g., Sikkim / destination-details.php).

## 1. Color Palette
- **Primary Dark Base (Midnight Blue):** `#030811` - Used for deep stacking backgrounds and contrast panels.
- **Luxury Gold Accent:** `#C5A059` (rgb: 197, 160, 89) - Used for section labels, italicized serif highlights, and interactive borders.
- **Pure White:** `#FFFFFF` - Used for the unified editorial sections and high-contrast text.
- **Sky Light Blue:** `#E0F0FF` - Used as the base for the parallax hero sections (cloud/sky backdrop).
- **Glassmorphism Base:** `rgba(10, 10, 10, 0.4)` to `0.6` with `backdrop-filter: blur(12px)`.

## 2. Typography
- **Primary Heading Font (Serif):** Elegant serif (e.g., Playfair Display, Lora). Used for massive hero titles `clamp(6rem, 15vw, 12rem)` and italicized golden accents (`font-style: italic; color: var(--gold)`).
- **Body & Secondary Font (Sans-Serif):** Clean, modern sans-serif (e.g., Inter, Montserrat). Used for UI elements, labels, and standard body copy.
- **Microcopy / Labels:** Uppercase, wide letter-spacing (`letter-spacing: 0.3em; font-weight: 600`), often rendered in gold.

## 3. UI Components & Borders
- **Corner Roundness:** Soft but structured (`border-radius: 16px` on glass cards).
- **Glass Cards (.hero-card, .sight-card, .dest-pkg-card):**
  - Semi-transparent dark background (`rgba(10,10,10,0.6)`) with heavy blur.
  - Subtle borders: `border: 1px solid rgba(255, 255, 255, 0.15)`.
  - Hover states: Gentle lift `transform: translateY(-8px) scale(1.02)` and a golden glow shadow `box-shadow: 0 20px 40px rgba(197, 160, 89, 0.08)`.
  - Forms use sleek inline inputs with embedded SVG icons and minimal borders.

## 4. Layout & Animation Paradigms
- **Cinematic Scroll & Parallax:**
  - Heavy use of layered PNGs for 3D parallax hero sections, controlled by mouse movement and scroll (GSAP ScrollTrigger).
- **Stacking Panels:** Sections act like a deck of cards. The current section is pinned while the next section scrolls up over it.
- **Unified Editorial Section:** Overlaps seamlessly using CSS masks to blend photography into pure white backgrounds. Text reveals use GSAP split-text.

## 5. Visual Motifs
- **Atmospheric Effects:** Moving clouds, floating particles (HTML5 Canvas), and dynamic SVG scroll trails.
