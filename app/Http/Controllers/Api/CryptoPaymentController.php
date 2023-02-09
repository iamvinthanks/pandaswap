<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CryptoGatewayService;
use App\Models\HistoryTransaction;
use GuzzleHttp\Client;
use App\Services\BSCService;

class CryptoPaymentController extends Controller
{
    public function __construct()
    {
        $this->CryptoGateway = new CryptoGatewayService;
        $this->BSC = new BSCService;
    }
    public function CreateBill(Request $request)
    {
        $process = $this->CryptoGateway->Create($request->amount);
        $data = HistoryTransaction::where('id', $process)->with('crypto_payment')->first();
        return response()->json([
            'code' => 200,
            'message' => 'Bill Created',
            'data' => $data
        ], 200);
    }
    public function CheckBill(Request $request)
    {
        // $resp = $this->CryptoGateway->Check();
        // $resp = $this->BSC->wallet();
        $resp = $this->BSC->interface();
        return response()->json([
            'code' => 200,
            'message' => 'Bill Created',
            'data' => $resp
        ], 200);
    }
}
