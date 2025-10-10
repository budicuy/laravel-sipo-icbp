@props([
    'name',
    'type' => 'text',
    'label' => null,
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'error' => null,
    'help' => null,
    'icon' => null,
    'size' => 'md',
    'step' => null,
    'min' => null,
    'max' => null,
    'options' => null, // For select type
    'multiple' => false, // For select type
    'rows' => 3 // For textarea
])

@php
    $sizeClasses = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-base'
    ];

    $labelSizes = [
        'sm' => 'text-xs',
        'md' => 'text-sm',
        'lg' => 'text-base'
    ];

    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $labelSize = $labelSizes[$size] ?? $labelSizes['md'];

    $hasError = $error || ($errors && $errors->has($name));
    $errorClass = $hasError ? 'border-red-500 focus:ring-red-500 bg-red-50' : 'border-gray-300 focus:ring-green-500 focus:border-green-500';
    $disabledClass = $disabled ? 'bg-gray-100 cursor-not-allowed' : 'bg-white';
    $iconPadding = $icon ? 'pl-10' : '';
    $rightIconPadding = $type === 'date' || $type === 'select' ? 'pr-10' : '';

    $inputClasses = "{$iconPadding} {$rightIconPadding} {$sizeClass} w-full border rounded-lg focus:outline-none focus:ring-2 transition-all {$errorClass} {$disabledClass}";
@endphp

<div class="w-full">
    <!-- Label -->
    @if($label)
        <label for="{{ $name ?? uniqid('input-') }}" class="block {{ $labelSize }} font-semibold text-gray-700 mb-2">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <!-- Input wrapper -->
    <div class="relative">
        <!-- Left icon -->
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="w-5 h-5 text-gray-400">{!! $icon !!}</span>
            </div>
        @endif

        <!-- Text/Email/Password/Number/Date inputs -->
        @if(in_array($type, ['text', 'email', 'password', 'number', 'date', 'tel', 'url']))
            <input
                type="{{ $type }}"
                id="{{ $name ?? uniqid('input-') }}"
                name="{{ $name }}"
                value="{{ old($name, $value) }}"
                placeholder="{{ $placeholder }}"
                class="{{ $inputClasses }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $step ? "step=\"{$step}\"" : '' }}
                {{ $min ? "min=\"{$min}\"" : '' }}
                {{ $max ? "max=\"{$max}\"" : '' }}
                {{ $hasError ? 'aria-invalid="true"' : '' }}>
        @endif

        <!-- Textarea -->
        @if($type === 'textarea')
            <textarea
                id="{{ $name ?? uniqid('input-') }}"
                name="{{ $name }}"
                placeholder="{{ $placeholder }}"
                rows="{{ $rows }}"
                class="{{ $inputClasses }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $readonly ? 'readonly' : '' }}
                {{ $hasError ? 'aria-invalid="true"' : '' }}>{{ old($name, $value) }}</textarea>
        @endif

        <!-- Select -->
        @if($type === 'select')
            <select
                id="{{ $name ?? uniqid('input-') }}"
                name="{{ $name }}{{ $multiple ? '[]' : '' }}"
                class="{{ $inputClasses }} appearance-none"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $multiple ? 'multiple' : '' }}
                {{ $hasError ? 'aria-invalid="true"' : '' }}>

                @if(!$multiple)
                    <option value="">{{ $placeholder ?? '-- Pilih --' }}</option>
                @endif

                @if($options)
                    @foreach($options as $key => $option)
                        @if(is_array($option))
                            <optgroup label="{{ $key }}">
                                @foreach($option as $optValue => $optLabel)
                                    <option value="{{ $optValue }}" {{ old($name, $value) == $optValue ? 'selected' : '' }}>
                                        {{ $optLabel }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @else
                            <option value="{{ $key }}" {{ old($name, $value) == $key ? 'selected' : '' }}>
                                {{ $option }}
                            </option>
                        @endif
                    @endforeach
                @endif

                {{ $slot }}
            </select>

            <!-- Select dropdown arrow -->
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        @endif

        <!-- Checkbox -->
        @if($type === 'checkbox')
            <div class="flex items-center">
                <input
                    type="checkbox"
                    id="{{ $name ?? uniqid('input-') }}"
                    name="{{ $name }}"
                    value="{{ $value ?? '1' }}"
                    class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                    {{ old($name) ? 'checked' : '' }}
                    {{ $disabled ? 'disabled' : '' }}
                    {{ $hasError ? 'aria-invalid="true"' : '' }}>
                <label for="{{ $name ?? uniqid('input-') }}" class="ml-2 text-sm text-gray-700">
                    {{ $label }}
                    @if($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            </div>
        @endif

        <!-- Radio -->
        @if($type === 'radio')
            <div class="flex items-center">
                <input
                    type="radio"
                    id="{{ $name ?? uniqid('input-') }}"
                    name="{{ $name }}"
                    value="{{ $value }}"
                    class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500"
                    {{ old($name) == $value ? 'checked' : '' }}
                    {{ $disabled ? 'disabled' : '' }}
                    {{ $hasError ? 'aria-invalid="true"' : '' }}>
                <label for="{{ $name ?? uniqid('input-') }}" class="ml-2 text-sm text-gray-700">
                    {{ $label }}
                    @if($required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>
            </div>
        @endif

        <!-- File -->
        @if($type === 'file')
            <input
                type="file"
                id="{{ $name ?? uniqid('input-') }}"
                name="{{ $name }}"
                class="{{ $inputClasses }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $multiple ? 'multiple' : '' }}
                {{ $hasError ? 'aria-invalid="true"' : '' }}>
        @endif
    </div>

    <!-- Help text -->
    @if($help && !$hasError)
        <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
    @endif

    <!-- Error message -->
    @if($hasError)
        <p class="mt-1 text-xs text-red-600 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $error ?? ($errors ? $errors->first($name) : '') }}
        </p>
    @endif
</div>
