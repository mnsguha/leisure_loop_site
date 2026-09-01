import sys
from rembg import remove
from PIL import Image

def process_image(input_path, output_path):
    try:
        # Load the input image
        input_image = Image.open(input_path)
        
        # Remove the background
        output_image = remove(input_image)
        
        # Save the result
        output_image.save(output_path, 'PNG')
        print(f"Success: Saved {output_path}")
    except Exception as e:
        print(f"Error processing {input_path}: {e}")

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python process_layer.py <input_path> <output_path>")
        sys.exit(1)
        
    input_path = sys.argv[1]
    output_path = sys.argv[2]
    process_image(input_path, output_path)
