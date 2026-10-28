@extends('layouts.admin')

@section('title', 'Editar contador')

@section('content')
    <h1 class="text-2xl font-bold">Editar contador</h1>
    @include('contadores._form', [
        'action' => route('contadores.update', $contador),
        'method' => 'PUT',
    ])
@endsection
