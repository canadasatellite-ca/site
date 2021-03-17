<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Http\UserAgent;

/**
 * Class Validator
 *
 * @package Aheadworks\AdvancedReviews\Model\Http\UserAgent
 */
class Validator
{
    /**
     * Check if specific user agent belongs to the bot
     *
     * @param string $userAgent
     * @return bool
     */
    public function isBot($userAgent)
    {
        return \Zend_Http_UserAgent_Bot::match($userAgent, []);
    }
}
