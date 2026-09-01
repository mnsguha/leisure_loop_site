import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

# ---- Step 1: Strip the first 818 lines that are all raw extracted with bad indentation ----
# Lines 1-818 have 17 extracted comment blocks interleaved with poorly indented code.
# We will keep lines 1..818 but just clean the "/* Extracted from ... */" comment strings.
content = re.sub(r'/\* Extracted from [^*]+\*/', '', content)

# ---- Step 2: Delete orphaned blocks (lines 819-1714 area) ----
# These are:
#   custom-typo-showcase block (line 819-997)
#   sikkim-static-backup blocks (lines 998-1713)
# The safe pointer-events/z-index block (1697-1713) we WANT to keep but rewrite.
# Strategy: delete from first typo-showcase :root block up to the pointer-events comment.

# Mark with unique sentinel
sentinel_keep = '/* === KEEP_FROM_HERE === */'

# The last "good" section starts at the known pointer events block 
content = content.replace(
    '.flight-anim,',
    sentinel_keep + '\n.flight-anim,'
)

# If sentinel not placed (block already removed), try the next known keeper
if sentinel_keep not in content:
    content = content.replace(
        '/* Staggered Destination Cards */',
        sentinel_keep + '\n/* Staggered Destination Cards */'
    )

# Now extract: everything before the custom-typo :root block + everything after sentinel
# The custom-typo :root looks like:  :root {\n            --navy: #0b1d33;
showcase_start = re.search(r':root \{\s*\n\s*--navy:', content)
sentinel_pos = content.find(sentinel_keep)

if showcase_start and sentinel_pos > 0 and showcase_start.start() < sentinel_pos:
    before_orphan = content[:showcase_start.start()]
    after_sentinel = content[sentinel_pos + len(sentinel_keep):]
    content = before_orphan + after_sentinel
    print('Orphaned blocks removed.')
else:
    print(f'showcase_start={showcase_start}, sentinel_pos={sentinel_pos}')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
    f.write(content)
