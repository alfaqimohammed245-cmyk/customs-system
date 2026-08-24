@props(['label', 'name', 'type' => 'text', 'required' => false])

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }} {!! $required ? '<span class="text-red-500">*</span>' : '' !!}
    </label>
    <input type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name) }}"
        {{ $required ? 'required' : '' }}
        class="border border-gray-300 rounded-lg w-full py-2 px-3 focus:ring-2 focus:ring-blue-500 outline-none">
</div>