with open('public/css/style.css', 'a', encoding='utf-8') as f:
    f.write("\n\n/* ─── NARRATIVE CASCADING IMAGES ─── */\n")
    f.write(".narrative-img-card { transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1); position: absolute; cursor: pointer; }\n")
    f.write('.narrative-img-card[data-pos="0"] { top: 0%; left: 0%; z-index: 30; }\n')
    f.write('.narrative-img-card[data-pos="1"] { top: 16.66%; left: 16.66%; z-index: 20; }\n')
    f.write('.narrative-img-card[data-pos="2"] { top: 33.33%; left: 33.33%; z-index: 10; }\n')
