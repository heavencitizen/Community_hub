@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-200 bg-white text-slate-800 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm']) }}>
