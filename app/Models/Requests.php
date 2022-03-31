<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requests extends Model
{
    protected $guarded = [];
    public function Service()
    {
        return $this->belongsTo(Services::class, 'Service_id');

    }
    public function combany()
    {
        return $this->belongsTo(User::class, 'combany_id');

    }


    
    use HasFactory;
}
