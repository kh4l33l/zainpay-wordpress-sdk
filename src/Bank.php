<?php

namespace Zainpay\SDK\WordPress;

use Zainpay\SDK\Bank as BaseBank;
use Zainpay\SDK\WordPress\Lib\WordPressRequestTrait;

class Bank extends BaseBank
{
    use WordPressRequestTrait;
}
