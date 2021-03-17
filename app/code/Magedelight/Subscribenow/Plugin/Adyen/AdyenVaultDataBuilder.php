<?php
/**
 * Magedelight
 * Copyright (C) 2018 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Plugin\Adyen;

use Closure;
use Magedelight\Subscribenow\Helper\Data as SubscribeHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;

class AdyenVaultDataBuilder {

    /**
     * Recurring variable
     * @var string
     */
    private static $enableRecurring = 'enableRecurring';
    /**
     * @var SubscribeHelper
    */
    private $subscribeHelper;

    /**
     * 
     * @param SubscribeHelper $subscribeHelper
     */
    public function __construct(
       SubscribeHelper $subscribeHelper  
    ) {
         $this->subscribeHelper = $subscribeHelper;
    }

    /**
     * 
     * @param \Adyen\Payment\Model\Cron $subject
     * @param type $result
     */
    public function aroundBuild(\Adyen\Payment\Gateway\Request\VaultDataBuilder $subject, Closure $proceed,$buildSubject) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("vault builder");
        $paymentDO = SubjectReader::readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $result = $proceed($buildSubject);
            if ($this->subscribeHelper->isModuleEnable()) {
                if($this->hasSubscriptionProduct($payment)){
                    $result[self::$enableRecurring] = true;
                }
            }
            
            $logger->info(print_r($result, true));
            return $result;
    }
    private function hasSubscriptionProduct($paymentInfo)
    {
        $allItems = $paymentInfo->getOrder()->getAllItems();
        foreach ($allItems as $item) {
            $buyRequest = $item->getBuyRequest()->getData();
            return !empty($buyRequest) &&
                isset($buyRequest['options']['_1']) &&
                ($buyRequest['options']['_1'] == 'subscription');

        }
    }
}
