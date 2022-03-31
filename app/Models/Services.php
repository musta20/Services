<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Services extends Model
{

    protected $guarded = [];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');

    }

    public function Servicestable()
    {
        return $this->morphTo();
    }

    use HasFactory;
}
