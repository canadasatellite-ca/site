<?php
/**
 * Magedelight
 * Copyright (C) 2018 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2018 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Plugin\Shipping\Rate\Result;

use Magedelight\Subscribenow\Helper\Data;
use Magedelight\Subscribenow\Model\Subscription;

class Append
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Subscription
     */
    private $subscription;
    
    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper,
        Subscription $subscription
    ) {
    
        $this->subscription = $subscription;
        $this->helper = $helper;
    }
    
    /**
     * Validate each shipping method before append.
     *
     * @param \Magento\Shipping\Model\Rate\Result $subject
     * @param \Magento\Quote\Model\Quote\Address\RateResult\AbstractResult|\Magento\Shipping\Model\Rate\Result $result
     * @return array
     */
    public function beforeAppend($subject, $result)
    {
        if (!$this->helper->isModuleEnable()) {
            return [$result];
        }
        
        if (!$result instanceof \Magento\Quote\Model\Quote\Address\RateResult\Method) {
            return [$result];
        }
        
        $isFreeShipping = $this->helper->isSubscriptionWithFreeShipping();
        $result->setData('is_free_shipping', false);
        
        if ($isFreeShipping && $this->subscription->cartHasSubscriptionItem()) {
            $result->setIsFreeShipping(true);
        }

        if ($this->isMethodRestricted($result)) {
            $result->setIsDisabled(true);
        }
        
        return [$result];
    }
    
    /**
     * @param \Magento\Shipping\Model\Rate\Result $shippingModel
     *
     * @return bool
     */
    public function isMethodRestricted($shippingModel)
    {
        $code = $shippingModel->getCarrier();
        $restrictedMethod = $this->helper->getAllowedShippingMethods();
        
        if ($restrictedMethod && !in_array($code, $restrictedMethod)) {
            return true;
        }

        return false;
    }
}
