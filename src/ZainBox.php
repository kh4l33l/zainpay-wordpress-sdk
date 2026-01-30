<?php

namespace Zainpay\SDK\WordPress;

use Zainpay\SDK\ZainBox as BaseZainBox;
use Zainpay\SDK\WordPress\Lib\WordPressHttpClient;

class ZainBox extends BaseZainBox
{
    protected function createClient(array $config)
    {
        return new WordPressHttpClient($config);
    }
}
