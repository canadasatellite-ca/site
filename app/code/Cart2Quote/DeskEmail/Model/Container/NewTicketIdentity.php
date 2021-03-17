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

class NewTicketIdentity extends Container implements IdentityInterface
{
    const XML_PATH_EMAIL_COPY_METHOD = 'cart2quote_deskemail/new_ticket/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'cart2quote_deskemail/new_ticket/copy_to';
    const XML_PATH_EMAIL_ADMIN_TEMPLATE = 'cart2quote_deskemail/new_ticket/admin_template';
    const XML_PATH_EMAIL_TEMPLATE = 'cart2quote_deskemail/new_ticket/template';
    const XML_PATH_EMAIL_IDENTITY = 'cart2quote_deskemail/new_ticket/identity';
    const XML_PATH_EMAIL_ENABLED = 'cart2quote_deskemail/new_ticket/enabled';

    /**
     * Check if the email is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_scopeConfig->isSetFlag(
            self::XML_PATH_EMAIL_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getStoreId()
        );
    }

    /**
     * Get the copy to emails
     *
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        $data = $this->getConfigValue(self::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    /**
     * Get the copy method
     *
     * @return string
     */
    public function getCopyMethod()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStore()->getStoreId());
    }

    /**
     * Get the admin template ID
     *
     * @return string
     */
    public function getAdminTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_ADMIN_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * Get the template ID
     *
     * @return string
     */
    public function getTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE, $this->getStore()->getStoreId());
    }

    /**
     * Get the sender email
     *
     * @return string
     */
    public function getEmailIdentity()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_IDENTITY, $this->getStore()->getStoreId());
    }
}
