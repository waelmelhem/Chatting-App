<?php

namespace App\Http\Controllers;

use Error;
use Throwable;
use App\Models\User;
use App\Models\Recipient;
use App\Models\Participant;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Events\MessageCreate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $user=User::find(1);
        // Auth::user();
        $conv=$user->conversations()->findOrFail($id);
        return $conv->message()->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $request->validate([
            "message"=>["required","string"],
            "conversation_id"=>[
                Rule::requiredIf(function() use($request){
                    return !$request->input("user_id");
                }),
                "int",
                "exists:conversations,id"],
            "user_id"=>[
                Rule::requiredIf(function() use($request){
                    return !$request->input("conversation_id");
                }),
                "int",
                "exists:users,id"
            ],
        ]);
        
        $conversation_id=$request->post("conversation_id");
        $user_id=$request->post("user_id");
        $user=Auth::user();
        
        Db::beginTransaction();
        try{
            if($conversation_id){
                $conversation=$user->conversations()->findOrFail($conversation_id);
            }
            else{
                $conversation= Conversation::where("type","peer")
                    ->whereHas("participants",function(Builder $query) use($user,$user_id){
                        $query->join("participants as participants2","participants2.conversation_id","=","participants.conversation_id")
                        ->where("participants2.user_id","=",$user_id)
                        ->where("participants.user_id","=",$user->id);
                })->first();
                if(!$conversation){
                    $conversation = Conversation::create([
                        "user_id"=>$user->id
                    ]);
                    $conversation->participants()->attach([
                        $user_id=>["joined_at"=>now()],
                        $user->id=>["joined_at"=>now()]
                    ]);
                }
            }
            
            // return 1;
            $message=$conversation->messages()->create([
                "user_id"=>$user->id,
                "body"=>$request->post("message"),
            ]);
            
            DB::statement(
                "
                insert into recipients (user_id,message_id)
                select user_id,? from participants
                where conversation_id = ?
                ",[$message->id,$conversation->id]
            );
            Db::commit();
            $conversation->update([
                "last_message_id"=>$message->id,
            ]);
            broadcast(new MessageCreate($message));
            return $message;
        }catch(Throwable $e){
            DB::rollBack();
            return $e;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $message=Auth::user()->conversations()->messages()->findOrFail($id);
        $res=Recipient::where([
            "user_id"=>Auth::user()->id,
            "message_id"=>$id,
        ])->delete();
        return $res;
    }
}
