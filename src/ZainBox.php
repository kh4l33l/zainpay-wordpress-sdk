<?php

namespace Zainpay\SDK\WordPress;

use Zainpay\SDK\ZainBox as BaseZainBox;
use Zainpay\SDK\Response;
use Zainpay\SDK\Util\FilterUtil;
use Zainpay\SDK\WordPress\Lib\WordPressRequestTrait;

class ZainBox extends BaseZainBox
{
    use WordPressRequestTrait;

    public function merchantTransactionList(?string $accountNumber, ?string $txnType, ?string $paymentChannel, ?string $dateFrom, ?string $dateTo, int $count = 20): Response
    {
        return $this->get($this->getModeUrl() . 'zainbox/transactions', array_merge(["count" => $count], FilterUtil::ConstructFilterParams($accountNumber, $txnType, $paymentChannel, $dateFrom, $dateTo)));
    }

    public function transactionList(string $zainboxCode, ?string $accountNumber, ?string $txnType, ?string $paymentChannel, ?string $dateFrom, ?string $dateTo, int $count = 20): Response
    {
        return $this->get($this->getModeUrl() . 'zainbox/transactions/' . $zainboxCode . "/" . $count, FilterUtil::ConstructFilterParams($accountNumber, $txnType, $paymentChannel, $dateFrom, $dateTo));
    }

    public function transactionHistory(string $zainboxCode, ?string $accountNumber, ?string $txnType, ?string $paymentChannel, ?string $dateFrom, ?string $dateTo, int $count = 20): Response
    {
        return $this->transactionList($zainboxCode, $accountNumber, $txnType, $paymentChannel, $dateFrom, $dateTo, $count);
    }

    public function virtualAccountTransactionList(string $virtualAccount, ?string $txnType, ?string $paymentChannel, ?string $dateFrom, ?string $dateTo, int $count = 20): Response
    {
        return $this->get($this->getModeUrl() . 'virtual-account/wallet/transactions/' . $virtualAccount . "/" . $count, FilterUtil::ConstructFilterParams(null, $txnType, $paymentChannel, $dateFrom, $dateTo));
    }
}
