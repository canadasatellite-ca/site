<?php

namespace Interactivated\Quotecheckout\Controller\Index;

class Updatelogin extends \Interactivated\Quotecheckout\Controller\Checkout\Onepage
{
	public function execute()
	{
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        if ($customerSession->isLoggedIn()) {
        	echo "1";
            return;
        }

        if ($this->getRequest()->isPost()) {
        	$email    = $this->getRequest()->getPost('email');
			$password = $this->getRequest()->getPost('password');

            if (!empty($email) && !empty($password)) {
                $accountManagement = $this->_objectManager->get('Magento\Customer\Api\AccountManagementInterface');
                try {
                    $customer = $accountManagement->authenticate($email, $password);
                    $customerSession->setCustomerDataAsLoggedIn($customer);
                    $customerSession->regenerateId();
                    echo "1";
                } catch (\Exception $e) {
                    echo "0";
                }
            }
        }

        return;
	}
}
