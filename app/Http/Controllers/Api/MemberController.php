<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserRekening;

class MemberController extends Controller
{
    public function NorekUser()
    {
        $user = auth()->user();
        $reklist = UserRekening::where('user_account', $user->account_id)->select('code','account_number')->get();
        return response()->json([
            'status' => true,
            'code'=>200,
            'message' => 'Data Norek User',
            'data' => $reklist,
        ],200);
    }
}
