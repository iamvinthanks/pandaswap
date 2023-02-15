<?php

namespace App\Services;
use IEXBase\TronAPI\Tron;
use App\Models\CryptoPayment;
use App\Models\HistoryTransaction;
use App\Repository\HistoryTransactionRepository;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Str;
use DB;
use IEXBase\TronAPI\Provider\HttpProvider;



class TronService
{
    public function __construct()
    {
        $this->fullNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        $this->solidityNode = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
        $this->eventServer = new \IEXBase\TronAPI\Provider\HttpProvider('https://api.shasta.trongrid.io');
    }
    public function generateWallet($amount,$coin)
    {
        try {
            $tron = new \IEXBase\TronAPI\Tron($this->fullNode, $this->solidityNode, $this->eventServer);
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            exit($e->getMessage());
        }
        $createAddrs = $tron->createAccount();
        $addressData['key'] = $createAddrs->response['private_key'];
        $addressData['address'] = $createAddrs->response['address_base58'];
        return $addressData;
    }
    public function getBalance($address)
    {
        try {
            $tron = new \IEXBase\TronAPI\Tron($this->fullNode, $this->solidityNode, $this->eventServer);
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            exit($e->getMessage());
        }
        $balance = $tron->getBalance($address);
        return $balance;
    }
    public function sendbalance($amount,$coin,$address,$pk)
    {
        try {
            $tron = new \IEXBase\TronAPI\Tron($this->fullNode, $this->solidityNode, $this->eventServer);
        } catch (\IEXBase\TronAPI\Exception\TronException $e) {
            exit($e->getMessage());
        }
        $tron->setPrivateKey($pk);
        $tron->setAddress($address);
        $resp = $tron->send($address, $amount);

        return $resp;
    }
}
