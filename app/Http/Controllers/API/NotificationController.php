<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function index(){
        $user = Auth::user();

        return response()->json($user->unReadNotifications);
    }

    public function markAsRead($notification){
        $notification->markAsRead();
    }

    public function show($notification){
        return response()->json($notification);
    }

    public function destroy($notification){
        $notification->delete();
    }
}
