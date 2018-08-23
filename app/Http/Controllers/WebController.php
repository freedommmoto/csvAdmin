<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessagePosted;
use Auth;
use App\Message;

class WebController extends Controller
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
     * handel messages save to base and push to vue
     *
     * @return status
     */
    public function saveMessages()
    {
        // Store the new message
        $user = Auth::user();

        $message = $user->messages()->create([
            'message' => request()->get('message')
        ]);

        // Announce that a new message has been posted
        broadcast(new MessagePosted($message, $user))->toOthers();

        return ['status' => $message];
    }

    /**
     * handel messages save to base and push to vue
     *
     * @return list of data
     */
    public function getMessages()
    {
        return Message::with('user')->get();
    }

    /**
     * handel messages save to base and push to vue
     *
     * @return status
     */
    public function csvUpload()
    {
        $csvData = request()->get('parse_csv');
        return $csvData;
    }


}