@extends('layouts.layout')

@push('css')
@endpush

{{----------------------------------------------------}}

@push('js')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script>

        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('46c790f03cdc593b6f3f', {
            cluster: 'ap1'
        });

        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function (data) {
            console.log(data);

            $('#results').append("<li>" + data + "</li>");
        });
    </script>
@endpush

{{----------------------------------------------------}}

@section('content')
    {{-- blade comment --}}

    <!-- html comment -->

    <form action="" method="post">
        <textarea name="asghar" cols="30" rows="10">@foreach($input as $url){{$url}}{!! "\n" !!}@endforeach</textarea><br>
        <button type="submit">Submit</button>
    </form>

    <ul id="results"></ul>

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
