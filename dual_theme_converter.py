import os
import re

# Directory to scan
VIEWS_DIR = 'resources/views'

# Mapping of Light Mode classes to their new Dark Mode equivalents
CLASS_MAPPINGS = {
    r'\bbg-white\b': 'dark:bg-gray-800',
    r'\bbg-gray-50\b': 'dark:bg-gray-900',
    r'\bbg-gray-100\b': 'dark:bg-gray-900',
    
    r'\btext-gray-900\b': 'dark:text-gray-100',
    r'\btext-gray-800\b': 'dark:text-gray-200',
    r'\btext-gray-700\b': 'dark:text-gray-300',
    r'\btext-gray-600\b': 'dark:text-gray-400',
    r'\btext-gray-500\b': 'dark:text-gray-400',
    r'\btext-black\b': 'dark:text-white',
    
    r'\bborder-gray-100\b': 'dark:border-gray-700',
    r'\bborder-gray-200\b': 'dark:border-gray-700',
    r'\bborder-gray-300\b': 'dark:border-gray-600',
}

# Explicit hex color replacements (replacing legacy gold with new primary)
HEX_REPLACEMENTS = {
    '#D4AF37': '#2596be',
    '#fbbf24': '#2596be',
    '#f59e0b': '#2596be',
    '#d97706': '#2596be',
    '#b45309': '#1c7ca0', # darker shade for hover
}

def process_class_string(class_str):
    """
    Takes a string of classes (e.g., 'bg-white p-4 text-gray-900')
    and appends the corresponding dark classes if they don't already exist.
    """
    classes = class_str.split()
    new_classes = []
    
    for c in classes:
        new_classes.append(c)
        for light_pattern, dark_class in CLASS_MAPPINGS.items():
            if re.fullmatch(light_pattern, c):
                # Check if the dark class is already in the list
                if dark_class not in classes and dark_class not in new_classes:
                    new_classes.append(dark_class)
                    
    return ' '.join(new_classes)

def replace_classes_in_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content

    # Find all class="..." attributes and process their contents
    # This regex handles single and double quotes
    def class_replacer(match):
        prefix = match.group(1) # class="
        class_str = match.group(2)
        suffix = match.group(3) # "
        
        new_class_str = process_class_string(class_str)
        return f'{prefix}{new_class_str}{suffix}'

    # Regex to match class="something" or class='something'
    content = re.sub(r'(class=[\'"])(.*?)([\'"])', class_replacer, content)

    # Replace legacy hex colors
    for old_hex, new_hex in HEX_REPLACEMENTS.items():
        # Case insensitive replace for hex codes
        content = re.sub(re.escape(old_hex), new_hex, content, flags=re.IGNORECASE)

    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        return True
    return False

def main():
    modified_count = 0
    for root, _, files in os.walk(VIEWS_DIR):
        for file in files:
            if file.endswith('.blade.php'):
                filepath = os.path.join(root, file)
                if replace_classes_in_file(filepath):
                    modified_count += 1
                    print(f"Updated: {filepath}")

    print(f"\nDone! Modified {modified_count} files to support Dual-Theme.")

if __name__ == '__main__':
    main()
