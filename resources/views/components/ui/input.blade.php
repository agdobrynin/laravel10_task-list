@props([
    'title',
    'name',
    'value',
    'type' => 'text',
    'id' => \Illuminate\Support\Str::uuid(),
])
<label for="{{ $id }}">{{ $title }}</label>
<input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}" @class(['border-red-500' => $errors->has($name)])>
@error($name)
    <div class="error">{{ $message }}</div>
@enderror
