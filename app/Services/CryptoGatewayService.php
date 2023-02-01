<?php

namespace App\Services;
use IEXBase\TronAPI\Tron;
use App\Models\CryptoPayment;
use App\Models\HistoryTransaction;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Str;
use DB;



class CryptoGatewayService
{
    public function Create($amount,$action)
    {
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.trongrid.io');
        try {
            $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            exit($e->getMessage());
        }

        if($action == 'createbill'){

        $expired = Carbon::now()->addMinutes(30);
        $createAddrs = $tron->createAccount();
        $addressData = $createAddrs->response;
        $coin = 'TRX';
        // DB::beginTransaction();
        // try{
            $data = [
                'user_id' => auth()->user()->id,
                'payment_wallet' => $addressData['address_base58'],
                'private_key' => $addressData['private_key'],
                'amount' => $amount,
                'coin' => $coin,
                'expired_at' => $expired
            ];
            $store = CryptoPayment::create($data);
            $history = [
                'user_id' => auth()->user()->id,
                'unique_code' => 'PSID-'.Str::random(10),
                'type' => 'sell_crypto',
                'amount' => $amount,
                'recipient_id' => 2,
                'crypto_payment_id' => $store->id,
            ];
            $storeHistory = HistoryTransaction::create($history);
            // DB::commit();
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return $e->getMessage();
        // }
        
        return $storeHistory->id;
        }
        if($action == 'checkbill'){
            $client = new Client();
            $response = $client->request('POST', 'https://api.trongrid.io/wallet/gettransactionbyid', [
              'body' => '{"value":"fec8611f4318574b5355c77dc9a1f587f579390ff97b510496d8676f0f174bc6"}',
              'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
              ],
            ]);
            $response = json_decode($response->getBody()->getContents());
            return $response;
            
        }
    }
}