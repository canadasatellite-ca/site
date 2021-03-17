<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model;

use \Cart2Quote\Quotation\Api\AccountManagementInterface;
use \Magento\Framework\App\ObjectManager;
use \Magento\Framework\Math\Random;

/**
 * Class AccountManagment
 *
 * @package Cart2Quote\Quotation\Model
 */
class AccountManagement extends \Magento\Customer\Model\AccountManagement implements AccountManagementInterface
{
    use \Cart2Quote\Features\Traits\Model\AccountManagement {
        sendNewEmailConfirmation as private traitSendNewEmailConfirmation;
        getMathRandom as private traitGetMathRandom;
    }

    /**
     * @var Random
     */
    private $mathRandom;

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
    public function sendNewEmailConfirmation(\Magento\Customer\Api\Data\CustomerInterface $customer, $redirectUrl = '')
    {
        $this->traitSendNewEmailConfirmation($customer, $redirectUrl);
    }

    /**
     * The constructor of the account management interface has 30 agruments,
     * so we have less conflicts if we use the object manager here.
     *
     * @return Random
     */
    public function getMathRandom()
    {
        return $this->traitGetMathRandom();
    }
}
