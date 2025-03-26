@extends('layouts.master')

@section('content')
    <h1>{{ __('Imported Data') }}</h1>
    @if(is_string($data))
        <p>{{ $data }}</p>
    @else
{{--        <table>--}}
{{--            <thead>--}}
{{--                <tr>--}}
{{--                    @foreach(array_keys($data[0]) as $header)--}}
{{--                        <th>{{ $header }}</th>--}}
{{--                    @endforeach--}}
{{--                </tr>--}}
{{--            </thead>--}}
{{--            <tbody>--}}
                @foreach($data as $row)
{{--                    <tr>--}}
                    <p>{{ $row['project_title']  }}</p>
{{--                        @foreach($row as $cell)--}}
{{--                            <p>{{ $cell }}</p>--}}
{{--                        @endforeach--}}
{{--                    </tr>--}}
                @endforeach
{{--            </tbody>--}}
{{--        </table>--}}
    @endif
@endsection
