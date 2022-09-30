<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationsController extends Controller
{
    public function index(){
        $user =Auth::user();
        return $user->conversations()->with(["last_message",
        "participants"=>function($builder)use ($user){
            $builder->where("id","<>",$user->id);
        }])->paginate();
    }
    public function show(Conversation $conversation)
    {
        // return $conversation;
        return $conversation->participants()->get();
    }
    public function addParticipant(Request $req,Conversation $conversation){
        $req->validate([
            "user_id"=>["required","int","exists:users,id"],
        ]);
        $conversation->participants()->attach([$req->post("user_id")=>["joined_at"=>Carbon::now()]]);
    }
    public function removeParticipant(Request $req,Conversation $conversation){
        $req->validate([
            "user_id"=>["required","int","exists:users,id"],
        ]);
        $conversation->participants()->detach($req->post("user_id"));
    }
}
