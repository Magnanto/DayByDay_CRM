@extends('layouts.master')

@section('content')
    <h1>{{ __('Import CSV') }}</h1>
    <form action="{{ route('database.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label>Fichier Project CSV</label>
        <br>
        <input type="file" name="projectsFile" required>
        <br><br>
        <label>Fichier Tasks CSV</label>
        <br>
        <input type="file" name="tasksFile" required>
        <br><br>
        <label>Fichier Leads CSV</label>
        <br>
        <input type="file" name="leadsFile" required>
        <br>
        <button type="submit">Importer</button>
    </form>
    @if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    @if(session('erreurs'))
        <div class="alert alert-danger">
            <ul>
                @foreach (session('erreurs') as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection