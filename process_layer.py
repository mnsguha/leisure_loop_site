import urllib.request
from rembg import remove
from PIL import Image

def main():
    # URL of a beautiful mountain with a clear sky
    url = "https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=1200&auto=format&fit=crop"
    bg_path = "public/images/parallax/sikkim_bg.jpg"
    fore_path = "public/images/parallax/sikkim_fore.png"
    
    print("Downloading image...")
    urllib.request.urlretrieve(url, bg_path)
    
    print("Processing with rembg...")
    input_image = Image.open(bg_path)
    output_image = remove(input_image)
    output_image.save(fore_path, 'PNG')
    
    print("Done!")

if __name__ == "__main__":
    main()
