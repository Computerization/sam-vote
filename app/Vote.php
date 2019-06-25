<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    public function VoteCriteria()
    {
        return $this->belongsToMany('App\VoteCriteria');
    }

    public function Response()
    {
        return $this->hasMany('App\Response');
    }

    public function VoteGroup()
    {
        return $this->belongsTo('App\VoteGroup')
    }
}
