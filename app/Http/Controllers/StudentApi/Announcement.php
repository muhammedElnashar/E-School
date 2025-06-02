<?php

namespace App\Http\Controllers\StudentApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use Illuminate\Http\Request;

class Announcement extends Controller
{
    public function index()
    {
        $announcements = \App\Models\Announcement::all();
        return response()->json([
            'status' => 'success',
            'data' => AnnouncementResource::collection($announcements)
        ]);
    }
}
