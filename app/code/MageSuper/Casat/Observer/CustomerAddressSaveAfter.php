<?php


namespace MageSuper\Casat\Observer;

class CustomerAddressSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Customer\Model\Address $customer_address */
        $customer_address = $observer->getEvent()->getCustomerAddress();
        $customer = $customer_address->getCustomer();
        $changed = false;
        $currentDefaultShipping = $customer->getDefaultShippingAddress();
        $currentDefaultShippingId = false;
        if($currentDefaultShipping){
            $currentDefaultShippingId = $currentDefaultShipping->getEntityId();
        }
        $currentDefaultBilling = $customer->getDefaultBillingAddress();
        $currentDefaultBillingId = false;
        if($currentDefaultBilling){
            $currentDefaultBillingId = $currentDefaultBilling->getEntityId();
        }
        $address_id = (int)$customer_address->getEntityId();
        if ($customer_address->getData('default_shipping') == false && $currentDefaultShippingId && $currentDefaultShippingId == $address_id) {
            $customer->setData('default_shipping', null);
            $changed = true;

        }
        if ($customer_address->getData('default_billing') == false && $currentDefaultBillingId && $currentDefaultBillingId == $address_id) {
            $customer->setData('default_billing', null);
            $changed = true;
        }
        if ($changed) {
            $customer->save();
        }
    }
}
