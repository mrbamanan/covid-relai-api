<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Notifications\NewPostNotification;
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
        return NotificationResource::collection($user->unReadNotifications);
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
