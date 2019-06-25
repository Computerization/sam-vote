<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoteGroup extends Model
{
    public function Vote()
    {
        return $this->hasMany('App\Vote');
    }
}
