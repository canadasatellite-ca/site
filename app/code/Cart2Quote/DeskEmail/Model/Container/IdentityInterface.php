<?php
/**
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2016] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 *
 * @category    Cart2Quote
 * @package     DeskEmail
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\DeskEmail\Model\Container;

use Magento\Store\Model\Store;

/**
 * Interface IdentityInterface
 */
interface IdentityInterface
{
    /**
     * Check if the email is enabled
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Get the copy to emails
     *
     * @return array|bool
     */
    public function getEmailCopyTo();

    /**
     * Get the copy method
     *
     * @return string
     */
    public function getCopyMethod();

    /**
     * Get the admin template ID
     *
     * @return string
     */
    public function getAdminTemplateId();

    /**
     * Get the template ID
     *
     * @return string
     */
    public function getTemplateId();

    /**
     * Get the sender email
     *
     * @return string
     */
    public function getEmailIdentity();

    /**
     * Get the main email
     *
     * @return string
     */
    public function getMainEmail();

    /**
     * Get the main name
     *
     * @return string
     */
    public function getMainName();

    /**
     * Get the store
     *
     * @return Store
     */
    public function getStore();

    /**
     * Set the store
     *
     * @param Store $store
     * @return void
     */
    public function setStore(Store $store);

    /**
     * Set the main email
     *
     * @param string $email
     * @return void
     */
    public function setMainEmail($email);

    /**
     * Set the main name
     *
     * @param string $name
     * @return void
     */
    public function setMainName($name);
}
