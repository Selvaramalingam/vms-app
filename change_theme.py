import os
import glob

def update_theme():
    directory = 'resources/views/**/*.blade.php'
    files = glob.glob(directory, recursive=True)
    
    # Also include app.js if there are references (though likely none)
    # files.append('resources/js/app.js')
    
    replacements = {
        'bg-zinc-900': 'bg-indigo-600',
        'hover:bg-zinc-800': 'hover:bg-indigo-700',
        'active:bg-zinc-950': 'active:bg-indigo-800',
        'focus:border-zinc-900': 'focus:border-indigo-500',
        'focus:ring-zinc-900': 'focus:ring-indigo-500',
        'text-zinc-900': 'text-indigo-600',
    }
    
    changed_files = 0
    
    for filepath in files:
        if not os.path.isfile(filepath):
            continue
            
        with open(filepath, 'r') as file:
            content = file.read()
            
        original_content = content
        
        for old, new in replacements.items():
            content = content.replace(old, new)
            
        if content != original_content:
            with open(filepath, 'w') as file:
                file.write(content)
            changed_files += 1

    print(f"Theme updated in {changed_files} files.")

if __name__ == '__main__':
    update_theme()
