<?php
namespace Digit\Paymentmethods\Observer;
use Magento\Framework\Event\ObserverInterface;


class DisablePaymentMethods implements ObserverInterface
{

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $result = $observer->getEvent()->getResult();
        $method = $observer->getEvent()->getMethodInstance();

        $currentMethod = $method->getCode();

        $subscriptionGroupAllowed = array('checkbymail','wiretransfer');
        $quote = $observer->getQuote();
        if ($quote){
            $customerGroup = $quote->getCustomerGroupId();
            if (in_array($currentMethod,$subscriptionGroupAllowed)){
                if ($customerGroup == '11'){//if in subscription group
                    return $this;
                }
            }
        }

        if(in_array($method->getCode(),array('creditcardphone','creditcardinstore','wiretransfer',
            'cash','checkbymail'))){
            $result->setData('is_available',false);
        }
        return $this;
    }
}