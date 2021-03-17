<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\CustomerData;

/**
 * Class Customer
 *
 * @package Cart2Quote\Quotation\Plugin\CustomerData
 */
class Customer
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Customer constructor.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * After get session data plugin
     *
     * @param \Magento\Customer\CustomerData\Customer $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData(\Magento\Customer\CustomerData\Customer $subject, $result)
    {
        $result['isLoggedIn'] = $this->customerSession->isLoggedIn();
        if ($this->customerSession->isLoggedIn() && $this->customerSession->getCustomerId()) {
            $customer = $this->customerSession->getCustomer();
            $result['email'] = $customer->getEmail();
            $result['firstname'] = $customer->getFirstname();
            $result['lastname'] = $customer->getLastname();
        }

        return $result;
    }
}
