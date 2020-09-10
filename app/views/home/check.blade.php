@extends('layouts.layout')

@push('css')
@endpush

{{----------------------------------------------------}}

@push('js')
@endpush

{{----------------------------------------------------}}

@section('content')
    {{-- blade comment --}}

    <!-- html comment -->

    <form action="" method="post">
        <textarea name="asghar" cols="30" rows="10">@foreach($input as $url){{$url}}{!! "\n" !!}@endforeach</textarea><br>
        <button type="submit">Submit</button>
    </form>

    @if($akbar)
    <div>
        <ul>
            @foreach($akbar as $url => $http_response)
                <li><b>{{$url}} : </b>{{$http_response}}</li>
            @endforeach
        </ul>
    </div>
    @endif

@endsection
