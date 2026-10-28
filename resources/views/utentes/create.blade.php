@extends('layouts.admin')

@section('title', 'Registar utente')

@section('content')
    <h1 class="text-2xl font-bold">Registar utente</h1>
    @include('utentes._form', [
        'action' => route('utentes.store'),
        'method' => 'POST',
    ])
@endsection
