<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model;
use \Cart2Quote\Quotation\Api\AccountManagementInterface;
use \Magento\Framework\App\ObjectManager;
use \Magento\Framework\Math\Random;
/**
 * Trait AccountManagement
 *
 * @package Cart2Quote\Quotation\Model
 */
trait AccountManagement
{
    /**
     * Send either confirmation or welcome email after an account creation
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $redirectUrl
     * @return void
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    private function sendNewEmailConfirmation(\Magento\Customer\Api\Data\CustomerInterface $customer, $redirectUrl = '')
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$newLinkToken = $this->getMathRandom()->getUniqueHash();
        $this->changeResetPasswordLinkToken($customer, $newLinkToken);
        parent::sendEmailConfirmation($customer, $redirectUrl);
		}
	}
    /**
     * The constructor of the account management interface has 30 agruments,
     * so we have less conflicts if we use the object manager here.
     *
     * @return Random
     */
    private function getMathRandom()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->mathRandom) {
            $this->mathRandom = ObjectManager::getInstance()->get(Random::class);
        }
        return $this->mathRandom;
		}
	}
}
