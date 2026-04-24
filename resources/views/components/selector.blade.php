@props([
    'equipos' => collect(),
])

<flux:input
    {{ $attributes }}
    list="equipos-list"
    placeholder="Escribe nombre de equipo..."
/>
<datalist id="equipos-list">
    @foreach ($equipos as $equipo)
        <option value="{{ $equipo->nombre }}"></option>
    @endforeach
</datalist>