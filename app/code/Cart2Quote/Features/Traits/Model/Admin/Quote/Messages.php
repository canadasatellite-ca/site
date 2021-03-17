<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Admin\Quote;
/**
 * Trait Messages
 *
 * @package Cart2Quote\Quotation\Model\Admin\Quote
 */
trait Messages
{
    /**
     * Determine the unique message identity in order to enhance message behavior
     *
     * @return string
     */
    private function getIdentity()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return hash('sha256', 'CART2QUOTE_NOTIFICATION' . $this->authSession->getUser()->getLogdate());
		}
	}
    /**
     * Determine whether to show the message or not
     *
     * @return boolean
     */
    private function isDisplayed()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->data->displayQuoteNotice($this->authSession->getUser()->getId())) {
            return $this->getNewRequestCount() > 0 || $this->getNewRequestSinceLoginCount() > 0;
        }
        return false;
		}
	}
    /**
     * Get quote request count with the state "open"
     *
     * @return int
     */
    private function getNewRequestCount()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteCollection = $this->getNewQuoteCollection();
        return $quoteCollection->getSize();
		}
	}
    /**
     * Get quote request count with the state "open" and creation date after last login
     *
     * @return int
     */
    private function getNewRequestSinceLoginCount()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteCollection = $this->getNewQuoteCollection();
        $loginDate = $this->authSession->getUser()->getLogdate();
        $adminLogin = $this->getPreviousLogin();
        $lastLoginDate = $adminLogin->getQuotationCreatedAt();
        if (empty($lastLoginDate)) {
            $lastLoginDate = $loginDate;
        }
        $quoteCollection->addFieldToFilter(
            \Magento\Quote\Model\Quote::KEY_UPDATED_AT,
            ['gt' => $lastLoginDate]
        );
        $quoteCollection->addFieldToFilter(
            \Magento\Quote\Model\Quote::KEY_UPDATED_AT,
            ['lt' => $loginDate]
        );
        return $quoteCollection->getSize();
		}
	}
    /**
     * Generate the text to be shown in the message
     *
     * @return \Magento\Framework\Phrase|string
     */
    private function getText()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$message = __('Cart2Quote notice:');
        $url = $this->backendUrl->getUrl('quotation/quote');
        $newRequestCount = $this->getNewRequestCount();
        if ($newRequestCount == 1) {
            $message .= '<br/>';
            $message .= __('You have <a href="%1">1 unanswered quote</a> request.', $url);
        } else {
            if ($newRequestCount > 1) {
                $message .= '<br/>';
                $message .= __('You have <a href="%1">%2 unanswered quote</a> requests.', $url, $newRequestCount);
            }
        }
        $newRequestSinceLogin = $this->getNewRequestSinceLoginCount();
        if ($newRequestSinceLogin == 1) {
            $message .= '<br/>';
            $message .= __('There is <a href="%1">1 new quote request</a> since your last login.', $url);
        } else {
            if ($newRequestSinceLogin > 1) {
                $message .= '<br/>';
                $message .= __(
                    'There are <a href="%1">%2 new quote requests</a> since your last login.',
                    $url,
                    $newRequestSinceLogin
                );
            }
        }
        return $message;
		}
	}
    /**
     * Get the severity value of the message
     *
     * @return int
     */
    private function getSeverity()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return \Magento\Framework\Notification\MessageInterface::SEVERITY_NOTICE;
		}
	}
    /**
     * Get previous admin login model
     *
     * @return \Magento\Security\Model\AdminSessionInfo
     */
    private function getPreviousLogin()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$statuses = [
            \Magento\Security\Model\AdminSessionInfo::LOGGED_OUT,
            \Magento\Security\Model\AdminSessionInfo::LOGGED_OUT_BY_LOGIN,
            \Magento\Security\Model\AdminSessionInfo::LOGGED_OUT_MANUALLY
        ];
        $adminCollection = $this->adminSessionInfoCollection;
        $adminCollection->addFieldToFilter('user_id', $this->authSession->getUser()->getId());
        $adminCollection->addFieldToFilter('status', ['in' => $statuses]);
        $this->adminSessionInfoCollection->setOrder('created_at', 'DESC');
        return $adminCollection->getFirstItem();
		}
	}
    /**
     * Get new quotes collection
     *
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
     */
    private function getNewQuoteCollection()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/**
         * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $collection
         */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteInterface::STATE,
            \Cart2Quote\Quotation\Model\Quote\Status::STATE_OPEN
        );
        $collection->addFieldToFilter('is_quote', ['eq' => \Cart2Quote\Quotation\Model\Quote::IS_QUOTE]);
        $collection->addFieldToFilter(
            'admin_creator_id',
            [
                ['neq' => $this->authSession->getUser()->getId()],
                ['null' => true]
            ]
        );
        return $collection;
		}
	}
}
