<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class message extends Model
{
    use HasFactory,softDeletes;
    protected $fillable=["	conversation_id",
    "user_id","body","type","deleted_at",
    "created_at","updated_at"
    ];

    public function user(){
        return $this->belongsTo(User::class)->withDefault([
            "name"=>__("user");
        ]);
    }
    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }
    public function recipients(){
        return $this->belongsToMany(User::class,"recipients")
        ->withPivot(["read_at","delated_at"]);
    }
}
