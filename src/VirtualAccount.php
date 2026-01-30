<?php

namespace Zainpay\SDK\WordPress;

use Zainpay\SDK\VirtualAccount as BaseVirtualAccount;
use Zainpay\SDK\WordPress\Lib\WordPressHttpClient;

class VirtualAccount extends BaseVirtualAccount
{
    protected function createClient(array $config)
    {
        return new WordPressHttpClient($config);
    }
}
