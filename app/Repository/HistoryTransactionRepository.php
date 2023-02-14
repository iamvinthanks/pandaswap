<?php

namespace App\Repository;

use App\Models\HistoryTransaction;
use App\Models\CryptoPayment;
use DB;
use Carbon\Carbon;
use Str;
class HistoryTransactionRepository

{
    public function addHistoryTransaction($amount,$coin,$wallet,$key,$type)
    {
        DB::beginTransaction();
        try{
            $expired = Carbon::now()->addMinutes(5);
            $data = [
                'user_id' => auth()->user()->id,
                'payment_wallet' => $wallet,
                'private_key' => $key,
                'amount' => $amount,
                'coin' => $coin,
                'expired_at' => $expired
            ];
            $store = CryptoPayment::create($data);
            $history = [
                'user_id' => auth()->user()->id,
                'unique_code' => 'PSID-'.Str::random(10),
                'type' =>$type,
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
        $txdata = [
            'txid' => $storeHistory->unique_code,
            'amount' => $storeHistory->amount,
            'coin' => $data['coin'],
            'wallet' => $data['payment_wallet'],
            'expired_at' => $data['expired_at'],
        ];
        return $txdata;
    }
}
