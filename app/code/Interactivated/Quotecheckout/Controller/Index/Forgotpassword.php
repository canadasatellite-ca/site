<?php

namespace Interactivated\Quotecheckout\Controller\Index;

use Magento\Customer\Model\AccountManagement;

class Forgotpassword extends \Interactivated\Quotecheckout\Controller\Checkout\Onepage
{
	function execute()
	{
        $email      = $this->getRequest()->getPost('email');
        if ($email) {
            if (!\Zend_Validate::is($email, 'EmailAddress')) {
                $this->_customerSession->setForgottenEmail($email);
                $emailError = "0";

                echo $emailError;
                return;
            }

            $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
            $websiteId = $storeManager->getWebsite()->getId();
            $store = $storeManager->getStore();

            $customer = $this->_objectManager->get('Magento\Customer\Model\Customer');
            $customer->setWebsiteId($websiteId);
            $customer->setStore($store);
            $customer->loadByEmail($email);
            if ($customer->getId()) {
                $customerAccountManagement = $this->_objectManager->get(
                    'Magento\Customer\Api\AccountManagementInterface'
                );
                try {
                    $customerAccountManagement->initiatePasswordReset(
                        $email,
                        AccountManagement::EMAIL_RESET
                    );
                    $emailError = "1";

                    echo $emailError;
                    return;
                } catch (\Exception $e) {
                    $emailError = "2";
                    echo $emailError;
                    return;
                }
            } else {
                $emailError = "2";
                $this->_customerSession->setForgottenEmail($email);

                echo $emailError;
                return;
            }
        } else {
            $emailError = "0";
            echo $emailError;
            return;
        }
	}
}
