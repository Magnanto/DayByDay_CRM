@extends('layouts.master')

@section('content')
    @if(session('message'))
        <p>{{ session('message') }}</p>
    @endif
    <h1>{{ __('Import CSV') }}</h1>
    <form action="{{ route('database.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label>Fichier CSV à importer</label>
        <input type="file" name="file" required>
        <br>
        <label>Nom de la table</label>
        <input type="text" name="table_name" placeholder="Table Name" required>
        <br>
        <button type="submit">Importer</button>
    </form>

    <h2>{{ __('Database Tables') }}</h2>
    <ul>
        @foreach($tables as $table)
            <li>{{ $table }}</li>
        @endforeach
    </ul>
@endsection
