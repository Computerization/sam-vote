@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
          <h1>Votes</h1>

          @if (session('status'))
          <hr>
              <div class="alert alert-success">
                  {{ session('status') }}
              </div>
          @endif
          <hr>
            @foreach($criterias as $criteria)
            <div class="card">
              <div class="card-body">
                <h4><a href="{{ url('vote', $criteria->id) }}">{{ $criteria->criteria }} </a></h4>
                <div> {{ $criteria->created_at }}
                </div>
              </div>
                <div class="card-footer">
                  <form action="{{ URL::action('VoteController@addvote', $vote_id) }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="criteria_id" value="{{ $criteria->id }}">
                    <button class="btn btn-primary" type="submit">Add to Vote Group</button>
                  </form>
                </div>

            </div>
            <br>
            @endforeach
        </div>
    </div>
</div>
@endsection
