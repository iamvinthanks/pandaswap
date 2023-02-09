<?php

namespace App\Services;
use App\Models\CryptoPayment;
use App\Models\HistoryTransaction;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Str;
use DB;
use IEXBase\TronAPI\Provider\HttpProvider;

class BSCService
{
    public function wallet()
    {
        $wallet = new \Binance\Wallet();
        $dd = $wallet->newAccountByPrivateKey();
        dd($dd);
    }
    public function interface()
    {
        // $uri = 'https://bsc-dataseed1.defibit.io/';// Mainnet
        // $uri = 'https://data-seed-prebsc-1-s1.binance.org:8545/';// Testnet
        // $api = new \Binance\NodeApi($uri);

        ## 方法 2 : Bscscan Api
        $apiKey = 'EJ5V369RFQGYBG1K4UAV2ACVYCE9JRHDW5';
        $api = new \Binance\BscscanApi($apiKey);

        $bnb = new \Binance\Bnb($api);

        $config = [
            'contract_address' => '0x55d398326f99059fF775485246999027B3197955',// USDT BEP20
            'decimals' => 18,
        ];
        $bep20 = new \Binance\BEP20($api, $config);
        // $balance = $bnb->bnbBalance('0xfeE38a5C7116d9227e8E476037dBf9d140d4cbbE');

        $from = 'dfaf2cf900ae080b2341540afcd33ac9fa0c503c434d866bbc226449d9266080';
        $to = '0xfeE38a5C7116d9227e8E476037dBf9d140d4cbbE';
        $amount = 0.09;
        $transfer = $bnb->transfer($from, $to, $amount);
        dd($transfer);
    }
}
