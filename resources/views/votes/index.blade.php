@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
          <h1>Votes</h1>

          @if(isset($group_id))
          <hr>
            <a class="btn btn-default" href="{{ URL::action('VoteGroupController@show',$group_id) }}">Back</a>
          @endif

          @if (session('status'))
          <hr>
              <div class="alert alert-success">
                  {{ session('status') }}
              </div>
          @endif
          <hr>
            @foreach($votes as $vote)
            <div class="card">
              <div class="card-body">
                <h4><a href="{{ url('vote',$vote->id) }}">{{ $vote->vote_name }} </a></h4>
                <div> {{ $vote->created_at }}
                @if(Auth::user()->group > 0)
                   - <a class="" href="{{ URL::action('VoteController@stat',$vote->id) }}">Stat and Control</a>
                @endif
                </div>
              </div>

              @if(Auth::user()->group > 0)
                @if(isset($group_id))
                @if($vote->vote_groups()->where('vote_groups.id',$group_id)->count()>0)
                <div class="card-footer">
                  <form action="{{ URL::action('VoteGroupController@rmvote',$group_id) }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="vote_id" value="{{ $vote->id }}">
                    <button class="btn btn-danger" type="submit">Remove from Vote Group</button>
                  </form>
                </div>
                @else
                <div class="card-footer">
                  <form action="{{ URL::action('VoteGroupController@addvote',$group_id) }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="vote_id" value="{{ $vote->id }}">
                    <button class="btn btn-primary" type="submit">Add to Vote Group</button>
                  </form>
                </div>
                @endif
                @endif
              @endif
            </div>
            <br>
            @endforeach
        </div>
    </div>
</div>
@endsection
