@extends('layouts.admin')

@section('title', 'Editar utente')

@section('content')
    <h1 class="text-2xl font-bold">Editar utente</h1>
    @include('utentes._form', [
        'action' => route('utentes.update', $utente),
        'method' => 'PUT',
    ])
@endsection
