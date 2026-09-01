import re

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

# I appended the previous CSS with /* Staggered Destination Cards */
start_idx = content.find('/* Staggered Destination Cards */')
if start_idx != -1:
    content = content[:start_idx]
    print("Found and removed previous stagger block.")
else:
    print("Previous stagger block not found via comment.")

new_css = '''/* Staggered Destination Cards */
.destinations-carousel {
    display: flex;
    align-items: center;
    padding: 50px 0 70px 0 !important;
    gap: 24px;
}

.dest-card {
    transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1), box-shadow 0.4s ease !important;
    will-change: transform;
}

/* Normal Stagger Offsets */
.dest-card.stagger-up {
    transform: translateY(-35px);
}

.dest-card.stagger-down {
    transform: translateY(35px);
}

/* Inverted Hover Interactions */
/* When hovering an UP card, it glides DOWN */
.dest-card.stagger-up:hover {
    transform: translateY(20px) scale(1.02) !important;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
    z-index: 10;
}

/* When hovering a DOWN card, it glides UP */
.dest-card.stagger-down:hover {
    transform: translateY(-20px) scale(1.02) !important;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
    z-index: 10;
}

/* Disable on mobile screens */
@media (max-width: 768px) {
    .dest-card.stagger-up,
    .dest-card.stagger-down,
    .dest-card.stagger-up:hover,
    .dest-card.stagger-down:hover {
        transform: translateY(0) !important;
    }
}
'''

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
    f.write(content + new_css)
