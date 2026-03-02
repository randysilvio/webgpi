@props(['name', 'label' => null, 'type' => 'text', 'value' => '', 'required' => false, 'placeholder' => ''])

<div class="w-full">
    {{-- Label hanya muncul jika diisi --}}
    @if($label)
        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
            {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        {{ $required ? 'required' : '' }}
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'w-full border-slate-300 rounded text-sm focus:ring-slate-500 focus:border-slate-500 placeholder-slate-400 text-slate-700 transition shadow-sm']) }}>

    @error($name) <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
</div>