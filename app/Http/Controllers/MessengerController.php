<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessengerController extends Controller
{
    public function index($id=null)
    {
        $user=Auth::user();
        $friendes=User::where("id","<>",$user->id)
        ->orderBy("name")->get();
        $chats= $user->conversations()
        ->with(["last_message",
        "participants"=>function($builder)use ($user){
            $builder->where("id","<>",$user->id);
        }])
        ->get();
        
        if($id){
            $messages=$chats->where("id",$id)->first();
        }
        if(!isset($messages)){
            $messages=$chats->first();
        }
        $current1=($messages);
        if(!$current1)$current1=new Conversation();
        else{
            $current1=$current1->first();
        }
        $messages=$messages===null?[]:$messages->messages()->with("user")->get();;
        // return $current1->id;

        return view("messenger",[
            "friendes"=>$friendes,
            "chats"=>$chats,
            "messages"=>$messages,
            "current1"=>$current1->id,
        ]);
    }
}
