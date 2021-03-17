<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Order\Cancel;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Reason
 */
class Reason implements OptionSourceInterface
{
    /**
     * Creates the attribute type options
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var array */
        return [
            ['value' => 'NoInventory', 'label' => __('No Inventory')],
            ['value' => 'ShippingAddressUndeliverable', 'label' => __('Shipping Address Undeliverable')],
            ['value' => 'CustomerExchange', 'label' => __('Customer Exchange')],
            ['value' => 'BuyerCanceled', 'label' => __('Buyer Canceled')],
            ['value' => 'GeneralAdjustment', 'label' => __('General Adjustment')],
            ['value' => 'CarrierCreditDecision', 'label' => __('Carrier Credit Decision')],
            ['value' => 'RiskAssessmentInformationNotValid', 'label' => __('Risk Assessment Information Not Valid')],
            ['value' => 'CarrierCoverageFailure', 'label' => __('Carrier Coverage Failure')],
            ['value' => 'CustomerReturn', 'label' => __('Customer Return')],
            ['value' => 'MerchandiseNotReceived', 'label' => __('Merchandise Not Received')]
        ];
    }
}
