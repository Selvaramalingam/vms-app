@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
