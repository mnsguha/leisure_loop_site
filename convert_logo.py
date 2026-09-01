from PIL import Image
import os

img_path = r"g:\Antigravity\leisure_loop_site\public\assets\img\logo.png"
out_path = r"g:\Antigravity\leisure_loop_site\public\assets\img\logo_white.png"

try:
    img = Image.open(img_path).convert("RGBA")
    data = img.getdata()
    
    new_data = []
    for item in data:
        r, g, b, a = item
        # If the pixel is dark and has some opacity, make it white
        if r < 50 and g < 50 and b < 50 and a > 0:
            new_data.append((255, 255, 255, a))
        else:
            new_data.append(item)
            
    img.putdata(new_data)
    img.save(out_path)
    print("Successfully created logo_white.png")
except Exception as e:
    print(f"Error: {e}")
