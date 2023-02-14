<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ChatMessage;
use App\Models\Demo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {
        $users = User::where('id', '!=', Auth::user()->id)->paginate(10);
        return view('home', compact('users'));
    }

    // public function renderConversationList($getUserSendToId, $sendToAuthID)
    // {

    //     $conversationList = ChatMessage::whereIn('to_user_id', [$sendToAuthID, $getUserSendToId])
    //         ->whereIn('from_user_id', [$sendToAuthID, $getUserSendToId])
    //         ->get();

    //     return response()->json($conversationList);
    // }
    public function renderConversationList(Request $request)
    {
        $conversationList = ChatMessage::whereIn('to_user_id', [$request->get('sendToAuthID'),  $request->get('getUserSendToId')])
            ->whereIn('from_user_id', [$request->get('getUserSendToId'), $request->get('sendToAuthID')])
            ->get();
        $view = view("conversationList", compact('conversationList'))->render();
        return response()->json(['html' => $view]);
    }



    public function sendMessage(Request $request)
    {
        try {
            $getSentToID = $request->get('getUserSendToId');
            $message = $request->get('message');
            ChatMessage::create([
                'to_user_id'    => $getSentToID,
                'from_user_id'  => Auth::user()->id,
                'chat_message'  => $message,
            ]);
            return response()->json($message);
        } catch (\Exception $e) {
            return response()->json([
                'status'    => 'error',
                'message'   => $e->getMessage()
            ]);
        }
    }
}
