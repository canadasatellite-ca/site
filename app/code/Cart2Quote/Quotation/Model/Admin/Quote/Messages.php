<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Admin\Quote;

/**
 * Class Messages
 *
 * @package Cart2Quote\Quotation\Model\Admin\Quote
 */
class Messages implements \Magento\Framework\Notification\MessageInterface
{
    use \Cart2Quote\Features\Traits\Model\Admin\Quote\Messages {
        getIdentity as private traitGetIdentity;
        isDisplayed as private traitIsDisplayed;
        getNewRequestCount as private traitGetNewRequestCount;
        getNewRequestSinceLoginCount as private traitGetNewRequestSinceLoginCount;
        getText as private traitGetText;
        getSeverity as private traitGetSeverity;
        getPreviousLogin as private traitGetPreviousLogin;
        getNewQuoteCollection as private traitGetNewQuoteCollection;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $backendUrl;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    private $adminSessionInfoCollection;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    private $data;

    /**
     * Messages constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $adminSessionInfoCollection
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     * @param \Cart2Quote\Quotation\Helper\Data $data
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection $adminSessionInfoCollection,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory,
        \Cart2Quote\Quotation\Helper\Data $data
    ) {
        $this->data = $data;
        $this->collectionFactory = $collectionFactory;
        $this->authSession = $authSession;
        $this->backendUrl = $backendUrl;
        $this->adminSessionInfoCollection = $adminSessionInfoCollection;
    }

    /**
     * Determine the unique message identity in order to enhance message behavior
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->traitGetIdentity();
    }

    /**
     * Determine whether to show the message or not
     *
     * @return boolean
     */
    public function isDisplayed()
    {
        return $this->traitIsDisplayed();
    }

    /**
     * Get quote request count with the state "open"
     *
     * @return int
     */
    public function getNewRequestCount()
    {
        return $this->traitGetNewRequestCount();
    }

    /**
     * Get quote request count with the state "open" and creation date after last login
     *
     * @return int
     */
    public function getNewRequestSinceLoginCount()
    {
        return $this->traitGetNewRequestSinceLoginCount();
    }

    /**
     * Generate the text to be shown in the message
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getText()
    {
        return $this->traitGetText();
    }

    /**
     * Get the severity value of the message
     *
     * @return int
     */
    public function getSeverity()
    {
        return $this->traitGetSeverity();
    }

    /**
     * Get previous admin login model
     *
     * @return \Magento\Security\Model\AdminSessionInfo
     */
    public function getPreviousLogin()
    {
        return $this->traitGetPreviousLogin();
    }

    /**
     * Get new quotes collection
     *
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
     */
    public function getNewQuoteCollection()
    {
        return $this->traitGetNewQuoteCollection();
    }
}
