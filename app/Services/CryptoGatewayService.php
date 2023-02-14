<?php

namespace App\Services;
use IEXBase\TronAPI\Tron;
use App\Models\CryptoPayment;
use App\Models\HistoryTransaction;
use App\Repository\HistoryTransactionRepository;
use App\Services\TronService;
use App\Services\BSCService;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Str;
use DB;
use IEXBase\TronAPI\Provider\HttpProvider;

class CryptoGatewayService
{
    public function __construct()
    {
        $this->TRXService = new TronService();
        $this->BNBService = new BSCService();
        $this->HistoryTransaction = new HistoryTransactionRepository();

    }
    public function Create($amount,$coin)
    {
        if($coin == 'TRX' || $coin == 'USDT'){
            $wallet = $this->TRXService->generateWallet($amount,$coin);
        }
        if($coin == 'BNB' || $coin == 'BUSD'){
            $wallet = $this->BNBService->generateWallet($amount,$coin);

        }
        $txdata = $this->HistoryTransaction->addHistoryTransaction($amount,$coin,$wallet['address'],$wallet['key'],'sell_crypto' );
        return $txdata;
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
