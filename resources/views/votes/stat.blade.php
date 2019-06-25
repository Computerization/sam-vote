@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <a class="btn btn-lg btn-default" href="{{ url('/vote') }}">Back</a>
            <h1>{{ $vote->vote_name }}</h1>
            {{-- <a href="{{ URL::action('VoteController@edit',$vote->id) }}" class="btn btn-default">Edit</a> --}}
            <a href="{{ URL::action('VoteController@export',$vote->id) }}" class="btn btn-default">Export</a>

            @if(!empty(session('status')))
            <hr>
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif

            <hr>
            @foreach($vote->votecriteria as $question)
            <div class="card">
              <div class="card-body">
                  <div class="field">
                      <label for="q{{ $question->id }}" style="font-size:1em;">
                          {{ $question->criteria }}
                      </label>
                  </div>

                <table class="table">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>User Name</th>
                      <th>Response</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($responses->where('vote_criteria_id', $question->id) as $answer)
                      <tr>
                        <th scope="row">{{ $answer->id }}</th>
                        <td>{{ $answer->user->name }}</td>
                        <td>{{ $answer->response }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>

              </div>
            </div>
            <br>
            @endforeach

        </div>
    </div>
</div>
@endsection
