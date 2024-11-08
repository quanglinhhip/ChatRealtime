<?php

namespace App\Http\Controllers;

use App\Events\UserOnlined;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function chat()
    {
        // echo "123";
        $user = User::where('id', '<>', Auth::user()->id)->get();
        return view('chat/chatpublic')->with([
            'users' => $user,
        ]);
    }

    public function sendMessage(Request $request)
    {
        broadcast(new UserOnlined(
            $request->user(),
            $request->message
        ));
        // return json_encode($request->message);
        return json_encode([
            'success' => 'done',
        ]);
    }
}
