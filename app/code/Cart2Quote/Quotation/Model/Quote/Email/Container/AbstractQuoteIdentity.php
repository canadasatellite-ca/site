<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Email\Container;

/**
 * Class AbstractQuoteIdentity
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Container
 */
abstract class AbstractQuoteIdentity extends Container implements IdentityInterface
{
    use \Cart2Quote\Features\Traits\Model\Quote\Email\Container\AbstractQuoteIdentity {
        isEnabled as private traitIsEnabled;
        getEmailCopyTo as private traitGetEmailCopyTo;
        getCopyMethod as private traitGetCopyMethod;
        getTemplateId as private traitGetTemplateId;
        getEmailIdentity as private traitGetEmailIdentity;
        getGuestTemplateId as private traitGetGuestTemplateId;
        getRecieverEmail as private traitGetRecieverEmail;
        getRecieverName as private traitGetRecieverName;
        isSendCopyToSalesRep as private traitIsSendCopyToSalesRep;
    }

    /**
     * Configuration paths
     */
    const XML_PATH_EMAIL_COPY_METHOD = '';
    const XML_PATH_EMAIL_COPY_TO = '';
    const XML_PATH_EMAIL_IDENTITY = '';
    const XML_PATH_EMAIL_TEMPLATE = '';
    const XML_PATH_EMAIL_ENABLED = '';
    const XML_PATH_EMAIL_GUEST_TEMPLATE = '';

    /**
     * Get is enabled setting
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->traitIsEnabled();
    }

    /**
     * Return email copy_to list
     *
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        return $this->traitGetEmailCopyTo();
    }

    /**
     * Return copy method
     *
     * @return mixed
     */
    public function getCopyMethod()
    {
        return $this->traitGetCopyMethod();
    }

    /**
     * Return template id
     *
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->traitGetTemplateId();
    }

    /**
     * Return email identity
     *
     * @return mixed
     */
    public function getEmailIdentity()
    {
        return $this->traitGetEmailIdentity();
    }

    /**
     * Return template id
     *
     * @return mixed
     */
    public function getGuestTemplateId()
    {
        return $this->traitGetGuestTemplateId();
    }

    /**
     * Get reciever email address
     *
     * @return string
     */
    public function getRecieverEmail()
    {
        return $this->traitGetRecieverEmail();
    }

    /**
     * Get reciever name
     *
     * @return string
     */
    public function getRecieverName()
    {
        return $this->traitGetRecieverName();
    }

    /**
     * Salesrep placeholder
     *
     * @return bool
     */
    public function isSendCopyToSalesRep()
    {
        return $this->traitIsSendCopyToSalesRep();
    }
}
