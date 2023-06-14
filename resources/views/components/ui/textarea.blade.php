@props([
    'title',
    'name',
    'value',
    'rows' => 5,
    'id' => \Illuminate\Support\Str::uuid(),
])
<label for="{{ $id }}">{{ $title }}</label>
<textarea name="{{ $name }}" id="{{ $id }}" rows="{{ $rows }}" @class(['border-red-500' => $errors->has($name)])>{{ $value }}</textarea>
@error($name)
    <div class="error">{{ $message }}</div>
@enderror
