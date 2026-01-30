<?php

namespace Zainpay\SDK\WordPress;

use Zainpay\SDK\Bank as BaseBank;
use Zainpay\SDK\WordPress\Lib\WordPressHttpClient;

class Bank extends BaseBank
{
    protected function createClient(array $config)
    {
        return new WordPressHttpClient($config);
    }
}
