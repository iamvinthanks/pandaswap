<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Escrow;

class EscrowController extends Controller
{
    public function createescrow(Request $request)
    {
        $data = [
            'escrow_id'=>'escrow_'.time(),
            'seller'=>auth()->user()->account_id,
            'amount'=>$request->amount,
            'rek_seller'=>$request->rek_seller,
            'id_rek_seller'=>$request->id_rek_seller,
        ];
        $createtranscation = Escrow::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'Transcation created',
            'data'=>$data['escrow_id']
        ], 200);
    }
    public function joinescrow($escrow_id)
    {
        $account_id = auth()->user()->account_id;
        
    }
    public function getEscrowdetail($escrow_id)
    {
        $escrow = Escrow::where('escrow_id',$escrow_id)->first();
        return response()->json([
            'status' => 'success',
            'code' =>200,
            'message' => 'Success get escrow detail',
            'data' => $escrow
        ], 200);
    }
    public function approveescrowseller($escrow_id , Request $request)
    {
        $account_id = auth()->user()->account_id;
        $escrow = Escrow::where('escrow_id',$escrow_id)->first();
        if($escrow->seller == $account_id){
            $escrow->seller_approve = 1;
            $escrow->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Success approve escrow',
            ], 200);
        }else{
            return response()->json([
                'status' => 'invalid',
                'message' => 'You are not seller',
            ], 200);
        }
        if($escrow->seller_approve == 1 && $escrow->buyer_approve == 1){
            $escrow->status = "completed";
            $escrow->save();
        }
    }
    public function approveescrowbuyer($escrow_id , Request $request)
    {
        $account_id = auth()->user()->account_id;
        $escrow = Escrow::where('escrow_id',$escrow_id)->first();
        if($escrow->buyer == $account_id){
            $escrow->buyer_approve = 1;
            $escrow->save();
            if($escrow->seller_approve == 1 && $escrow->buyer_approve == 1){
                $escrow->status = "completed";
                $escrow->save();
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Success approve escrow',
            ], 200);
        }else{
            return response()->json([
                'status' => 'invalid',
                'message' => 'You are not buyer',
            ], 200);
        }
        if($escrow->seller_approve == 1 && $escrow->buyer_approve == 1){
            $escrow->status = "completed";
            $escrow->save();
        }
    }
}
