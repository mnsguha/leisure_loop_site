# Homepage Design Specifications (index.php)

This document outlines the UI/UX design specifications and structural layout for the primary landing page (`index.php`), adhering to the ultra-luxury, cinematic aesthetic defined in the global Leisure Loop Design System.

## 1. Editorial Hero Banner
- **Background**: Full-screen cinematic video loop or high-resolution luxury travel imagery, seamlessly darkening at the bottom to transition into the page content.
- **Content**: 
  - Eyebrow: `Explore Without Limits` (Elegant, tracking-widest, animated reveal).
  - Main Title: "Your Journey Begins Here." (Large serif typography, mixing regular and italicized `Playfair Display`).
  - Subtitle: Subtle, translucent descriptive text.
- **Interactive Element (Hero Form)**: An inline, 2-step booking/inquiry form floating over the hero. Glassmorphic aesthetic (`backdrop-blur-md`, subtle borders), featuring destination, dates, and a custom traveler dropdown popover.

## 2. Most Coveted Journeys (Featured Packages)
- **Layout**: Horizontal scrolling or grid-based featured packages.
- **Card Design**: Cinematic portrait cards (`aspect-[4/5]`) with grayscale-to-color hover effects.
- **Details**: Floating badges for Duration (e.g., "05 N / 06 D"), serif titles, striking price typography with gold highlights, and circular interaction buttons.

## 3. Global Escapes (International)
- **Background**: Interactive, 3D holographic wireframe globe effect with layered CSS elements to simulate a premium technological/global reach.
- **Elements**: Curved flight paths, airplane vector graphics.
- **Content**: Showcasing international high-end destinations with a similar luxury card treatment.

## 4. The Art of Discovery (How We Work)
- **Visuals**: CSS-only video parallax section. A large, high-resolution video playing behind a masked or semi-transparent foreground.
- **Purpose**: A storytelling section explaining the bespoke nature of Leisure Loop curations, emphasizing exclusivity and personalized itineraries.

## 5. Bespoke Curation Themes
- **Layout**: Dynamic horizontal pill strip for theme selection (e.g., "Himalayan Peaks", "Coastal Retreats").
- **Cards Carousel**: Thematic cards with deep luxury gradient overlays, top-float tags, and circular gold glass icon badges.

## 6. The Loop Distinction (Why Choose Us)
- **Structure**: A staggered, asymmetrical editorial layout.
  - Left column: Editorial showcase text (sticky or floating).
  - Right column: "Elite Pillars" (e.g., "Private Travel Architects", "Off-Market Privileges") laid out in a cascading list with custom gold numerical accents and minimalist iconography.

## 7. Journal & Insights
- **Cards**: Blog/editorial previews using strict geometric layouts. Clean typography, subtle zoom-on-hover image effects, and gold accent reveals on interaction.

## 8. Inner Circle (Newsletter)
- **Background**: Pinned parallax image (`background-attachment: fixed`) depicting a deep, atmospheric landscape (e.g., dark woods or mountains).
- **Foreground Container**: A centered glassmorphic card (`backdrop-blur-16px`, `rgba(5, 10, 20, 0.5)`) providing stark contrast to the background.
- **Typography**: "Join The Inner Circle" eyebrow in gold. Serif headline for the call to action.
- **Form Input**: Sleek, pill-shaped input field seamlessly merged with a gold "Subscribe" button.

## 9. Global UI Standards
- **Color Palette**: Dark theme dominant (`var(--bg)` `#050505`, surface `#0a0a0a`), accented strictly by Leisure Loop Gold (`#C5A059`) and stark white.
- **Typography**: 
  - Headlines: `Playfair Display` (Serif, elegant, high contrast).
  - Body/UI: `Inter` (Sans-serif, highly legible, wide tracking for uppercase labels).
- **Interactive States**: Hover effects utilize smooth `transform` (scale, translate) and `transition` over 0.4s to 0.7s to maintain a deliberately slow, premium feel.
