<?php

namespace Zainpay\SDK\WordPress;

use Zainpay\SDK\VirtualAccount as BaseVirtualAccount;
use Zainpay\SDK\Response;
use Zainpay\SDK\Util\FilterUtil;
use Zainpay\SDK\WordPress\Lib\WordPressRequestTrait;

class VirtualAccount extends BaseVirtualAccount
{
    use WordPressRequestTrait;

    public function transactionList(string $accountNumber, ?string $txnType, ?string $paymentChannel, ?string $dateFrom, ?string $dateTo, int $count = 20): Response
    {
        return $this->get($this->getModeUrl() . 'virtual-account/wallet/transactions/' . $accountNumber . "/" . $count, FilterUtil::ConstructFilterParams(null, $txnType, $paymentChannel, $dateFrom, $dateTo));
    }

    public function createVirtualAccount(
        string $bvn,
        string $firstName,
        string $surname,
        string $email,
        string $mobile,
        string $dob,
        string $gender,
        string $address,
        string $title,
        string $state,
        string $zainboxCode,
        string $bankType = "wemaBank"
    ): Response {
        return $this->post($this->getModeUrl() . 'virtual-account/create/request', [
            'bankType'     => $bankType,
            'bvn'          => $bvn,
            'firstName'    => $firstName,
            'surname'      => $surname,
            'email'        => $email,
            'mobileNumber' => $mobile,
            'dob'          => $dob,
            'gender'       => $gender,
            'address'      => $address,
            'title'        => $title,
            'state'        => $state,
            'zainboxCode'  => $zainboxCode
        ]);
    }
}
