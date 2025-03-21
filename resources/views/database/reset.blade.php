@extends('layouts.master')

@section('content')
    @if(session('message'))
        <p>{{ session('message') }}</p>
    @endif
    <h1>{{ __('Rénitialiser les données') }}</h1>
    <button><a href="{{ route('database.reset') }}">Reset</a></button>
@endsection
