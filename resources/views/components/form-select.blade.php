@props(['name', 'label' => null, 'required' => false])

<div class="w-full">
    @if($label)
    <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
        {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
    </label>
    @endif
    
    <select name="{{ $name }}" {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 text-slate-700 shadow-sm']) }}>
        {{ $slot }}
    </select>

    @error($name) <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>