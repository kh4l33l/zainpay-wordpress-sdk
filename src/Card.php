<?php

namespace Zainpay\SDK\WordPress;

use Zainpay\SDK\Card as BaseCard;
use Zainpay\SDK\WordPress\Lib\WordPressHttpClient;

class Card extends BaseCard
{
    protected function createClient(array $config)
    {
        return new WordPressHttpClient($config);
    }
}
