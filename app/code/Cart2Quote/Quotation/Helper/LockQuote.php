<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

/**
 * Class LockQuote
 * @package Cart2Quote\Quotation\Helper
 */
class LockQuote extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @const quote form settings config path
     */
    const LOCK_QUOTE = 'cart2quote_advanced/lock_quote/quote_statuses';

    /**
     * @return array
     */
    public function getQuoteStatusesConfigArray()
    {
        return json_decode(
            $this->scopeConfig->getValue(
                self::LOCK_QUOTE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ),
            true
        );
    }
}
