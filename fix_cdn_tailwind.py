import os
import re

VIEWS_DIR = 'resources/views'

SCRIPT_TO_INJECT = """    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { primary: '#2596be' }
                }
            }
        }
    </script>"""

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # If it uses cdn and doesn't have tailwind.config
    if 'cdn.tailwindcss.com' in content and 'tailwind.config' not in content:
        # insert after the cdn script tag
        content = re.sub(
            r'(<script src="https://cdn\.tailwindcss\.com"></script>)',
            r'\1\n' + SCRIPT_TO_INJECT,
            content
        )
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        return True
    return False

def main():
    modified = 0
    for root, _, files in os.walk(VIEWS_DIR):
        for file in files:
            if file.endswith('.blade.php'):
                if process_file(os.path.join(root, file)):
                    modified += 1
    print(f"Fixed {modified} files.")

if __name__ == '__main__':
    main()
