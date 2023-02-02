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
    public function Create($amount)
    {
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        try {
            $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            exit($e->getMessage());
        }
        $expired = Carbon::now()->addMinutes(5);
        $createAddrs = $tron->createAccount();
        $addressData = $createAddrs->response;
        $coin = 'TRX';
        DB::beginTransaction();
        try{
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
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
        
        return $storeHistory->id;
    }
    public function Check()
    {
        $fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        $solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        $eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        try {
            $tron = new \IEXBase\TronAPI\Tron($fullNode, $solidityNode, $eventServer);
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            exit($e->getMessage());
        }
        $transaction = HistoryTransaction::where('status','waiting_payment')->with('detail_wallet','user_rekening')->get();
        try{
        foreach($transaction as $key => $trans){
            $todaytime = Carbon::now()->format('Y-m-d H:i:s');
            if($todaytime > $transaction[$key]->detail_wallet->expired_at)
            {
                HistoryTransaction::where('id',$transaction[$key]->id)->update([
                    'status' => 'expired'
                ]);
            }
        }

        //check payment 
        $transactionupdate = HistoryTransaction::where('status','waiting_payment')->with('detail_wallet','user_rekening')->get();
        foreach($transactionupdate as $kiy => $trans2){
        $amount_to_send = $transactionupdate[$kiy]->amount;
        $payment_wallet = $transactionupdate[$kiy]->detail_wallet->payment_wallet;
        if($transactionupdate[$kiy]->type == 'sell_crypto')
        {
            if($transactionupdate[$kiy]->detail_wallet->coin == 'TRX')
            {
                $balance = $tron->getBalance($payment_wallet) / 1000000; // get balance convert from satoshi to TRX
                if($balance >= $amount_to_send || $balance <= $amount_to_send && $balance > 15)
                {
                    $tron->send(ENV('TRON_ADDRESS'), $balance);
                    // <<--- Here add To send money to user
                    HistoryTransaction::where('id',$transactionupdate[$kiy]->id)->update($data['status']);
                    CryptoPayment::where('id',$transactionupdate[$kiy]->crypto_payment_id)->update([
                        'paid_amount'=> $balance,
                        'status' => 'completed',
                        'updated_at' => Carbon::now()
                    ]);
                }
            }
        }
        }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

}