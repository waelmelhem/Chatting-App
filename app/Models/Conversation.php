<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable=[
        "user_id","label","last_message_id"
    ];
    public function participants(){
        return $this->belongsToMany(User::class,"participants")
        ->withPivot([
            "joined_at","role"
        ]);
    };
    public function messages(){
        return $this->hasMany(Messages::class)
        ->latest();
    };
    public function user(){
        return $this->belongsTo(User::class);
    };
    public function  last_message(){
        return $this->hasOne(Message::class,"last_message_id","id")
        ->withDefault("no sented message yet");
    }
     
}
