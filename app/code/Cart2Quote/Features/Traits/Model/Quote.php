<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model;
use Magento\Catalog\Model\Product;
/**
 * Quote model
 * Supported events:
 *  sales_quote_load_after
 *  sales_quote_save_before
 *  sales_quote_save_after
 *  sales_quote_delete_before
 *  sales_quote_delete_after
 * Trait Quote
 * @package Cart2Quote\Quotation\Model
 */
trait Quote
{
    /**
     * Gets the created_by value from quotation_quote
     *
     * @return string|null
     */
    private function getQuotationCreatedBy() {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::CREATED_BY);
		}
	}
    /**
     * Saves the created_by value to quotation_quote
     *
     * @param string $createdBy
     * @return Quote
     */
    private function setQuotationCreatedBy($createdBy)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::CREATED_BY, $createdBy);
		}
	}
    /**
     * Saves customer's reject message in the database
     *
     * @param string $reasonForRejection
     * @return $this
     */
    private function setRejectMessage($reasonForRejection)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::REJECT_MESSAGE, $reasonForRejection);
		}
	}
    /**
     * Retrieve current shipping method
     *
     * @return string
     */
    private function getShippingMethod()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->getIsVirtual() && ($this->getShippingAddress()->getShippingDescription())) {
            return $this->getShippingAddress()->getShippingDescription();
        }
		}
	}
    /**
     * Set send request email
     *
     * @param bool $sendRequestEmail
     * @return $this
     */
    private function setSendRequestEmail($sendRequestEmail)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::SEND_REQUEST_EMAIL, $sendRequestEmail);
		}
	}
    /**
     * Get send request email
     *
     * @return $this
     */
    private function getSendRequestEmail()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::SEND_REQUEST_EMAIL);
		}
	}
    /**
     * Set request email sent
     *
     * @param bool $requestEmailSent
     * @return $this
     */
    private function setRequestEmailSent($requestEmailSent)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::REQUEST_EMAIL_SENT, $requestEmailSent);
		}
	}
    /**
     * Get request email send
     *
     * @return $this
     */
    private function getRequestEmailSent()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::REQUEST_EMAIL_SENT);
		}
	}
    /**
     * Set send quote canceled email
     *
     * @param bool $sendQuoteCanceledEmail
     * @return $this
     */
    private function setSendQuoteCanceledEmail($sendQuoteCanceledEmail)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::SEND_QUOTE_CANCELED_EMAIL, $sendQuoteCanceledEmail);
		}
	}
    /**
     * Get send quote canceled email
     *
     * @return $this
     */
    private function getSendQuoteCanceledEmail()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::SEND_QUOTE_CANCELED_EMAIL);
		}
	}
    /**
     * Set quote canceled email sent
     *
     * @param bool $quoteCanceledEmailSent
     * @return $this
     */
    private function setQuoteCanceledEmailSent($quoteCanceledEmailSent)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::QUOTE_CANCELED_EMAIL_SENT, $quoteCanceledEmailSent);
		}
	}
    /**
     * Get quote canceled email sent
     *
     * @return $this
     */
    private function getQuoteCanceledEmailSent()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::QUOTE_CANCELED_EMAIL_SENT);
		}
	}
    /**
     * Set send quote edited email
     *
     * @param boolean $sendQuoteEditedEmail
     * @return $this
     */
    private function setSendQuoteEditedEmail($sendQuoteEditedEmail)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::SEND_QUOTE_EDITED_EMAIL, $sendQuoteEditedEmail);
		}
	}
    /**
     * Get send quote edited email
     *
     * @return boolean
     */
    private function getSendQuoteEditedEmail()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::SEND_QUOTE_EDITED_EMAIL);
		}
	}
    /**
     * Set quote edited email sent
     *
     * @param boolean $quoteEditedEmailSent
     * @return $this
     */
    private function setQuoteEditedEmailSent($quoteEditedEmailSent)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::QUOTE_EDITED_EMAIL_SENT, $quoteEditedEmailSent);
		}
	}
    /**
     * Get quote edited email sent
     *
     * @return boolean
     */
    private function getQuoteEditedEmailSent()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::QUOTE_EDITED_EMAIL_SENT);
		}
	}
    /**
     * Set send proposal accepted email
     *
     * @param bool $sendProposalAcceptedEmail
     * @return $this
     */
    private function setSendProposalAcceptedEmail($sendProposalAcceptedEmail)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::SEND_PROPOSAL_ACCEPTED_EMAIL, $sendProposalAcceptedEmail);
		}
	}
    /**
     * Get send proposal accepted email
     *
     * @return $this
     */
    private function getSendProposalAcceptedEmail()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::SEND_PROPOSAL_ACCEPTED_EMAIL);
		}
	}
    /**
     * Set proposal accepted email sent
     *
     * @param bool $proposalAcceptedEmailSent
     * @return $this
     */
    private function setProposalAcceptedEmailSent($proposalAcceptedEmailSent)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::PROPOSAL_ACCEPTED_EMAIL_SENT, $proposalAcceptedEmailSent);
		}
	}
    /**
     * Get proposal accepted email sent
     *
     * @return $this
     */
    private function getProposalAcceptedEmailSent()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::PROPOSAL_ACCEPTED_EMAIL_SENT);
		}
	}
    /**
     * Set send proposal expired email
     *
     * @param bool $sendProposalExpiredEmail
     * @return $this
     */
    private function setSendProposalExpiredEmail($sendProposalExpiredEmail)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::SEND_PROPOSAL_EXPIRED_EMAIL, $sendProposalExpiredEmail);
		}
	}
    /**
     * Get send proposal expired email
     *
     * @return $this
     */
    private function getSendProposalExpiredEmail()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::SEND_PROPOSAL_EXPIRED_EMAIL);
		}
	}
    /**
     * Set propozal expired email sent
     *
     * @param bool $proposalExpiredEmailSent
     * @return $this
     */
    private function setProposalExpiredEmailSent($proposalExpiredEmailSent)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::PROPOSAL_EXPIRED_EMAIL_SENT, $proposalExpiredEmailSent);
		}
	}
    /**
     * Get propsal expired email sent
     *
     * @return $this
     */
    private function getProposalExpiredEmailSent()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::PROPOSAL_EXPIRED_EMAIL_SENT);
		}
	}
    /**
     * Set send proposal email
     *
     * @param bool $sendProposalEmail
     * @return $this
     */
    private function setSendProposalEmail($sendProposalEmail)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::SEND_PROPOSAL_EMAIL, $sendProposalEmail);
		}
	}
    /**
     * Get send proposal email
     *
     * @return $this
     */
    private function getSendProposalEmail()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::SEND_PROPOSAL_EMAIL);
		}
	}
    /**
     * Set proposal email sent
     *
     * @param bool $proposalEmailSent
     * @return $this
     */
    private function setProposalEmailSent($proposalEmailSent)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::PROPOSAL_EMAIL_SENT, $proposalEmailSent);
		}
	}
    /**
     * Get proposal email sent
     *
     * @return $this
     */
    private function getProposalEmailSent()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::PROPOSAL_EMAIL_SENT);
		}
	}
    /**
     * Set send reminder email
     *
     * @param bool $sendReminderEmail
     * @return $this
     */
    private function setSendReminderEmail($sendReminderEmail)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::SEND_REMINDER_EMAIL, $sendReminderEmail);
		}
	}
    /**
     * Get send reminder email
     *
     * @return $this
     */
    private function getSendReminderEmail()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::SEND_REMINDER_EMAIL);
		}
	}
    /**
     * Set reminder email sent
     *
     * @param bool $reminderEmailSent
     * @return $this
     */
    private function setReminderEmailSent($reminderEmailSent)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::REMINDER_EMAIL_SENT, $reminderEmailSent);
		}
	}
    /**
     * Get reminder email sent
     *
     * @return $this
     */
    private function getReminderEmailSent()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::REMINDER_EMAIL_SENT);
		}
	}
    /**
     * Get proposal sent
     *
     * @return string
     */
    private function getProposalSent()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::PROPOSAL_SENT);
		}
	}
    /**
     * Get fixed shipping price
     *
     * @return float
     */
    private function getFixedShippingPrice()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::FIXED_SHIPPING_PRICE);
		}
	}
    /**
     * Set fixed shipping price
     *
     * @param float $fixedShippingPrice
     * @return $this
     */
    private function setFixedShippingPrice($fixedShippingPrice)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::FIXED_SHIPPING_PRICE, $fixedShippingPrice);
		}
	}
    /**
     * Create
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    private function create(\Magento\Quote\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$quote->getId()) {
            $quote->getResource()->save($quote);
            $this->isObjectNew(true);
        }
        //backend creation check
        try {
            if ($this->appState->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
                $this->setCreatedInBackend(true);
                if ($this->authSession->getUser()) {
                    $this->setAdminCreatorId($this->authSession->getUser()->getId());
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            //Do nothing, area code is probably not set.
        }
        $this->setQuoteId($quote->getId());
        $this->addData($quote->getData()); //this needs to be the first line before the other setters
        $this->setLinkedQuotationId($quote->getId());
        $this->setIsQuotationQuote(true);
        $this->setStoreId($quote->getStoreId());
        $this->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_OPEN)
            ->setStatus(
                $this->getConfig()->getStateDefaultStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATE_OPEN)
            );
        if ($quote->getIsPhoneOnly()) {
            $this->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_PHONE_ONLY);
        }
        if ($quote->getPrintOnly()) {
            $this->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_PRINT_ONLY);
        }
        $this->setOriginalSubtotal($this->getSubtotal());
        $this->setBaseOriginalSubtotal($this->getBaseSubtotal());
        if (!$quote->getCustomerIsGuest()) {
            $this->setCustomer($quote->getCustomer());
        }
        $this->setBillingAddress($quote->getBillingAddress());
        $this->setShippingAddress($quote->getShippingAddress());
        $defaultExpiryDate = $this->_scopeConfig->getValue(
            self::DEFAULT_EXPIRATION_TIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()
        );
        if ($defaultExpiryDate == 0) {
            $this->setExpiryEnabled(false);
        } else {
            $this->setExpiryDate($this->getDefaultExpiryDate());
        }
        $this->setExpiryEmail(true);
        $this->setReminderDate($this->getDefaultReminderDate());
        $defaultReminderTime = $this->_scopeConfig->getValue(
            self::DEFAULT_REMINDER_TIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()
        );
        if ($defaultReminderTime == 0) {
            $this->setReminderEnabled(false);
        } else {
            $this->setReminderEnabled(true);
        }
        /**
         * The first save is needed to create tier items to the database.
         * RecollectQuote function needs these tier items to calculate the totals.
         * We need the second save to save the totals to database
         */
        $this->save();
        $this->setRecollect(true);
        $this->recollectQuote();
        $this->save();
        return $this;
		}
	}
    /**
     * Sets the status for the quote.
     *
     * @param string $status
     * @return $this
     */
    private function setStatus($status)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::STATUS, $status);
		}
	}
    /**
     * Sets the state for the quote.
     *
     * @param string $state
     * @return $this
     */
    private function setState($state)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::STATE, $state);
		}
	}
    /**
     * Retrieve quote configuration model
     *
     * @return Quote\Config
     */
    private function getConfig()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_quoteConfig;
		}
	}
    /**
     * Set original subtotal
     *
     * @param float $originalSubtotal
     * @return $this
     */
    private function setOriginalSubtotal($originalSubtotal)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(self::ORIGINAL_SUBTOTAL, $originalSubtotal);
        return $this;
		}
	}
    /**
     * Set Base Original Subtotal
     *
     * @param float $originalBaseSubtotal
     * @return $this
     */
    private function setBaseOriginalSubtotal($originalBaseSubtotal)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(self::ORIGINAL_BASE_SUBTOTAL, $originalBaseSubtotal);
        return $this;
		}
	}
    /**
     * Set Base Original Subtotal Incl Tax
     *
     * @param float $originalSubtotalInclTax
     * @return $this
     */
    private function setOriginalSubtotalInclTax($originalSubtotalInclTax)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(self::ORIGINAL_SUBTOTAL_INCL_TAX, $originalSubtotalInclTax);
        return $this;
		}
	}
    /**
     * Set Base Original Subtotal Incl Tax
     *
     * @param float $originalBaseSubtotalInclTax
     * @return $this
     */
    private function setBaseOriginalSubtotalInclTax($originalBaseSubtotalInclTax)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(self::BASE_ORIGINAL_SUBTOTAL_INCL_TAX, $originalBaseSubtotalInclTax);
        return $this;
		}
	}
    /**
     * Get Original Subtotal Incl Tax
     *
     * @return float
     */
    private function getOriginalSubtotalInclTax()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::ORIGINAL_SUBTOTAL_INCL_TAX);
		}
	}
    /**
     * Get Base Original Subtotal Incl Tax
     *
     * @return float
     */
    private function getBaseOriginalSubtotalInclTax()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::BASE_ORIGINAL_SUBTOTAL_INCL_TAX);
		}
	}
    /**
     * Get default expiry date of quote
     *
     * @return date
     */
    private function getDefaultExpiryDate()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$defaultExpiryTime = $this->_scopeConfig->getValue(
            self::DEFAULT_EXPIRATION_TIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()
        );
        if ($defaultExpiryTime == null) {
            $defaultExpiryTime = 7; // days
        }
        $expiryDate = strtotime("+" . $defaultExpiryTime . " day");
        return $this->datetime->gmDate('Y-m-d', $expiryDate);
		}
	}
    /**
     * Get default reminder date of quote
     *
     * @return date
     */
    private function getDefaultReminderDate()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$defaultReminderTime = $this->_scopeConfig->getValue(
            self::DEFAULT_REMINDER_TIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()
        );
        if ($defaultReminderTime == null) {
            $defaultReminderTime = 3; // days
        }
        $reminderDate = strtotime("+" . $defaultReminderTime . " day");
        return $this->datetime->gmDate('Y-m-d', $reminderDate);
		}
	}
    /**
     * Save quote data
     *
     * @return $this
     * @throws \Exception
     */
    private function save()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			//save quotation quote
        $this->_getResource()->save($this);
        //save quote quote
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->_objectManager->create(\Magento\Quote\Model\Quote::class)->load($this->getId());
        if (($quote->getLinkedQuotationId() == $this->getId()) || !$quote->getLinkedQuotationId()) {
            $quote->addData($this->getData());
            $quote->_getResource()->save($quote);
        }
        return $this;
		}
	}
    /**
     * Set collect totals flag for quote
     *
     * @param bool $flag
     * @return $this
     */
    private function setRecollect($flag)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_needCollect = $flag;
        return $this;
		}
	}
    /**
     * Recollect totals for customer cart.
     * - Set recollect totals flag for quote
     *
     * @return $this
     */
    private function recollectQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->_needCollect === true) {
            //save some data for later checks and fallbacks
            $prefixName = $this->getCustomerPrefix();
            $firstName = $this->getCustomerFirstname();
            $middleName = $this->getCustomerMiddlename();
            $lastName = $this->getCustomerLastname();
            $suffixName = $this->getCustomerSuffix();
            $dob = $this->getCustomerDob();
            $gender = $this->getCustomerGender();
            $email = $this->getData('customer_email');
            //on some setups collect totals removes the customer data, probably a module conflict
            $this->collectTotals();
            //check if we lost the email address
            if ($this->getData('customer_email') != $email) {
                //WARNING; There is very likely a module conflict on the checkout_cart_save_before or checkout_cart_save_after events. Please fix that or contact us.
                //$this->logger->info('C2Q: Probable module conflict');
                $this->setCustomerPrefix($prefixName);
                $this->setCustomerFirstname($firstName);
                $this->setCustomerMiddlename($middleName);
                $this->setCustomerLastname($lastName);
                $this->setCustomerSuffix($suffixName);
                $this->setCustomerDob($dob);
                $this->setCustomerGender($gender);
                $this->setCustomerEmail($email);
            }
            /**
             * Set Original Subtotal
             */
            $this->recalculateOriginalSubtotal();
            /**
             * Set Custom Price Total
             */
            $this->recalculateCustomPriceTotal();
            /**
             * Set Quote adjustment total
             */
            $this->recalculateQuoteAdjustmentTotal();
        }
        $this->setRecollect(false);
        return $this;
		}
	}
    /**
     * Function that recalculates the new original subtotal
     *
     * @return $this
     */
    private function recalculateOriginalSubtotal()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$newOriginalSubtotal = 0;
        $newBaseOriginalSubtotal = 0;
        $originalSubtotalInclTax = 0;
        $originalBaseSubtotalInclTax = 0;
        foreach ($this->getAllVisibleItems() as $item) {
            if (!$this->isChildProduct($item)) {
                if ($item->getCurrentTierItem()) {
                    $price = $item->getCurrentTierItem()->getOriginalPrice();
                    $basePrice = $item->getCurrentTierItem()->getBaseOriginalPrice();
                    $priceInclTax = $item->getCurrentTierItem()->getOriginalPriceInclTax();
                    $basePriceInclTax = $item->getCurrentTierItem()->getBaseOriginalPriceInclTax($item, $basePrice);
                } else {
                    $price = $item->getConvertedPrice();
                    $basePrice = $item->getBasePrice();
                    $priceInclTax = $this->getOriginalPriceInclTax($item, $price);
                    $basePriceInclTax = $this->getBaseOriginalPriceInclTax($item, $basePrice);
                }
                //roundPrice is added in Magento 2.3.1
                //$price = $this->priceCurrency->roundPrice($price);
                $price = $this->priceCurrency->round($price);
                $newOriginalSubtotal += $this->formatSubtotal($item, $price);
                $newBaseOriginalSubtotal += $this->formatSubtotal($item, $basePrice);
                $originalSubtotalInclTax += $this->formatSubtotal($item, $priceInclTax);
                $originalBaseSubtotalInclTax += $this->formatSubtotal($item, $basePriceInclTax);
            }
        }
        $this->setOriginalSubtotal($newOriginalSubtotal);
        $this->setBaseOriginalSubtotal($newBaseOriginalSubtotal);
        $this->setOriginalSubtotalInclTax($originalSubtotalInclTax);
        $this->setBaseOriginalSubtotalInclTax($originalBaseSubtotalInclTax);
        return $this;
		}
	}
    /**
     * Get original price including tax
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float $price
     * @return float|int
     */
    private function getOriginalPriceInclTax($item, $price)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $price + $this->getOriginalTaxAmount($item, $price);
		}
	}
    /**
     * Get base original price including tax
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float $basePrice
     * @return float|int
     */
    private function getBaseOriginalPriceInclTax($item, $basePrice)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $basePrice + $this->getBaseOriginalTaxAmount($item, $basePrice);
		}
	}
    /**
     * Get original tax amount
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float $price
     * @return float
     */
    private function getOriginalTaxAmount($item, $price)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            return $item->getTaxAmount();
        }
        $taxRate = $item->getTaxPercent();
        return $price * ($taxRate / 100);
		}
	}
    /**
     * Get base original tax amount
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float $price
     * @return float
     */
    private function getBaseOriginalTaxAmount($item, $price)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            return $item->getBaseTaxAmount();
        }
        $taxRate = $item->getTaxPercent();
        return $price * ($taxRate / 100);
		}
	}
    /**
     * Format subtotal
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param float $price
     * @return float
     */
    private function formatSubtotal($item, $price)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return (($item->getQty() * $price) * 1);
		}
	}
    /**
     * Check if item has parent and parent type is configurable or bundle
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return bool
     */
    private function isChildProduct($item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$parent = $item->getParentItem();
        if (isset($parent)) {
            if ($parent->getProductType() == 'configurable'
                || $parent->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                return true;
            }
        }
        return false;
		}
	}
    /**
     * Concert a price to the quote rate price
     * - Magento does not come with a currency conversion via the quote rates, only the active rates.
     *
     * @param int|float|double $price
     * @return double
     */
    private function convertPriceToQuoteCurrency($price)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->isCurrencyDifferent()) {
            $price = \Cart2Quote\Quotation\Model\Quote::convertRate($price, $this->getBaseToQuoteRate(), false);
        }
        return $price;
		}
	}
    /**
     * Check if currency is different on this quote
     *
     * @return bool
     */
    private function isCurrencyDifferent()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteCurrency = $this->getData(\Magento\Quote\Api\Data\CurrencyInterface::KEY_QUOTE_CURRENCY_CODE);
        $baseCurrency = $this->getData(\Magento\Quote\Api\Data\CurrencyInterface::KEY_BASE_CURRENCY_CODE);
        return $quoteCurrency != $baseCurrency;
		}
	}
    /**
     * Convert the rate of a price
     * - Todo: consider refactoring this to a helper
     *
     * @param int|float|double $price
     * @param int|float|double $rate
     * @param bool $base
     * @return double
     */
    private static function convertRate($price, $rate, $base = false)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($base) {
            $price = (double)$price / $rate;
        } elseif (!$base) {
            $price = (double)$price * $rate;
        }
        return $price;
		}
	}
    /**
     * Function that recalculates the new custom price total
     *
     * @return $this
     */
    private function recalculateCustomPriceTotal()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$customPriceTotal = 0;
        $baseCustomPriceTotal = 0;
        foreach ($this->getAllVisibleItems() as $item) {
            if (!$this->isChildProduct($item)) {
                $itemCustomPrice = $item->getCustomPrice();
                if ($itemCustomPrice <= 0) {
                    if ($this->priceIncludesTax($item->getStoreId())) {
                        $itemCustomPrice = $item->getPriceInclTax();
                    } else {
                        $itemCustomPrice = $item->getConvertedPrice();
                    }
                }
                $itemBaseCustomPrice = $item->getBaseCustomPrice();
                if ($itemBaseCustomPrice <= 0) {
                    if ($this->priceIncludesTax($item->getStoreId())) {
                        $itemBaseCustomPrice = $item->getBasePriceInclTax();
                    } else {
                        $itemBaseCustomPrice = $item->getBasePrice();
                    }
                }
                $itemCustomPrice = floatval($itemCustomPrice);
                $customPriceTotal += $itemCustomPrice * $item->getQty();
                $baseCustomPriceTotal += $itemBaseCustomPrice * $item->getQty();
            }
        }
        $this->setCustomPriceTotal($customPriceTotal);
        $this->setBaseCustomPriceTotal($baseCustomPriceTotal);
        return $this;
		}
	}
    /**
     * Check if the current item is set to show prices including tax
     *
     * @param null|int $storeId
     * @return bool
     */
    private function priceIncludesTax($storeId = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($storeId === null) {
            $storeId = $this->getStoreId();
        }
        return $this->quotationTaxHelper->priceIncludesTax($storeId);
		}
	}
    /**
     * Check if subtotal includes tax on this quote or a given store id
     *
     * @param int $storeId
     * @return bool
     */
    private function subtotalIncludesTax($storeId = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($storeId === null) {
            $storeId = $this->getStoreId();
        }
        return $this->quotationTaxHelper->displaySalesSubtotalInclTax($storeId);
		}
	}
    /**
     * Set Custom Price Total
     *
     * @param float $customPriceTotal
     * @return $this
     */
    private function setCustomPriceTotal($customPriceTotal)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(self::CUSTOM_PRICE_TOTAL, $customPriceTotal);
        return $this;
		}
	}
    /**
     * Set Base Custom Price Total
     *
     * @param float $baseCustomPriceTotal
     * @return $this
     */
    private function setBaseCustomPriceTotal($baseCustomPriceTotal)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(self::BASE_CUSTOM_PRICE_TOTAL, $baseCustomPriceTotal);
        return $this;
		}
	}
    /**
     * Function that recalculates the new custom price total
     *
     * @return $this
     */
    private function recalculateQuoteAdjustmentTotal()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteAdjustment = ((double)$this->getBaseSubtotal()) - (double)$this->getBaseOriginalSubtotal();
        $baseQuoteAdjustment = ((double)$this->getSubtotal()) - (double)$this->getOriginalSubtotal();
        $this->setQuoteAdjustment($quoteAdjustment);
        $this->setBaseQuoteAdjustment($baseQuoteAdjustment);
        return $this;
		}
	}
    /**
     * Getter for the tax amount
     *
     * @return int
     */
    private function getTaxAmount()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$tax = 0;
        foreach ($this->getAllVisibleItems() as $item) {
            $tax += $item->getTaxAmount();
        }
        return $tax;
		}
	}
    /**
     * Getter for the base tax amount
     *
     * @return int
     */
    private function getBaseTaxAmount()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$tax = 0;
        foreach ($this->getAllVisibleItems() as $item) {
            $tax += $item->getBaseTaxAmount();
        }
        return $tax;
		}
	}
    /**
     * Get Base Original Subtotal
     *
     * @return float
     */
    private function getBaseOriginalSubtotal()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::ORIGINAL_BASE_SUBTOTAL);
		}
	}
    /**
     * Get original subtotal
     *
     * @return mixed
     */
    private function getOriginalSubtotal()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::ORIGINAL_SUBTOTAL);
		}
	}
    /**
     * Set Quote Adjustment
     *
     * @param float $quoteAdjustment
     * @return $this
     */
    private function setQuoteAdjustment($quoteAdjustment)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(self::QUOTE_ADJUSTMENT, $quoteAdjustment);
        return $this;
		}
	}
    /**
     * Set Base Quote Adjustment
     *
     * @param float $baseQuoteAdjustment
     * @return $this
     */
    private function setBaseQuoteAdjustment($baseQuoteAdjustment)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setData(self::BASE_QUOTE_ADJUSTMENT, $baseQuoteAdjustment);
        return $this;
		}
	}
    /**
     * Remove tier item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param int $qty
     * @return $this
     */
    private function removeTier(\Magento\Quote\Model\Quote\Item $item, $qty)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			//Cannot remove current tier(qty)
        if (!$item->getCurrentTier()->isSelected()
            && $this->tierItemCollectionFactory->create()->tierExists(
                $item->getQty(),
                $qty
            )
        ) {
            return $this->getTier($item, $qty)->delete();
        }
        return $this;
		}
	}
    /**
     * Retrieve quote edit availability
     *
     * @return bool
     */
    private function canEdit()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return true;
		}
	}
    /**
     * Retrieve quote cancel availability
     *
     * @return bool
     */
    private function canCancel()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return true;
		}
	}
    /**
     * Check whether quote is canceled
     *
     * @return bool
     */
    private function isCanceled()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return false;
		}
	}
    /**
     * Retrieve quote hold availability
     *
     * @return bool
     */
    private function canHold()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return true;
		}
	}
    /**
     * Retrieve quote unhold availability
     *
     * @return bool
     */
    private function canUnhold()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return false;
		}
	}
    /**
     * Check if comment can be added to quote history
     *
     * @return bool
     */
    private function canComment()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return true;
		}
	}
    /*********************** STATUSES ***************************/
    /**
     * Can change quote request check
     *
     * @return bool
     */
    private function canChangeRequest()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return true;
		}
	}
    /**
     * Return array of quote status history items without deleted.
     *
     * @return array
     */
    private function getAllStatusHistory()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$history = [];
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted()) {
                $history[] = $status;
            }
        }
        return $history;
		}
	}
    /**
     * Return collection of quote status history items.
     *
     * @return ResourceModel\Quote\Status\History\Collection
     */
    private function getStatusHistoryCollection()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$collection = $this->_historyCollectionFactory->create()->setQuoteFilter($this)
            ->setOrder('created_at', 'desc')
            ->setOrder('entity_id', 'desc');
        if ($this->getId()) {
            foreach ($collection as $status) {
                $status->setQuote($this);
            }
        }
        return $collection;
		}
	}
    /**
     * Return collection of visible on frontend quote status history items.
     *
     * @return array
     */
    private function getVisibleStatusHistory()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$history = [];
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted() && $status->getComment() && $status->getIsVisibleOnFront()) {
                $history[] = $status;
            }
        }
        return $history;
		}
	}
    /**
     * Get status history by id
     *
     * @param int $statusId
     * @return string|false
     */
    private function getStatusHistoryById($statusId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($this->getStatusHistoryCollection() as $status) {
            if ($status->getId() == $statusId) {
                return $status;
            }
        }
        return false;
		}
	}
    /**
     * Set the quote status history object and the quote object to each other
     * - Adds the object to the status history collection, which is automatically saved when the quote is saved.
     * - See the entity_id attribute backend model.
     * - Or the history record can be saved standalone after this.
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Status\History $history
     * @return $this
     */
    private function addStatusHistory(\Cart2Quote\Quotation\Model\Quote\Status\History $history)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$history->setQuote($this);
        $this->setStatus($history->getStatus());
        if (!$history->getId()) {
            $this->setStatusHistories(array_merge($this->getStatusHistories(), [$history]));
            $this->setDataChanges(true);
        }
        return $this;
		}
	}
    /**
     * Quote saving
     *
     * @return $this
     * @throws \Exception
     */
    private function saveQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->getId()) {
            return $this;
        }
        $this->recollectQuote();
        $this->save();
        return $this;
		}
	}
    /**
     * Parse data retrieved from request
     *
     * @param array $data
     * @return  $this
     */
    private function importPostData($data)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (is_array($data)) {
            $this->addData($data);
        } else {
            return $this;
        }
        if (isset($data['comment'])) {
            $this->addData($data['comment']);
            if (empty($data['comment']['customer_note_notify'])) {
                $this->setCustomerNoteNotify(false);
            } else {
                $this->setCustomerNoteNotify(true);
            }
        }
        if (isset($data['billing_address'])) {
            $this->setBillingAddress($data['billing_address']);
        }
        if (isset($data['shipping_address'])) {
            $this->setShippingAddress($data['shipping_address']);
        }
        if (isset($data['shipping_method'])) {
            $this->setShippingMethod($data['shipping_method']);
        }
        if (isset($data['payment_method'])) {
            $this->setPaymentMethod($data['payment_method']);
        }
        if (isset($data['coupon']['code'])) {
            $this->applyCoupon($data['coupon']['code']);
        }
        return $this;
		}
	}
    /**
     * Set shipping method
     *
     * @param string $method
     * @return $this
     */
    private function setShippingMethod($method)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->getShippingAddress()->setShippingMethod($method);
        $this->setRecollect(true);
        return $this;
		}
	}
    /**
     * Set payment method into quote
     *
     * @param string $method
     * @return $this
     */
    private function setPaymentMethod($method)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->getPayment()->setMethod($method);
        return $this;
		}
	}
    /**
     * Add coupon code to the quote
     *
     * @param string $code
     * @return $this
     */
    private function applyCoupon($code)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$code = trim((string)$code);
        $this->setCouponCode($code);
        $this->setRecollect(true);
        return $this;
		}
	}
    /**
     * Empty shipping method and clear shipping rates
     *
     * @return $this
     */
    private function resetShippingMethod()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->getShippingAddress()->setShippingMethod(null);
        $this->getShippingAddress()->setShippingDescription(null);
        $this->getShippingAddress()->removeAllShippingRates();
        return $this;
		}
	}
    /**
     * Collect shipping data for quote shipping address
     *
     * @return $this
     */
    private function collectShippingRates()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$noCountryId = false;
        $this->getShippingAddress()->setCollectShippingRates(true);
        //make sure that the country id is set before the collection, so that we always have a shipping rate
        if (!$this->getShippingAddress()->getCountryId()) {
            $noCountryId = true;
            $this->getShippingAddress()->setCountryId(
                $this->_scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $this->getStore()
                )
            );
        }
        $this->collectRates();
        if ($noCountryId) {
            $this->getShippingAddress()->setCountryId(null);
        }
        return $this;
		}
	}
    /**
     * Calculate totals
     *
     * @return void
     */
    private function collectRates()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->collectTotals();
		}
	}
    /**
     * Set payment data into quote
     *
     * @param array $data
     * @return $this
     */
    private function setPaymentData($data)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!isset($data['method'])) {
            $data['method'] = $this->getPayment()->getMethod();
        }
        $this->getPayment()->importData($data);
        return $this;
		}
	}
    /**
     * Initialize data for price rules
     *
     * @return $this
     */
    private function initRuleData()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_coreRegistry->register(
            'rule_data',
            new \Magento\Framework\DataObject(
                [
                    'store_id' => $this->_session->getStore()->getId(),
                    'website_id' => $this->_session->getStore()->getWebsiteId(),
                    'customer_group_id' => $this->getCustomerGroupId(),
                ]
            )
        );
        return $this;
		}
	}
    /**
     * Set shipping anddress to be same as billing
     *
     * @param bool $flag If true - don't save in address book and actually copy data across billing and shipping
     *                   addresses
     * @return $this
     */
    private function setShippingAsBilling($flag)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($flag) {
            $tmpAddress = clone $this->getBillingAddress();
            $tmpAddress->unsAddressId()->unsAddressType();
            $data = $tmpAddress->getData();
            $data['save_in_address_book'] = 0;
            // Do not duplicate address (billing address will do saving too)
            $this->getShippingAddress()->addData($data);
        }
        $this->getShippingAddress()->setSameAsBilling($flag);
        $this->setRecollect(true);
        return $this;
		}
	}
    /**
     * Add multiple products to current quotation quote
     *
     * @param array $products
     * @return $this
     */
    private function addProducts(array $products)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($products as $productId => $config) {
            $config['qty'] = $this->stockCheck->getQtyFromConfig($config);
            try {
                if (is_numeric($config)) {
                    $config = $this->objectFactory->create(['qty' => $config]);
                }
                if (is_array($config)) {
                    $config = $this->objectFactory->create($config);
                }
                $product = $this->productRepository->getById($productId, false, $this->getStoreId());
                if ($this->quotationDataHelper->isStockEnabledBackend()) {
                    $this->checkProduct($product, $config, true);
                }
                $this->checkProduct($product, $config);
                $quoteItem = $this->addProduct($product, $config);
                $quoteItem->getResource()->save($quoteItem);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                return $e;
            }
        }
        return $this;
		}
	}
    /**
     * Check different product types stock quantities
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $config
     * @param boolean $qtyCheck
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function checkProduct($product, $config, $qtyCheck = false)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			switch ($product->getTypeId()) {
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                $this->stockCheck->prepareBundleProduct($product, $config, $qtyCheck);
                break;
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                $this->stockCheck->prepareConfigurableProduct($product, $config, $qtyCheck);
                break;
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                $this->stockCheck->prepareGroupedProduct($config, $qtyCheck);
                break;
            default:
                $this->stockCheck->prepareSimpleProduct($product, $config, $qtyCheck);
                break;
        }
		}
	}
    /**
     * Add product to the quote
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param null|float|\Magento\Framework\DataObject $request
     * @param null|string $processMode
     * @return \Magento\Quote\Model\Quote\Item|string
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addProduct(
        \Magento\Catalog\Model\Product $product,
        $request = null,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$parentItem = parent::addProduct($product, $request, $processMode);
        if ($product->getTypeId() == \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $product = $parentItem->getProduct();
        }
        $item = $this->getItemByProduct($product);
        if ($item) {
            $tierItem = $item->getCurrentTierItem();
            if (isset($tierItem)) {
                $tierItem->setQty($item->getQty());
                $this->tierItemResourceModel->save($tierItem);
            }
        }
        return $parentItem;
		}
	}
    /**
     * Get quote item by product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool|\Magento\Quote\Model\ResourceModel\Quote\Item
     */
    private function getItemByProduct($product)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($this->getItemsByProduct($product) as $item) {
            if (!$item->getExtensionAttributes()->getSection()->getSectionId()) {
                return $item;
            }
        }
        return false;
		}
	}
    /**
     * Get Items by product
     *
     * @param Product $product
     * @return array
     */
    private function getItemsByProduct($product)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$items = [];
        foreach ($this->getAllItems() as $item) {
            if ($item->representProduct($product)) {
                $items[] = $item;
            }
        }
        return $items;
		}
	}
    /**
     * Update base custom price
     *
     * @return $this
     */
    private function updateBaseCustomPrice()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getCurrentTierItem() instanceof \Cart2Quote\Quotation\Model\Quote\TierItem
                && $item->getCurrentTierItem()->getId()
            ) {
                $baseCalculatedPrice = $item->getBaseCalculationPrice();
                $item->getCurrentTierItem()->setBaseCustomPrice($baseCalculatedPrice)->save();
            }
        }
        return $this;
		}
	}
    /**
     * Set proposal subtotal
     *
     * @param float $amount
     * @param bool $isPercentage
     * @return $this
     */
    private function setSubtotalProposal($amount, $isPercentage)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$storeId = $this->getStoreId();
        if ($this->quotationTaxHelper->customPriceIncludesTax($storeId)) {
            $baseSubtotal = (float)$this->getOriginalSubtotalInclTax();
        } else {
            $baseSubtotal = (float)$this->getOriginalSubtotal();
        }
        foreach ($this->getAllItems() as $item) {
            /** @var \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem */
            $tierItem = $item->getCurrentTierItem();
            if ($isPercentage) {
                $proposalBaseSubtotal = $tierItem->calculatePrice($baseSubtotal, $amount);
            } else {
                $proposalBaseSubtotal = $amount;
                if (!$this->quotationTaxHelper->customPriceIncludesTax($storeId)) {
                    $proposalBaseSubtotal = $this->quotationTaxHelper->getPriceExclTax(
                        $tierItem,
                        $proposalBaseSubtotal
                    );
                }
            }
            $customPrice = null;
            if ($amount > 0) {
                //Calculate item price percentage of original sub-total per item
                if ($this->quotationTaxHelper->customPriceIncludesTax($storeId)) {
                    $orgPrice = $tierItem->getOriginalPriceInclTax();
                } else {
                    $orgPrice = $tierItem->getOriginalPrice();
                }
                $percentage = $tierItem->calculatePercentage($baseSubtotal, $orgPrice);
                $customPrice = $tierItem->calculatePrice($proposalBaseSubtotal, $percentage);
            }
            $tierItem->setCustomPrice($customPrice);
            $tierItem->setSelected();
            $this->setTotalsCollectedFlag(false)->collectTotals();
            $tierItem = $tierItem->setData(
                array_replace_recursive(
                    $tierItem->getData(),
                    $item->getData(),
                    [
                        'base_original_price' => $tierItem->getBaseOriginalPrice()
                    ]
                )
            );
            /**
             * This is needed for catalog prices incl. taxes setting.
             * Otherwise it would display price excl. tax in the custom price input which leads to wrong backend totals.
             */
            $tierItem->setCustomPrice($customPrice);
            $tierItem->save();
        }
        return $this;
		}
	}
    /**
     * Remove quote item
     *
     * @param int $item
     * @return $this
     */
    private function removeQuoteItem($item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->removeItem($item);
        $this->setRecollect(true);
        return $this;
		}
	}
    /**
     * Remove quote item by item identifier
     *
     * @param int $itemId
     * @return $this
     */
    private function removeQuotationItem($itemId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($this->tierItemCollectionFactory->create()->getTierItemsByItemId($itemId) as $tierItem) {
            $tierItem->delete();
        }
        $this->itemSectionResourceModel->delete($this->itemSectionProvider->getSection($itemId));
        return parent::removeItem($itemId);
		}
	}
    /**
     * Get items collection
     *
     * @param bool $useCache
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getItemsCollection($useCache = true)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (null === $this->_items) {
            $this->_items = $this->_quoteItemCollectionFactory->create();
            $this->_items->getSelect()->joinLeft(
                ['quotation_quote_section_items' => $this->_items->getTable('quotation_quote_section_items')],
                'main_table.item_id=quotation_quote_section_items.item_id',
                'sort_order'
            );
            $this->_items->setOrder(
                'quotation_quote_section_items.sort_order',
                \Magento\Framework\Data\Collection::SORT_ORDER_ASC
            );
            $this->extensionAttributesJoinProcessor->process($this->_items);
            $this->_items->setQuote($this);
        }
        return parent::getItemsCollection($useCache);
		}
	}
    /**
     * Retrieve label of quote status
     *
     * @return string
     */
    private function getStatusLabel()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getConfig()->getStatusLabel($this->getStatus());
		}
	}
    /**
     * Get status
     *
     * @return string
     */
    private function getStatus()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::STATUS);
		}
	}
    /**
     * Retrieve label of quote status
     *
     * @return string
     */
    private function getStateLabel()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getConfig()->getStateLabel($this->getState());
		}
	}
    /**
     * Get state
     *
     * @return string
     */
    private function getState()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::STATE);
		}
	}
    /**
     * Get formatted price value including quote currency rate to quote website currency
     *
     * @param float $price
     * @param bool $addBrackets
     * @return  string
     */
    private function formatPrice($price, $addBrackets = false)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->formatPricePrecision($price, 2, $addBrackets);
		}
	}
    /**
     * Format price presision with or without brackets
     *
     * @param float $price
     * @param int $precision
     * @param bool $addBrackets
     * @return string
     */
    private function formatPricePrecision($price, $precision, $addBrackets = false)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getQuoteCurrency()->formatPrecision($price, $precision, [], true, $addBrackets);
		}
	}
    /**
     * Get currency model instance. Will be used currency with which quote placed
     *
     * @return \Magento\Directory\Model\Currency
     */
    private function getQuoteCurrency()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->_quoteCurrency === null) {
            $this->_quoteCurrency = $this->_currencyFactory->create();
            $this->_quoteCurrency->load($this->getQuoteCurrencyCode());
        }
        return $this->_quoteCurrency;
		}
	}
    /**
     * Getter for quote_currency_code
     *
     * @return string
     */
    private function getQuoteCurrencyCode()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(\Magento\Quote\Api\Data\CurrencyInterface::KEY_QUOTE_CURRENCY_CODE);
		}
	}
    /**
     * Format base price
     *
     * @param float $price
     * @return string
     */
    private function formatBasePrice($price)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->formatBasePricePrecision($price, 2);
		}
	}
    /**
     * Format base price prescision
     *
     * @param float $price
     * @param int $precision
     * @return string
     */
    private function formatBasePricePrecision($price, $precision)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getBaseCurrency()->formatPrecision($price, $precision);
		}
	}
    /**
     * Retrieve order website currency for working with base prices
     *
     * @return \Magento\Directory\Model\Currency
     */
    private function getBaseCurrency()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->_baseCurrency === null) {
            $this->_baseCurrency = $this->_currencyFactory->create()->load($this->getBaseCurrencyCode());
        }
        return $this->_baseCurrency;
		}
	}
    /**
     * Retrieve passed currency for working with different currencies
     *
     * @param string $currency
     * @return \Magento\Directory\Model\Currency|\Magento\Quote\Model\Cart\Currency
     */
    private function getAnyCurrency($currency)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->anyCurrency === null) {
            $this->anyCurrency = $this->_currencyFactory->create()->load($currency);
        }
        return $this->anyCurrency;
		}
	}
    /**
     * Reset the quote currency to the current quote currency
     *
     * @return $this
     */
    private function resetQuoteCurrency()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_quoteCurrency = $this->_currencyFactory->create();
        $this->_quoteCurrency->load($this->getQuoteCurrencyCode());
        return $this;
		}
	}
    /**
     * Retrieve text formatted price value including quote rate
     *
     * @param float $price
     * @return  string
     */
    private function formatPriceTxt($price)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getQuoteCurrency()->formatTxt($price);
		}
	}
    /**
     * Get customer name (by adding the first and last name togetter
     * - TODO: add midlename if available?
     *
     * @return string
     */
    private function getCustomerName()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->getCustomerFirstname()) {
            $customerName = $this->getCustomerFirstname() . ' ' . $this->getCustomerLastname();
        } else {
            $customerName = (string)__('Guest');
        }
        return $customerName;
		}
	}
    /**
     * Get formatted quote created date in store timezone
     *
     * @param string $format date format type (short|medium|long|full)
     * @return  string
     */
    private function getCreatedAtFormatted($format)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->_session->getQuote();
        //workaround for Magento 2.3.4 and newer? date print issue when date include time
        $createdAtDate = explode(" ", $quote->getQuotationCreatedAt());
        if (!empty($createdAtDate)) {
            $createdAtDate = $createdAtDate[0];
        } else {
            $createdAtDate = null;
        }
        return $this->timezone->formatDate(
            $this->timezone->scopeDate(
                $this->getStore(),
                $createdAtDate,
                true
            ),
            $format,
            true
        );
		}
	}
    /**
     * Get customer note if getCustomerNoteNotify returns true
     *
     * @return string
     */
    private function getEmailCustomerNote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->getCustomerNoteNotify()) {
            return $this->getCustomerNote();
        }
        return '';
		}
	}
    /**
     * Get formated expiry date
     *
     * @return string
     */
    private function getExpiryDateString()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->getExpiryEnabled()) {
            $expiryDateString = '(' . __('valid until ') . $this->getExpiryDateFormatted(2) . ')';
            return $expiryDateString;
        }
        return '';
		}
	}
    /**
     * Get formatted quote expiry date
     *
     * @param string $format date format type (short|medium|long|full)
     * @return  string
     */
    private function getExpiryDateFormatted($format)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->timezone->formatDate($this->getExpiryDate(), $format, false);
		}
	}
    /**
     * Sets the increment ID for the quote.
     *
     * @param string $id
     * @return $this
     */
    private function setIncrementId($id)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::INCREMENT_ID, $id);
		}
	}
    /**
     * Sets the proposal sent for the quote.
     *
     * @param string $timestamp
     * @return $this
     */
    private function setProposalSent($timestamp)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setData(self::PROPOSAL_SENT, $timestamp);
		}
	}
    /**
     * Function to check whether the quote can be accepted based on its state and status
     *
     * @return bool
     */
    private function canAccept()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$state = $this->getState();
        $status = $this->getStatus();
        return $this->_statusObject->canAccept($state, $status);
		}
	}
    /**
     * Function to check whether the quote can show prices based on its state and status
     *
     * @return bool
     */
    private function showPrices()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$state = $this->getState();
        $status = $this->getStatus();
        return $this->_statusObject->showPrices($state, $status);
		}
	}
    /**
     * Get Base Customer Price Total
     *
     * @return float
     */
    private function getBaseCustomPriceTotal()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::BASE_CUSTOM_PRICE_TOTAL);
		}
	}
    /**
     * Get Customer Price Total
     *
     * @return float
     */
    private function getCustomPriceTotal()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::CUSTOM_PRICE_TOTAL);
		}
	}
    /**
     * Get Quote Adjustment
     *
     * @return float
     */
    private function getQuoteAdjustment()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::QUOTE_ADJUSTMENT);
		}
	}
    /**
     * Get Base Quote Adjustment
     *
     * @return float
     */
    private function getBaseQuoteAdjustment()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::BASE_QUOTE_ADJUSTMENT);
		}
	}
    /**
     * Return quote entity type
     *
     * @return string
     */
    private function getEntityType()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return self::ENTITY;
		}
	}
    /**
     * Get increment id
     *
     * @return string
     */
    private function getIncrementId()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->getData(self::INCREMENT_ID);
		}
	}
    /**
     * Function that gets a hash to use in a url (for autologin urls)
     *
     * @return string
     */
    private function getUrlHash()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->getHash() == "") {
            $hash = $this->getRandomHash();
            $this->setHash($hash);
            $this->save();
        }
        $hash = sha1($this->getCustomerEmail() . $this->getHash() . $this->getPasswordHash());
        return $hash;
		}
	}
    /**
     * Function that generates a random hash of a given length
     *
     * @param int $length
     * @return string
     * @throws \Exception
     */
    private function getRandomHash($length = 40)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$max = ceil($length / 40);
        $random = '';
        for ($i = 0; $i < $max; $i++) {
            $random .= sha1(microtime(true) . \random_int(10000, 90000));
        }
        return substr($random, 0, $length);
		}
	}
    /**
     * Concert a price to the quote base rate price
     * - Magento does not come with a currency conversion via the quote rates, only the active rates.
     *
     * @param int|float $price
     * @return double
     */
    private function convertPriceToQuoteBaseCurrency($price)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->isCurrencyDifferent()) {
            $price = \Cart2Quote\Quotation\Model\Quote::convertRate($price, $this->getBaseToQuoteRate(), true);
        }
        return $price;
		}
	}
    /**
     * Convert shipping price to quote base / currency amount
     *
     * @param int|float $price
     * @param bool $base
     * @return float
     */
    private function convertShippingPrice($price, $base)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$baseToQuoteRate = $this->getBaseToQuoteRate();
        if (isset($baseToQuoteRate) && $baseToQuoteRate > 0) {
            $price = \Cart2Quote\Quotation\Model\Quote::convertRate($price, $baseToQuoteRate, $base);
        }
        return $price;
		}
	}
    /**
     * Get total item qty
     *
     * @return int
     */
    private function getTotalItemQty()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$itemsQty = 0;
        foreach ($this->getAllVisibleItems() as $item) {
            $itemQty = $item->getQty();
            $itemsQty += $itemQty;
        }
        return $itemsQty;
		}
	}
    /**
     * Get sections
     *
     * @param array $unassignedData
     * @return array
     */
    private function getSections($unassignedData = [])
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$sections = [];
        $extensionAttributes = $this->getExtensionAttributes();
        if ($extensionAttributes !== null) {
            $sections = $extensionAttributes->getSections();
            if (!$sections) {
                return [];
            }
            foreach ($sections as $section) {
                if ($section->getIsUnassigned()) {
                    $section->setData(array_merge($section->getData(), $unassignedData));
                }
            }
            foreach($this->getAllVisibleItems() as $item){
                $this->assignToSection($item);
            }
            usort($sections, [$this, 'sort']);
        }
        return $sections;
		}
	}
    /**
     * Get section items for a given section id
     *
     * @param int $sectionId
     * @return array
     */
    private function getSectionItems($sectionId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$items = [];
        //presort on itemId
        $visibleItems = $this->getAllVisibleItems();
        usort($visibleItems, [$this, 'sortItemId']);
        /**
         * @var \Magento\Quote\Api\Data\CartItemInterface $item
         */
        foreach ($visibleItems as $item) {
            if ($item->getExtensionAttributes()->getSection()->getSectionId() == $sectionId) {
                $items[] = $item;
            }
        }
        usort($items, [$this, 'sort']);
        return $items;
		}
	}
    /**
     * Init resource model
     *
     * @return void
     */
    private function _construct()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_init(\Cart2Quote\Quotation\Model\ResourceModel\Quote::class);
		}
	}
    /**
     * Sort the quote items to the itemId
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $compare
     * @param \Magento\Quote\Api\Data\CartItemInterface $to
     * @return int
     */
    private function sortItemId($compare, $to)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$compareItem = $compare->getItemId();
        $toItem = $to->getItemId();
        return $compareItem <=> $toItem;
		}
	}
    /**
     * Sort the quote cart to a given order
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Section|\Magento\Quote\Api\Data\CartItemInterface $compare
     * @param \Cart2Quote\Quotation\Model\Quote\Section|\Magento\Quote\Api\Data\CartItemInterface $to
     * @return int
     */
    private function sort($compare, $to)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($compare instanceof \Magento\Quote\Api\Data\CartItemInterface
            && $to instanceof \Magento\Quote\Api\Data\CartItemInterface) {
            $compareSortOrder = $compare->getExtensionAttributes()->getSection()->getSortOrder();
            $toSortOrder = $to->getExtensionAttributes()->getSection()->getSortOrder();
        } else {
            $compareSortOrder = $compare->getSortOrder();
            $toSortOrder = $to->getSortOrder();
        }
        return $compareSortOrder <=> $toSortOrder;
		}
	}
    /**
     * Check if the Quote has Optional Items
     *
     * @return bool
     */
    private function hasOptionalItems()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getCurrentTierItem() && $item->getCurrentTierItem()->getMakeOptional()) {
                return true;
            }
        }
        return false;
		}
	}
    /**
     * Getter for the extention attributes
     * This is a fix for M2.1 and M2.2 as they can have ExtensionAttributes set to null
     *
     * @return \Magento\Quote\Api\Data\CartExtensionInterface|null
     */
    private function getExtensionAttributes()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->getData(self::EXTENSION_ATTRIBUTES_KEY)) {
            $extensionAttributes = $this->extensionAttributesFactory->create(get_class($this), []);
            $this->_data[self::EXTENSION_ATTRIBUTES_KEY] = $extensionAttributes;
        }
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
		}
	}
    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    private function assignToSection(\Magento\Quote\Model\Quote\Item $item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$itemSection = $this->itemSectionProvider->getSection($item->getId());
        if ($itemSection->getItemId() && !$itemSection->getSectionId()) {
            /**
             * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Section\Collection $sectionCollection
             */
            $sectionCollection = $this->sectionCollectionFactory->create();
            $unassignedId = $sectionCollection->getUnassignedSectionIdForQuote($this->getId());
            if ($unassignedId) {
                $itemSection->setSectionId($unassignedId);
                $this->sectionItemResourceModel->save($itemSection);
                $item->getExtensionAttributes()->getSection()->setSectionId($unassignedId);
                $item->getExtensionAttributes()->getSection()->setSortOrder("0");
            }
        }
		}
	}
    /**
     * Define customer object
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return $this
     */
    private function setCustomer(\Magento\Customer\Api\Data\CustomerInterface $customer = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_customer = $customer;
        $this->setCustomerId($customer->getId());
        //only reset data on non-guest quotes
        if ($customer->getId()) {
            /* @TODO: Remove the method after all external usages are refactored in MAGETWO-19930 */
            return parent::setCustomer($customer);
        }
        return $this;
		}
	}
    /**
     * Getter for the customer email
     * @return string
     */
    private function getCustomerEmail()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$customerEmail = $this->getData('customer_email');
        try {
            //fallbacks for missing emails
            //start with billing address email
            if (!$customerEmail) {
                $billingAddress = $this->getBillingAddress();
                $customerEmail = $billingAddress->getData('email'); //dont use ->getEmail() to avoid infinite loop
            }
            //if we don't have a billing address, try the shipping address
            if (!$customerEmail) {
                $shippingAddress = $this->getShippingAddress();
                $customerEmail = $shippingAddress->getData('email'); //dont use ->getEmail() to avoid infinite loop
            }
            //at last try tho find a customer
            if (!$customerEmail && $this->getCustomerId()) {
                $customerEmail = $this->getCustomer()->getEmail();
            }
        } catch (\Exception $exception) {
            //fallback failed, do nothing
        }
        return $customerEmail;
		}
	}
}
