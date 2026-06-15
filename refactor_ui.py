import os
import re

VIEWS_DIR = 'resources/views'

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content

    # 1. Global Layouts Wrapper Padding
    # Change <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> to include px-4
    content = re.sub(
        r'(class="[^"]*?max-w-[a-z0-9]+ mx-auto) sm:px-6 lg:px-8',
        r'\1 px-4 sm:px-6 lg:px-8',
        content
    )

    # Remove the random 'px-4 sm:px-0' inside grids since the parent now has px-4
    content = content.replace('px-4 sm:px-0', '')

    # 2. Form Grids
    # Change grid-cols-2 to grid-cols-1 sm:grid-cols-2
    content = re.sub(
        r'class="([^"]*?)grid-cols-2([^"]*?)"',
        lambda m: f'class="{m.group(1)}grid-cols-1 sm:grid-cols-2{m.group(2)}"' 
                  if 'sm:grid-cols-2' not in m.group(0) and 'md:grid-cols-2' not in m.group(0) and 'lg:grid-cols-2' not in m.group(0)
                  else m.group(0),
        content
    )

    # Change grid-cols-3 to grid-cols-1 sm:grid-cols-3 or md:grid-cols-3
    content = re.sub(
        r'class="([^"]*?)grid-cols-3([^"]*?)"',
        lambda m: f'class="{m.group(1)}grid-cols-1 md:grid-cols-3{m.group(2)}"' 
                  if 'sm:grid-cols-3' not in m.group(0) and 'md:grid-cols-3' not in m.group(0) and 'lg:grid-cols-3' not in m.group(0)
                  else m.group(0),
        content
    )
    
    # 3. Specific Dashboard adjustments
    # "grid grid-cols-2 md:grid-cols-5" -> "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5"
    content = content.replace('grid grid-cols-2 md:grid-cols-5', 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5')
    
    # "grid grid-cols-1 lg:grid-cols-2" -> remains same (mobile first)
    
    # "grid grid-cols-2 md:grid-cols-4" -> "grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4"
    content = content.replace('grid grid-cols-2 md:grid-cols-4', 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4')
    
    # "grid grid-cols-1 md:grid-cols-3" -> "grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3"
    content = content.replace('grid grid-cols-1 md:grid-cols-3', 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3')

    # 4. Tables wrappers
    # Make sure all tables have whitespace-nowrap in <td> to prevent breaking.
    # Actually, Laravel tables usually do, but let's check for <div class="overflow-x-auto">
    # If there's a table not wrapped in overflow-x-auto, we should wrap it.
    # A simple regex to wrap unwrapped tables is complex in Python. We will rely on manual checks if there's any.
    # However, replacing `<table ` with `<table class="... w-full` might help.
    
    # 5. Auth cards
    # Laravel breeze usually uses max-w-md w-full sm:max-w-md
    
    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated: {filepath}")

for root, _, files in os.walk(VIEWS_DIR):
    for file in files:
        if file.endswith('.blade.php'):
            process_file(os.path.join(root, file))

print("Refactoring script completed.")
