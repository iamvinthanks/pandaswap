<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Carbon\Carbon;
use IEXBase\TronAPI\Tron;

class PaymentController extends Controller
{

    public function validatebank(Request $request)
    {
        $client = new Client();
        $response = $client->request('POST', 'https://api-stg.oyindonesia.com/api/account-inquiry', [
            'headers' => [
                'x-oy-username' => 'alvinnasa',
                'x-api-key' => '9bc75d6b-fe31-46d6-a9c3-dc4bd29f47cc',
                'content-type' => 'application/json'
            ],
            'body' => json_encode([
                'bank_code' => $request->bank_code,
                'account_number' => $request->account_number,
            ]),
        ]);
        $data = json_decode($response->getBody()->getContents());
        return response()->json($data);
    }
    public function sendmoney(Request $request)
    {
        $txid = 'TX-'.Carbon::now()->format('YmdHis');
        
        $client = new Client();
        $response = $client->request('POST','https://api-stg.oyindonesia.com/api/remit',[
            'headers' => [
                'x-oy-username' => 'alvinnasa',
                'x-api-key' => '9bc75d6b-fe31-46d6-a9c3-dc4bd29f47cc',
                'content-type' => 'application/json',
                'accept'=>'application/json',
            ],
            'body'=> json_encode([
                'recipient_bank' => $request->recipient_bank,
                'recipient_account' => $request->recipient_account,
                'amount' => $request->amount,
                'partner_trx_id'=>$txid,
            ]),
        ]);
        $data = json_decode($response->getBody()->getContents());
        return response()->json($data);
    }
    public function getblock(Request $request)
    {
        $client = new Client();
        $response = $client->request('POST','https://api.trongrid.io/wallet/gettransactionbyid',[
            'headers' => [
                'accept'=>'application/json',
                'TRON-PRO-API-KEY'=>'26f79c1a-65f4-4f48-82cb-ed6935f15146',
            ],
            'body'=> json_encode([
                'value' => 'dffb97e6021955b4e6da84516b1176e5fc164b69bcbee960e33f082237a9a404',
            ]),
        
        ]);
        $data = json_decode($response->getBody()->getContents());
        $resp = $data->raw_data->contract[0]->parameter->value;
        $value = ($resp->amount/1000000 );
        return response()->json($value);
    }
    public function createpayment(request $request)
    {
        $txid = 'TX-'.Carbon::now()->format('YmdHis');
        $client = new Client();
        if($request->payment_method == "va"){
            $res = $client->request('POST','https://api-stg.oyindonesia.com/api/generate-static-va',[
                'headers'=> [
                        'x-oy-username' => 'alvinnasa',
                        'x-api-key' => '9bc75d6b-fe31-46d6-a9c3-dc4bd29f47cc',
                        'content-type' => 'application/json',
                        'accept'=>'application/json',
                ],
                'body'=>json_encode([
                        'partner_user_id'=> '1231233',
                        'bank_code'=> '002',
                        'amount'=> $request->amount,
                        'is_open'=> false,
                        'is_single_use' => true,
                        'is_lifetime'=> false,
                        'expiration_time'=> 60,
                        'username_display'=> 'Pandaswap Indonesia',
                        'trx_expiration_time'=> 5,
                        'partner_trx_id'=> $txid,
                        'trx_counter' => 1
                ]),
            ]);
            $data = json_decode($res->getBody()->getContents());
            return response()->json($data);
            
        }
        if($request->payment_method == "qris")
        {
            $res = $client->request('POST','https://api-stg.oyindonesia.com/api/payment-routing/create-transaction',[
                'headers'=>[
                    'x-oy-username' => 'alvinnasa',
                    'x-api-key' => '9bc75d6b-fe31-46d6-a9c3-dc4bd29f47cc',
                    'content-type' => 'application/json',
                    'accept'=>'application/json',
                ],
                'body'=>json_encode([
                    'partner_user_id'=> 'USR-20211117-1029',
                    'partner_trx_id'=> 'TRX-20211117-1030',
                    'need_frontend'=> false,
                    'sender_email'=> 'sender@gmail.com',
                    'receive_amount'=> 14000,
                    'list_enable_payment_method'=> 'VA',
                    'list_enable_sof'=> '002',
                    'va_display_name'=> 'partner_brand',
                    'payment_routing'=> json_encode([
                        'recipient_bank'=> '014',
                        'recipient_account'=> '1234567890',
                        'recipient_amount'=> 10000,
                        'recipient_email'=> 'recipient_bca@gmail.com'
                    ])
                ])
            ]);
        }
    }
    public function tronPayment()
    {

        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        
        try {
            $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            exit($e->getMessage());
        }
        
        
       
        $data = $tron->createAccount();
        var_dump($data);
        
        
    }
}
