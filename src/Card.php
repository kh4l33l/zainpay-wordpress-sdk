<?php

namespace Zainpay\SDK\WordPress;

use Zainpay\SDK\Card as BaseCard;
use Zainpay\SDK\Response;
use Zainpay\SDK\Util\FilterUtil;
use Zainpay\SDK\WordPress\Lib\WordPressRequestTrait;

class Card extends BaseCard
{
    use WordPressRequestTrait;

    public function zainboxTransactionHistory(string $zainboxCode, int $count = 20, ?string $dateFrom, ?string $dateTo, ?string $email, ?string $status, ?string $txnRef): Response
    {
        return $this->get($this->getModeUrl() . 'zainbox/card/transactions/' . $zainboxCode, FilterUtil::CardTxnHistoryFilterParams(null, $count, $dateFrom, $dateTo, $email, $status, $txnRef));
    }
}
