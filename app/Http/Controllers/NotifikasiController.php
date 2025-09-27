<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index()
    {
        return response()->json([
            'notifications' => auth()->user()->unreadNotifications,
            'count' => auth()->user()->unreadNotifications->count(),
        ]);
    }

    public function markAsRead(Request $request)
    {
        auth()->user()->unreadNotifications
            ->where('id', $request->id)
            ->markAsRead();
            
        return response()->noContent();
    }
}