<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Board;

class BoardController extends Controller
{
    public function boardAll()
    {
        $board = Board::all();

        if ( !$board ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$board], 200);
        }

    }
}
