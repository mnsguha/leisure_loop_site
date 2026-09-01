from PIL import Image
import colorsys

img_path = r"g:\Antigravity\leisure_loop_site\public\assets\img\logo.webp"
out_path = r"g:\Antigravity\leisure_loop_site\public\assets\img\logo.webp"

img = Image.open(img_path).convert("RGBA")
width, height = img.size
data = img.load()

# The "Explore More" text is likely in the bottom half of the image.
# Let's target gray pixels in the bottom half.
for y in range(height // 2, height):
    for x in range(width):
        r, g, b, a = data[x, y]
        if a > 0:
            h, s, v = colorsys.rgb_to_hsv(r/255.0, g/255.0, b/255.0)
            # Low saturation (gray), not completely black, not completely white
            if s < 0.2 and 0.2 < v < 0.8:
                # Change to white, keeping original alpha
                data[x, y] = (255, 255, 255, a)

img.save(out_path, "WEBP")
print("Logo updated successfully.")
