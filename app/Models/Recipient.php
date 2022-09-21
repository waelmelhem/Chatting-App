<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recipient extends Pivot
{
    use HasFactory,SoftDeletes;
    public $timestamp=false;
    protected $casts=[
    "read_at"=>"datetime"
    ];
    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
