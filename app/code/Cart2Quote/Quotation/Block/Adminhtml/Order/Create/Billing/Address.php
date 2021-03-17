<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */
namespace Cart2Quote\Quotation\Block\Adminhtml\Order\Create\Billing;

/**
 * Class Address
 * -- Fix for M2 2.2.8, 2.3.1 and up. (While staying compatible with 2.1, <2.2.8 and 2.3.0)
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Order\Create\Billing
 */
class Address extends \Magento\Sales\Block\Adminhtml\Order\Create\Billing\Address
{
    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        //add customerAddressCollection if it isn't set
        if (!$this->hasData('customerAddressCollection')) {
            if (class_exists(\Magento\Customer\Model\ResourceModel\Address\Collection::class)) {
                /** @var \Magento\Customer\Model\ResourceModel\Address\Collection $addressCollection */
                $addressCollection = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get(\Magento\Customer\Model\ResourceModel\Address\Collection::class);

                $this->setData('customerAddressCollection', $addressCollection);
            }
        }

        //add customerAddressFormatter if it isn't set
        if (!$this->hasData('customerAddressFormatter')) {
            if (class_exists(\Magento\Sales\ViewModel\Customer\AddressFormatter::class)) {
                /** @var \Magento\Sales\ViewModel\Customer\AddressFormatter $addressFormatter */
                $addressFormatter = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get(\Magento\Sales\ViewModel\Customer\AddressFormatter::class);

                $this->setData('customerAddressFormatter', $addressFormatter);
            }
        }
    }
}
