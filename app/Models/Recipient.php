<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Pivot
{
    use HasFactory,softDeletes;
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
