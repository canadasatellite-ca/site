<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Model\Service\Payment;

class COD
{
    private $order;

    /**
     * @var array
     */
    private $data;

    public function __construct(
        $order,
        $cardCollectionFactory,
        array $data = []
    ) {
    
        $this->order = $order;
        $this->data = $data;
    }

    public function getPaymentToken()
    {
        return '';
    }
    
    public function getTitle()
    {
        return $this->getPaymentInformation()->getAdditionalInformation('method_title');
    }

    public function getMethodCode()
    {
        return $this->getPaymentInformation()->getMethod();
    }

    private function getPaymentInformation()
    {
        return $this->order->getPayment();
    }

    public function getImportData()
    {
        /** @var \Magedelight\Subscribenow\Model\ProductSubscribers $subscription */
        $subscription = $this->data['subscription_instance'];
        return [
            'method' => $subscription->getPaymentMethodCode(),
            'subscription_id' => $subscription->getPaymentToken()
        ];
    }
}
