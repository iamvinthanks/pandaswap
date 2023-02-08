<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Services\GateValidate;

class BinanceController extends Controller
{
    public function __construct(){
        $this->BS = ENV('BINANCE_SECRET');
        $this->BK = ENV('BINANCE_KEY');
        $this->GateValidate = new GateValidate();
    }
    public function verifcode(Request $request)
    {

        $time = Carbon::now()->timestamp.'000';
        
        $secret = ENV('BINANCE_SECRET');
        $signature = hash_hmac('sha256','referenceNo='.$request->referenceNo.'&timestamp='.$time, $secret);
        $client = new Client();
        try{
        $response = $client->request('GET', 'https://api.binance.me/sapi/v1/giftcard/verify', [
            'query' => [
                'referenceNo' => $request->referenceNo,
                'timestamp' =>$time,
                'signature'=>$signature,
            ],
            'headers' => [
                'X-MBX-APIKEY' => ENV('BINANCE_KEY'),
            ],]);
        $data_code = json_decode($response->getBody()->getContents());
        $data = json_decode(json_encode($data_code));
        $total = $this->GateValidate->price($data->data->token,$data->data->amount);
        if($total['total_value'] < 20000 )
        {
            return response()->json([
                'status' => false,
                'code'=>201,
                'message' => 'Minimal Redeem Rp. 20.000,-',
            ],201);
        }
        if($data->code == '000000')
        {
            if($data->data->valid == true){
                return response()->json([
                    'status' => true,
                    'code'=>200,
                    'message' => 'Code atau No.Refrensi Valid !',
                    'data' => $total,
                ],200);
            }else{
                return response()->json([
                            'status' => false,
                            'code'=>201,
                            'message' => 'Code atau No.Refrensi Tidak Valid ! ',
                            'notes'=>'Silahkan Cek Kembali No.Refrensi atau Code yang anda masukan',
                        ],201);
            }
        }
        }catch(\Exception $e){
            return response()->json([
                'code'=>501,
                'status' => false,
                'message' => 'Request Timeout!',$e->getMessage(),
                'notes'=>'Silahkan Refresh Browser',

            ],501);
        }
    }

    public function redeemcode(Request $request)
    {
        $data = $request->all();
        $data = json_decode(json_encode($data));
        $time = Carbon::now()->timestamp.'000';
        $secret = $this->BS;
        $signature = hash_hmac('sha256','code='.$request->code.'&timestamp='.$time, $secret);
            $client = new Client();
            $response = $client->request('POST', 'https://api.binance.me/sapi/v1/giftcard/redeemCode', [
                'query' => [
                    'code' => $request->code,
                    'timestamp' =>$time,
                    'signature'=>$signature,
                ],
                'headers' => [
                    'X-MBX-APIKEY' => $this->BK,
                ],]);
            $datacode = json_decode($response->getBody()->getContents(), true);
            $data = json_decode(json_encode($datacode));
            
            if($data->success == true){
                $finaldata = $this->GateValidate->countandsend($data->data->token,$data->data->amount,$request->recipient_bank,$request->recipient_account);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'ADA YG ANEH NIHHHH!',
                ]);
            }            
            return $finaldata;
    }
    
    public function ipcheck(){
        $client = New Client();
        $response = $client->request('GET', 'https://api64.ipify.org');
        $data = json_decode($response->getBody()->getContents(), true);
        return response()->json([
            'status' => true,
            'message' => $data,
        ]);

    }
    
}
