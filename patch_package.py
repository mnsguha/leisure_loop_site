import re

fpath = r'g:\Antigravity\leisure_loop_site\public\package.php'

with open(fpath, 'r', encoding='utf-8') as f:
    content = f.read()

html_new = """<section>
<h2 class="font-display-md text-white mb-10">Day-by-Day Journey</h2>
<div class="space-y-4">
<details class="group glass-card rounded-2xl p-6 open:bg-white/5 transition-all" open="">
<summary class="flex justify-between items-center cursor-pointer">
<div class="flex gap-6 items-center">
<span class="w-12 h-12 flex items-center justify-center rounded-full bg-secondary/20 text-secondary font-bold">01</span>
<div>
<h3 class="font-bold text-lg text-white">Arrival &amp; Gangtok Transfer</h3>
<p class="text-sm text-on-surface-variant">The Capital City of Sikkim</p>
</div>
</div>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-180">expand_more</span>
</summary>
<div class="pt-6 pl-[4.5rem] text-on-surface-variant leading-relaxed">
A representative will be there to Welcome our guests on arrival at Bagdogra Airport/NJP Railway Station and he will be assisting for transfer our guest to Gangtok - Approx 135 kilometers 4 ½ - 5 hours drive. Gangtok is the capital city of Sikkim known for its natural beauty, exotic flora &amp; fauna, magnificent vistas, indo-tibetan food, mystic rituals at an height of 1670 meters / 5480 feet. On arrival check-in to hotel &amp; rest of the day enjoy the leisure activities of the hotel property or free to roam around famous MG Road(Shopping Arena) or satisfy taste buds by having local foods. Overnight stay at Gangtok.
</div>
</details>

<details class="group glass-card rounded-2xl p-6 transition-all">
<summary class="flex justify-between items-center cursor-pointer">
<div class="flex gap-6 items-center">
<span class="w-12 h-12 flex items-center justify-center rounded-full bg-secondary/20 text-secondary font-bold">02</span>
<div>
<h3 class="font-bold text-lg text-white">Tsomgo Lake Excursion</h3>
<p class="text-sm text-on-surface-variant">Glacial Lakes &amp; Sacred Shrines</p>
</div>
</div>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-180">expand_more</span>
</summary>
<div class="pt-6 pl-[4.5rem] text-on-surface-variant leading-relaxed">
After breakfast at hotel start for the Excursion to Tsomgo Lake nearly 40km from Gangtok. The beautiful Lake is oval shaped glacial lake, Surrounded by rugged mountains on all sides, this scenic lake is located at an altitude of 12313 ft, generally snow covered almost all year around, nearly about 50 feet deep &amp; more than 1km long &amp; home to many migratory birds. On the way to Tsomgo Lake take a halt at Kyongnosla waterfalls at Kyongnosla Alpine Sanctuary - the home to the red panda &amp; Tibetan wolf with scenic beauty of alpine trees. Nearby is the Baba Mandir (around 17 km from Tsomgo Lake)- a sacred site for all pilgrims - named after an army man Baba Harbhajan Singh who sacrificed his life for the nation. - situated at a height of 13123 ft. After visited all the locations back to Gangtok and overnight stay there. (Incase of Landslide or due to any other reasons if Tsomgo Lake is closed then an alternate sightseeing will be provided.)
</div>
</details>

<details class="group glass-card rounded-2xl p-6 transition-all">
<summary class="flex justify-between items-center cursor-pointer">
<div class="flex gap-6 items-center">
<span class="w-12 h-12 flex items-center justify-center rounded-full bg-secondary/20 text-secondary font-bold">03</span>
<div>
<h3 class="font-bold text-lg text-white">Journey to Lachung</h3>
<p class="text-sm text-on-surface-variant">Waterfalls &amp; Scenic Views</p>
</div>
</div>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-180">expand_more</span>
</summary>
<div class="pt-6 pl-[4.5rem] text-on-surface-variant leading-relaxed">
After breakfast pick up from hotel &amp; transfer to Lachung (8,800 ft). Enroute visit Singhik View point, Seven Sister Water Fall, Naga Water Fall, and arrive Lachung by evening. Dinner at hotel. Overnight stay at Lachung.
</div>
</details>

<details class="group glass-card rounded-2xl p-6 transition-all">
<summary class="flex justify-between items-center cursor-pointer">
<div class="flex gap-6 items-center">
<span class="w-12 h-12 flex items-center justify-center rounded-full bg-secondary/20 text-secondary font-bold">04</span>
<div>
<h3 class="font-bold text-lg text-white">Yumthang Valley Excursion</h3>
<p class="text-sm text-on-surface-variant">The Valley of Flowers</p>
</div>
</div>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-180">expand_more</span>
</summary>
<div class="pt-6 pl-[4.5rem] text-on-surface-variant leading-relaxed">
After early breakfast drive up for Yumthang valley (11,800 ft. / 24 km / 2 hours) excursion tour. Yumthang, where the tree line ends and the rhododendron groves cover the landscape in a surreal shade. Yumthang also called the valley of flowers as in spring wild alpine flowers carpet the land. Yumthang is also known for its hot spring, which have healing medicinal properties. Overnight stay.
</div>
</details>

<details class="group glass-card rounded-2xl p-6 transition-all">
<summary class="flex justify-between items-center cursor-pointer">
<div class="flex gap-6 items-center">
<span class="w-12 h-12 flex items-center justify-center rounded-full bg-secondary/20 text-secondary font-bold">05</span>
<div>
<h3 class="font-bold text-lg text-white">Return to Gangtok</h3>
<p class="text-sm text-on-surface-variant">Descending the Himalayas</p>
</div>
</div>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-180">expand_more</span>
</summary>
<div class="pt-6 pl-[4.5rem] text-on-surface-variant leading-relaxed">
After breakfast drive to Gangtok. Overnight stay at Gangtok.
</div>
</details>

<details class="group glass-card rounded-2xl p-6 transition-all">
<summary class="flex justify-between items-center cursor-pointer">
<div class="flex gap-6 items-center">
<span class="w-12 h-12 flex items-center justify-center rounded-full bg-secondary/20 text-secondary font-bold">06</span>
<div>
<h3 class="font-bold text-lg text-white">Departure</h3>
<p class="text-sm text-on-surface-variant">Onward Journey</p>
</div>
</div>
<span class="material-symbols-outlined text-secondary transition-transform group-open:rotate-180">expand_more</span>
</summary>
<div class="pt-6 pl-[4.5rem] text-on-surface-variant leading-relaxed">
Post breakfast at hotel, proceed for the Bagdogra Airport (IXB) / NJP Railway Station. Board your flight / train for your onward destination with cheerful memory of your holiday.
</div>
</details>
</div>
</section>"""

# Find the section to replace
import re
pattern = re.compile(r'<section>\s*<h2 class="font-display-md text-white mb-10">Day-by-Day Journey</h2>\s*<div class="space-y-4">.*?</section>', re.DOTALL)
content = pattern.sub(html_new, content)

with open(fpath, 'w', encoding='utf-8') as f:
    f.write(content)

print("Patching complete for package.php.")
