<?php

namespace CanadaSatellite\Theme\Model\Stdlib\Cookie;

use Magento\Framework\Stdlib\Cookie\PhpCookieManager as ParentPhpCookieManager;

class PhpCookieManager extends ParentPhpCookieManager
{
    const MAX_NUM_COOKIES = 50;
    const MAX_COOKIE_SIZE = 8192;
}
