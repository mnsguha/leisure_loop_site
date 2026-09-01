from PIL import Image

img_path = r"g:\Antigravity\leisure_loop_site\public\assets\img\leisure.png"
img = Image.open(img_path).convert("RGBA")

bbox = img.getbbox()
if bbox:
    cropped = img.crop(bbox)
    width, height = cropped.size
    
    empty_cols = []
    for x in range(width):
        is_empty = True
        for y in range(height):
            if cropped.getpixel((x, y))[3] > 0:
                is_empty = False
                break
        if is_empty:
            empty_cols.append(x)
            
    split_x = None
    if empty_cols:
        streaks = []
        current_streak = []
        for col in empty_cols:
            if not current_streak or col == current_streak[-1] + 1:
                current_streak.append(col)
            else:
                streaks.append(current_streak)
                current_streak = [col]
        if current_streak:
            streaks.append(current_streak)
            
        largest_streak = max(streaks, key=len)
        split_x = largest_streak[len(largest_streak)//2]
        
    if split_x:
        icon = cropped.crop((0, 0, split_x, height))
        text = cropped.crop((split_x, 0, width, height))
        
        icon_bbox = icon.getbbox()
        if icon_bbox: icon = icon.crop(icon_bbox)
            
        text_bbox = text.getbbox()
        if text_bbox: text = text.crop(text_bbox)
        
        icon.save(r"g:\Antigravity\leisure_loop_site\public\assets\img\mobile_icon.png")
        text.save(r"g:\Antigravity\leisure_loop_site\public\assets\img\mobile_text.png")
        print(f"Success! Split at column {split_x}")
    else:
        print("Error: Could not find an empty gap between icon and text.")
else:
    print("Error: Image is completely transparent.")
