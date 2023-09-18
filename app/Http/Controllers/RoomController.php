<?php

namespace App\Http\Controllers;

use App\Models\Room;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response(['rooms' => Room::all()]);
    }
}
