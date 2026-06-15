import os
import re

VIEWS_DIR = 'resources/views'

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content

    # 1. Card Upgrades
    # Replace standard laravel cards
    content = content.replace('bg-white shadow-sm sm:rounded-lg', 'bg-white border border-slate-200 rounded-xl shadow-sm')
    content = content.replace('bg-white overflow-hidden shadow-sm sm:rounded-lg', 'bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden')
    
    # 2. Input Upgrades
    # Remove blue rings and update to sleek zinc rings
    content = content.replace('focus:border-blue-300', 'focus:border-zinc-900')
    content = content.replace('focus:ring-blue-200', 'focus:ring-zinc-900 focus:ring-1 focus:ring-offset-0')
    content = content.replace('border-gray-300', 'border-slate-200 text-sm')
    
    # 3. Typography Upgrades
    # Label text colors
    content = content.replace('text-gray-700 font-bold', 'text-slate-700 font-medium')
    content = content.replace('text-gray-700', 'text-slate-700')
    content = content.replace('text-gray-800', 'text-slate-900')
    content = content.replace('text-gray-900', 'text-slate-900')
    content = content.replace('text-gray-500', 'text-slate-500')
    
    # 4. Dashboard Specific cleanup (remove legacy colored borders)
    content = re.sub(r'border-l-4 border-(blue|indigo|green|red|purple|orange)-500', '', content)
    # Remove legacy orange-100 backgrounds
    content = content.replace('bg-orange-100', 'bg-amber-50 border border-amber-200')
    
    # 5. Buttons Upgrades
    # Primary actions (e.g., Save, Submit)
    content = content.replace('bg-blue-600', 'bg-zinc-900')
    content = content.replace('hover:bg-blue-700', 'hover:bg-zinc-800')
    content = content.replace('active:bg-blue-800', 'active:bg-zinc-950')
    
    # Action buttons (Edit/Delete in tables)
    # Convert blue/red text buttons to modern slate/red ghost styles
    content = content.replace('text-blue-600 hover:text-blue-900 bg-blue-50', 'text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200')
    content = content.replace('text-red-600 hover:text-red-900 bg-red-50', 'text-rose-600 hover:text-rose-900 bg-rose-50 hover:bg-rose-100 border border-rose-100')
    
    # 6. Table Upgrades
    content = content.replace('divide-gray-200', 'divide-slate-100')
    content = content.replace('bg-gray-50', 'bg-slate-50/50')
    content = content.replace('bg-gray-100', 'bg-slate-50/80')
    # Table headers
    content = content.replace('text-xs font-bold text-gray-500 uppercase', 'text-xs font-medium text-slate-500 uppercase tracking-wider')
    content = content.replace('font-bold text-gray-700 uppercase', 'text-xs font-medium text-slate-500 uppercase tracking-wider')
    
    # Form elements specific: Make labels look premium
    content = content.replace('block text-sm font-bold text-gray-700', 'block text-sm font-medium text-slate-700 mb-1')
    content = content.replace('block text-sm font-medium text-slate-700', 'block text-sm font-medium text-slate-700 mb-1')
    
    # Strip some old classes if they result in duplication
    content = content.replace('mb-1 mb-1', 'mb-1')

    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Refactored SaaS styles in: {filepath}")

for root, _, files in os.walk(VIEWS_DIR):
    for file in files:
        if file.endswith('.blade.php'):
            process_file(os.path.join(root, file))

print("SaaS Rebuild script completed.")
