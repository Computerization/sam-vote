<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    public function Vote()
    {
        return $this->belongsTo('App\Vote')
    }

    public function VoteCriteria()
    {
        return $this->belongsTo('App\VoteCriteria')
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
