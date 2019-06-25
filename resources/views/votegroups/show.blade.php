@extends('layouts.semantic')

@section('title'){{ $vote_group -> group_name }} - @endsection

@section('content')

    {{-- Successful Actions --}}
    @if (session('status'))
        <div class="ui text container blue message">
            <div class="header">
                <p>
                    <i class="info icon"></i>
                    {{ session('status') }}
                </p>
            </div>
        </div>
    @endif

    <div class="ui raised padded text container segment">
        <div class="ui center aligned padded basic segment">
            <h1 class="ui header">
                {{ $vote_group -> group_name }}
            </h1>
        </div>
        <hr />
        <div class="ui large very relaxed divided list">
            @foreach($vote_group->vote as $vote)
                <div class="item">
                    <i class="terminal icon"></i>
                    <div class="content">
                        <a href="{{ url('vote',$vote->id) }}">
                            {{ $vote->vote_name }}
                        </a>
                        <small class="description">
                            Created at {{ $vote->created_at }}
                        </small>
                    </div>
                </div>
            @endforeach
        </div>


    </div>

@endsection
