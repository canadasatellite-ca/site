<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Api;

/**
 * Interface AccountManagementInterface
 *
 * @package Cart2Quote\Quotation\Api
 */
interface AccountManagementInterface extends \Magento\Customer\Api\AccountManagementInterface
{
    /**
     * Send either confirmation or welcome email after an account creation
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string $redirectUrl
     * @return void
     */
    public function sendNewEmailConfirmation(\Magento\Customer\Api\Data\CustomerInterface $customer, $redirectUrl = '');
}
