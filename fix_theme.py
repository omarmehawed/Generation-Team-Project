import re

file_path = 'resources/views/welcome.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    text = f.read()

# Very specific, no variable length lookbehinds or greedy wildcards before the :class
# We just find :class="..." exactly.
pattern1 = re.compile(r':class\s*=\s*\"\{\s*\'([^\']+)\'\s*:\s*!darkMode\s*,\s*\'([^\']+)\'\s*:\s*darkMode\s*\}\"')
pattern2 = re.compile(r':class\s*=\s*\"\{\s*\'([^\']+)\'\s*:\s*darkMode\s*,\s*\'([^\']+)\'\s*:\s*!darkMode\s*\}\"')
pattern3 = re.compile(r':class\s*=\s*\"\{\s*\'([^\']+)\'\s*:\s*darkMode\s*\}\"')
pattern4 = re.compile(r':class\s*=\s*\"\{\s*\'([^\']+)\'\s*:\s*!darkMode\s*\}\"')

count = 0

def r1(m):
    global count
    count += 1
    light = m.group(1).strip()
    dark = m.group(2).strip()
    d_classes = ' '.join('dark:' + c for c in dark.split())
    # Instead of replacing into class="...", we just output class="light dark"
    # Wait, if there is a class="..." before it, it will create duplicate class="..."
    # browsers merge them or ignore the second. Let's just output `class="light dark"` 
    # and then we'll clean up duplicate class tags.
    return f'x-removed-class="{light} {d_classes}"'

text = pattern1.sub(r1, text)

def r2(m):
    global count
    count += 1
    dark = m.group(1).strip()
    light = m.group(2).strip()
    d_classes = ' '.join('dark:' + c for c in dark.split())
    return f'x-removed-class="{light} {d_classes}"'

text = pattern2.sub(r2, text)

# For 3, it's just dark mode classes
def r3(m):
    global count
    count += 1
    dark = m.group(1).strip()
    d_classes = ' '.join('dark:' + c for c in dark.split())
    return f'x-removed-class="{d_classes}"'
    
text = pattern3.sub(r3, text)

def r4(m):
    global count
    count += 1
    light = m.group(1).strip()
    return f'x-removed-class="{light}"'

text = pattern4.sub(r4, text)

# Now we have `class="py-8..." x-removed-class="bg-white..."`
# We can safely merge them without backtracking.
# Find `class="A" [whitespace] x-removed-class="B"`
merge_pattern = re.compile(r'class\s*=\s*\"([^"]*)\"(\s+)x-removed-class\s*=\s*\"([^"]*)\"')
while True:
    new_text = merge_pattern.sub(r'class="\1 \3"\2', text)
    if new_text == text:
        break
    text = new_text

# If there are any `x-removed-class="..."` left without a preceding `class="..."`, just rename them to `class="..."`
text = re.sub(r'x-removed-class\s*=\s*\"([^"]*)\"', r'class="\1"', text)

print("Replaced:", count)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(text)
