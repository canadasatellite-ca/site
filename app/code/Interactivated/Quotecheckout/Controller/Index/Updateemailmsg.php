<?php

namespace Interactivated\Quotecheckout\Controller\Index;

class Updateemailmsg extends \Interactivated\Quotecheckout\Controller\Checkout\Onepage
{
	function execute()
	{
		if (!$this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
            $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
            $email                = (string) $this->getRequest()->getParam('email');
            $websiteId            = $storeManager->getWebsite()->getId();
            $store                = $storeManager->getStore();
            $customer             = $this->_objectManager->get('Magento\Customer\Model\Customer');
            $customer->setWebsiteId($websiteId);
            $customer->setStore($store);
            $customer->loadByEmail($email);
            if ($customer->getId() && $customer->getPasswordHash()) {
                echo "0";
            } else {
                echo "1";
            }
        } else {
            echo "1";
        }

        return;
	}
}
