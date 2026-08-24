@props(['label', 'name', 'options' => [], 'required' => false])

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }} {!! $required ? '<span class="text-red-500">*</span>' : '' !!}
    </label>
    <select name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        class="border border-gray-300 rounded-lg w-full py-2 px-3 focus:ring-2 focus:ring-blue-500 outline-none bg-white">

        <option value="">اختر...</option>

        @foreach($options as $key => $option)
        @php
        // هذه الأسطر تجعل المكون ذكياً للتعامل مع المصفوفات العادية أو البيانات القادمة من قاعدة البيانات
        $val = is_object($option) ? $option->id : (is_numeric($key) ? $option : $key);
        $display = is_object($option) ? $option->name : $option;
        @endphp
        <option value="{{ $val }}" {{ old($name) == $val ? 'selected' : '' }}>
            {{ $display }}
        </option>
        @endforeach

    </select>
</div>