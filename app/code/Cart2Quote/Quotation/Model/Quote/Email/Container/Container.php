<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Email\Container;

/**
 * Class Container
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Container
 */
abstract class Container extends \Magento\Sales\Model\Order\Email\Container\Container implements IdentityInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\Email\Container\Container {
    }

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct(
            $scopeConfig,
            $storeManager
        );
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }
}
