@if (session('status'))
    <x-adminlte-alert theme="success" icon="bi bi-check-circle" dismissable>
        {{ session('status') }}
    </x-adminlte-alert>
@endif

@if ($errors->any())
    <x-adminlte-alert theme="danger" icon="bi bi-exclamation-triangle" dismissable>
        {{ $errors->first() }}
    </x-adminlte-alert>
@endif
