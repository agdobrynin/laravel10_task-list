@props([
    'title',
    'name',
    'options',
    'value',
    'id' => \Illuminate\Support\Str::uuid(),
])
<label for="{{ $id }}">{{ $title }}</label>
<select name="{{ $name }}" id="{{ $id }}" @class(['border-red-500' => $errors->has($name)])>
    @foreach ($options as $val => $text)
        <option value="{{ $val }}" @if ($val === $value) selected @endif>{{ $text }}</option>    
    @endforeach
</select>
@error($name)
    <div class="error">{{ $message }}</div>
@enderror

