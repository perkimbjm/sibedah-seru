@props([
    'name',
    'label',
    'options',
    'errors' => null,
    'uppercase' => false
])

<div class="form-group">
    <label class="required" for="{{ $name }}">{{ $label }}</label>
    <select class="form-control {{ $errors && $errors->has($name) ? 'is-invalid' : '' }}" name="{{ $name }}" id="{{ $name }}" required>
        <option value="">-- Pilih --</option>
        @foreach($options as $option)
            <option value="{{ $uppercase ? strtoupper($option) : $option }}" {{ old($name) == $option ? 'selected' : '' }}>
                {{ $option }}
            </option>
        @endforeach
    </select>
    @if($errors && $errors->has($name))
        <span class="text-danger">{{ $errors->first($name) }}</span>
    @endif
</div>
