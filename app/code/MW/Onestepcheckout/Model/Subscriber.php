<?php

namespace MW\Onestepcheckout\Model;

class Subscriber extends \Magento\Newsletter\Model\Subscriber
{
	/**
     * Override: Load subscriber info by customerId
     *
     * @param int $customerId
     * @return $this
     */
    public function loadByCustomerId($customerId)
    {
        try {
            $customerData = $this->customerRepository->getById($customerId);
            $data = $this->getResource()->loadByCustomerData($customerData);
            $this->addData($data);
            if (!empty($data) && $customerData->getId() && !$this->getCustomerId()) {
                $this->setCustomerId($customerData->getId());
                $this->setSubscriberConfirmCode($this->randomSequence());

                // Subscribe while registerring new account using Onestepcheckout
				if (!empty($data['subscriber_confirm_code'])) {
					$this->setSubscriberConfirmCode($data['subscriber_confirm_code']);
				}

                $this->save();
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
        }

        return $this;
    }
}
