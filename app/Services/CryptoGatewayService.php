<?php

namespace App\Services;
use IEXBase\TronAPI\Tron;
use App\Models\CryptoPayment;
use App\Models\HistoryTransaction;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Str;
use DB;
use IEXBase\TronAPI\Provider\HttpProvider;



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
        $tron->setPrivateKey("5ea5c571adf3de837265ec86ff0c7fa0f8acadbc141a9f24cc312738e0c1c07b");
        $tron->setAddress("TRqN3V1GboCaShMYhzAvgx5XbCMPoenocn");
        /// testtt

        $tron->sendToken('TXtERAriPz234LMEo7YTm3hSWbwTpwUzP1',10,"usdt");

        // testtt
        $transaction = HistoryTransaction::where('status','waiting_payment')->with('detail_wallet','user_rekening')->get();
        try{
        $todaytime = Carbon::now()->format('Y-m-d H:i:s');
        //check payment
        $transactionupdate = HistoryTransaction::where('status','waiting_payment')->with('detail_wallet','user_rekening')->get();
        foreach($transactionupdate as $kiy => $trans2){
        $amount_to_send[$kiy] = $transactionupdate[$kiy]->amount;
        $payment_wallet[$kiy] = $transactionupdate[$kiy]->detail_wallet->payment_wallet;
        $pk_wallet[$kiy] = $transactionupdate[$kiy]->detail_wallet->private_key;
        if($transactionupdate[$kiy]->type == 'sell_crypto')
        {
            if($transactionupdate[$kiy]->detail_wallet->coin == 'TRX')
            {
                $balance[$kiy] = $tron->getBalance($payment_wallet[$kiy]); // get balance convert from satoshi to TRX
                $balance[$kiy] = $balance[$kiy] / 1000000;
                if($balance[$kiy] >= $amount_to_send[$kiy] || $balance[$kiy] <= $amount_to_send[$kiy] && $balance[$kiy] > 15)
                {
                    $tron->setPrivateKey($pk_wallet[$kiy]);
                    $tron->setAddress($payment_wallet[$kiy]);
                    $tron->send(ENV('TRX_ADDRESS'), $balance[$kiy]);
                    // <<--- Here add To send money to user
                    HistoryTransaction::where('id',$transactionupdate[$kiy]->id)->update(['status' => 'completed',]);
                    CryptoPayment::where('id',$transactionupdate[$kiy]->crypto_payment_id)->update([
                        'paid_amount'=> $balance[$kiy],
                        'status' => 'completed',
                        'updated_at' => Carbon::now()
                    ]);
                }
            }
        }
        foreach($transaction as $key => $trans){
            if($todaytime > $transaction[$key]->detail_wallet->expired_at)
            {
                HistoryTransaction::where('id',$transaction[$key]->id)->update([
                    'status' => 'expired'
                ]);
            }
        }
        }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return true;
    }

}
