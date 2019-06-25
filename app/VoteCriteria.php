<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoteCriteria extends Model
{
    public function Vote()
    {
        return $this->belongsToMany('App\Vote');
    }

    public function Response()
    {
        return $this->hasMany('App\Response');
    }
}
