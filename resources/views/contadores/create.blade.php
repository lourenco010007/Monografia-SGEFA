@extends('layouts.admin')

@section('title', 'Registar contador')

@section('content')
    <h1 class="text-2xl font-bold">Registar contador</h1>
    @include('contadores._form', [
        'action' => route('contadores.store'),
        'method' => 'POST',
    ])
@endsection
