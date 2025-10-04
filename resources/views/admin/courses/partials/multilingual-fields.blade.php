{{-- Multilingual Fields Partial --}}
{{-- Usage: @include('admin.courses.partials.multilingual-fields', ['fieldName' => 'name', 'label' => 'Course Title', 'type' => 'input', 'required' => true]) --}}

@php
    $fieldName = $fieldName ?? 'field';
    $label = $label ?? 'Field';
    $type = $type ?? 'input';
    $required = $required ?? false;
    $rows = $rows ?? 4;
    $placeholder = $placeholder ?? '';
    $value = $value ?? '';
    $valueAr = $valueAr ?? '';
@endphp

<div class="multilingual-field mb-3">
    <label class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-danger">*</span>
        @endif
    </label>

    <!-- Language Tabs -->
    <ul class="nav nav-tabs" id="{{ $fieldName }}Tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="{{ $fieldName }}-en-tab" data-bs-toggle="tab"
                data-bs-target="#{{ $fieldName }}-en" type="button" role="tab">
                <i class="fa fa-flag-usa me-1"></i>English
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="{{ $fieldName }}-ar-tab" data-bs-toggle="tab"
                data-bs-target="#{{ $fieldName }}-ar" type="button" role="tab">
                <i class="fa fa-flag me-1"></i>العربية
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="{{ $fieldName }}TabContent">
        <!-- English Tab -->
        <div class="tab-pane fade show active" id="{{ $fieldName }}-en" role="tabpanel">
            @if ($type === 'textarea')
                <textarea class="form-control @error($fieldName) is-invalid @enderror" name="{{ $fieldName }}"
                    id="{{ $fieldName }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
                    @if ($required) required @endif>{{ old($fieldName, $value) }}</textarea>
            @elseif($type === 'select')
                <select class="form-select @error($fieldName) is-invalid @enderror" name="{{ $fieldName }}"
                    id="{{ $fieldName }}" @if ($required) required @endif>
                    <option value="">Select {{ $label }}</option>
                    @if (isset($options))
                        @foreach ($options as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}"
                                {{ old($fieldName, $value) == $optionValue ? 'selected' : '' }}>
                                {{ $optionLabel }}
                            </option>
                        @endforeach
                    @endif
                </select>
            @else
                <input type="{{ $type }}" class="form-control @error($fieldName) is-invalid @enderror"
                    name="{{ $fieldName }}" id="{{ $fieldName }}" value="{{ old($fieldName, $value) }}"
                    placeholder="{{ $placeholder }}" @if ($required) required @endif>
            @endif
            @error($fieldName)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Arabic Tab -->
        <div class="tab-pane fade" id="{{ $fieldName }}-ar" role="tabpanel">
            @if ($type === 'textarea')
                <textarea class="form-control @error($fieldName . '_ar') is-invalid @enderror" name="{{ $fieldName }}_ar"
                    id="{{ $fieldName }}_ar" rows="{{ $rows }}" placeholder="{{ $placeholder }}" dir="rtl">{{ old($fieldName . '_ar', $valueAr) }}</textarea>
            @elseif($type === 'select')
                <select class="form-select @error($fieldName . '_ar') is-invalid @enderror"
                    name="{{ $fieldName }}_ar" id="{{ $fieldName }}_ar" dir="rtl">
                    <option value="">اختر {{ $label }}</option>
                    @if (isset($optionsAr))
                        @foreach ($optionsAr as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}"
                                {{ old($fieldName . '_ar', $valueAr) == $optionValue ? 'selected' : '' }}>
                                {{ $optionLabel }}
                            </option>
                        @endforeach
                    @endif
                </select>
            @else
                <input type="{{ $type }}" class="form-control @error($fieldName . '_ar') is-invalid @enderror"
                    name="{{ $fieldName }}_ar" id="{{ $fieldName }}_ar"
                    value="{{ old($fieldName . '_ar', $valueAr) }}" placeholder="{{ $placeholder }}" dir="rtl">
            @endif
            @error($fieldName . '_ar')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<style>
    .multilingual-field .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
    }

    .multilingual-field .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
    }

    .multilingual-field .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    .multilingual-field .tab-content {
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        padding: 1rem;
    }

    .multilingual-field textarea[dir="rtl"] {
        text-align: right;
    }

    .multilingual-field input[dir="rtl"] {
        text-align: right;
    }
</style>
