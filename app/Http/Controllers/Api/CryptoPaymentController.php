<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CryptoGatewayService;
use App\Models\HistoryTransaction;

class CryptoPaymentController extends Controller
{
    public function __construct()
    {
        $this->CryptoGateway = new CryptoGatewayService;
    }
    public function CreateBill()
    {
        $process = $this->CryptoGateway->Create('5');
        $data = HistoryTransaction::where('id', 1)->with('crypto_payment')->first();
        return response()->json([
            'code' => 200,
            'message' => 'Bill Created',
            'data' => $process
        ],200);
    }
    public function CheckBill(Request $request)
    {
        dd($request->txid);
        $process = $this->CryptoGateway->Check($request->txid,'checkbill');
        return response()->json([
            'code' => 200,
            'message' => 'Bill Checked',
            'data' => $process
        ],200);
    }
}
            