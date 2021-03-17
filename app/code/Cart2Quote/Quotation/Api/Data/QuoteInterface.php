<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Api\Data;

/**
 * Quote interface.
 * Interface QuoteInterface
 * @package Cart2Quote\Quotation\Api\Data
 */
interface QuoteInterface
{
    /**
     * State
     */
    const STATE = 'state';

    /**
     * Status
     */
    const STATUS = 'status';

    /**
     * Increment id
     */
    const INCREMENT_ID = 'increment_id';

    /**
     * Proposal sent
     */
    const PROPOSAL_SENT = 'proposal_sent';

    /**
     * Send request email
     */
    const SEND_REQUEST_EMAIL = 'send_request_email';

    /**
     * Request email sent
     */
    const REQUEST_EMAIL_SENT = 'request_email_sent';

    /**
     * Send quote edited email
     */
    const SEND_QUOTE_EDITED_EMAIL = 'send_quote_edited_email';

    /**
     * Quote edited email sent
     */
    const QUOTE_EDITED_EMAIL_SENT = 'quote_edited_email_sent';

    /**
     * Send quote canceled email
     */
    const SEND_QUOTE_CANCELED_EMAIL = 'send_quote_canceled_email';

    /**
     * Quote canceled email sent
     */
    const QUOTE_CANCELED_EMAIL_SENT = 'quote_canceled_email_sent';

    /**
     * Send proposal accepted email
     */
    const SEND_PROPOSAL_ACCEPTED_EMAIL = 'send_proposal_accepted_email';

    /**
     * Proposa accepted email sent
     */
    const PROPOSAL_ACCEPTED_EMAIL_SENT = 'proposal_accepted_email_sent';

    /**
     * Send Proposal Rejected Email
     */
    const SEND_PROPOSAL_REJECTED_EMAIL = 'send_proposal_rejected_email';

    /**
     * Proposal Rejected Email Sent
     */
    const PROPOSAL_REJECTED_EMAIL_SENT = 'proposal_rejected_email_sent';

    /**
     * Send proposal expired email
     */
    const SEND_PROPOSAL_EXPIRED_EMAIL = 'send_proposal_expired_email';

    /**
     * Proposal expired email sent
     */
    const PROPOSAL_EXPIRED_EMAIL_SENT = 'proposal_expired_email_sent';

    /**
     * Send proposal email
     */
    const SEND_PROPOSAL_EMAIL = 'send_proposal_email';

    /**
     * Proposal email sent
     */
    const PROPOSAL_EMAIL_SENT = 'proposal_email_sent';

    /**
     * Send reminder email
     */
    const SEND_REMINDER_EMAIL = 'send_reminder_email';

    /**
     * Reminder email sent
     */
    const REMINDER_EMAIL_SENT = 'reminder_email_sent';

    /**
     * Original Base Subtotal
     */
    const ORIGINAL_BASE_SUBTOTAL = 'base_original_subtotal';

    /**
     * Original Subtotal
     */
    const ORIGINAL_SUBTOTAL = 'original_subtotal';

    /**
     * Original subtotal incl. tax
     */
    const ORIGINAL_SUBTOTAL_INCL_TAX = 'original_subtotal_incl_tax';

    /**
     * Base original subtotal incl. tax
     */
    const BASE_ORIGINAL_SUBTOTAL_INCL_TAX = 'base_original_subtotal_incl_tax';

    /**
     * Base Custom Price Total
     */
    const BASE_CUSTOM_PRICE_TOTAL = 'base_custom_price_total';

    /**
     * Custom Price Total
     */
    const CUSTOM_PRICE_TOTAL = 'custom_price_total';

    /**
     * Base Quote Adjustment
     */
    const BASE_QUOTE_ADJUSTMENT = 'base_quote_adjustment';

    /**
     * Quote Adjustment
     */
    const QUOTE_ADJUSTMENT = 'quote_adjustment';

    /**
     * Fixed Shipping Price
     */
    const FIXED_SHIPPING_PRICE = 'fixed_shipping_price';

    /**
     * Get quote ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set quote ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Returns information about the customer who is assigned to the quote.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface Information about the customer who is assigned to the quote.
     */
    public function getCustomer();

    /**
     * Get Increment id
     *
     * @return string
     */
    public function getIncrementId();

    /**
     * Sets the increment ID for the quote.
     *
     * @param string $id
     * @return $this
     */
    public function setIncrementId($id);

    /**
     * Get state
     *
     * @return string
     */
    public function getState();

    /**
     * Set state
     *
     * @param string $state
     * @return $this
     */
    public function setState($state);

    /**
     * Get state label
     *
     * @return string
     */
    public function getStateLabel();

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get status label
     *
     * @return string
     */
    public function getStatusLabel();

    /**
     * Sets the proposal sent for the quote.
     *
     * @param string $timestamp
     * @return $this
     */
    public function setProposalSent($timestamp);

    /**
     * Get proposal sent
     *
     * @return string
     */
    public function getProposalSent();

    /**
     * Get Original Subtotal
     *
     * @return float
     */
    public function getOriginalSubtotal();

    /**
     * Set Original Subtotal
     *
     * @param float $originalSubtotal
     * @return $this
     */
    public function setOriginalSubtotal($originalSubtotal);

    /**
     * Set base original subtotal inlcuding tax
     *
     * @param float $originalBaseSubtotalInclTax
     * @return $this
     */
    public function setBaseOriginalSubtotalInclTax($originalBaseSubtotalInclTax);

    /**
     * Set original subtotal including tax
     *
     * @param float $originalBaseSubtotalInclTax
     * @return $this
     */
    public function setOriginalSubtotalInclTax($originalBaseSubtotalInclTax);

    /**
     * Get Base
     *
     * @return float
     */
    public function getBaseOriginalSubtotalInclTax();

    /**
     * Get original subtotal including tax
     *
     * @return float
     */
    public function getOriginalSubtotalInclTax();

    /**
     * Get Base Original Subtotal
     *
     * @return float
     */
    public function getBaseOriginalSubtotal();

    /**
     * Set Base Original Subtotal
     *
     * @param float $originalBaseSubtotal
     * @return $this
     */
    public function setBaseOriginalSubtotal($originalBaseSubtotal);

    /**
     * Get Base Customer Price Total
     *
     * @return float
     */
    public function getBaseCustomPriceTotal();

    /**
     * Set Base Custom Price Total
     *
     * @param float $baseCustomPriceTotal
     * @return $this
     */
    public function setBaseCustomPriceTotal($baseCustomPriceTotal);

    /**
     * Get Customer Price Total
     *
     * @return float
     */
    public function getCustomPriceTotal();

    /**
     * Set Custom Price Total
     *
     * @param float $customPriceTotal
     * @return $this
     */
    public function setCustomPriceTotal($customPriceTotal);

    /**
     * Get Base Quote Adjustment
     *
     * @return float
     */
    public function getBaseQuoteAdjustment();

    /**
     * Set Base Quote Adjustment
     *
     * @param float $baseQuoteAdjustment
     * @return $this
     */
    public function setBaseQuoteAdjustment($baseQuoteAdjustment);

    /**
     * Get Quote Adjustment
     *
     * @return float
     */
    public function getQuoteAdjustment();

    /**
     * Set Quote Adjustment
     *
     * @param float $quoteAdjustment
     * @return $this
     */
    public function setQuoteAdjustment($quoteAdjustment);

    /**
     * Set send request email
     *
     * @param boolean $sendRequestEmail
     * @return $this
     */
    public function setSendRequestEmail($sendRequestEmail);

    /**
     * Get Send request email
     *
     * @return boolean
     */
    public function getSendRequestEmail();

    /**
     * Set request email sent
     *
     * @param boolean $requestEmailSent
     * @return $this
     */
    public function setRequestEmailSent($requestEmailSent);

    /**
     * Get request email sent
     *
     * @return boolean
     */
    public function getRequestEmailSent();

    /**
     * Set send quote canceled email
     *
     * @param boolean $sendQuoteCanceledEmail
     * @return $this
     */
    public function setSendQuoteCanceledEmail($sendQuoteCanceledEmail);

    /**
     * Get send quote canceled email
     *
     * @return boolean
     */
    public function getSendQuoteCanceledEmail();

    /**
     * Set quote canceled email sent
     *
     * @param boolean $quoteCanceledEmailSent
     * @return $this
     */
    public function setQuoteCanceledEmailSent($quoteCanceledEmailSent);

    /**
     * Get quote canceled email sent
     *
     * @return boolean
     */
    public function getQuoteCanceledEmailSent();

    /**
     * Set send quote edited email
     *
     * @param boolean $sendQuoteEditedEmail
     * @return $this
     */
    public function setSendQuoteEditedEmail($sendQuoteEditedEmail);

    /**
     * Get send quote edited email
     *
     * @return boolean
     */
    public function getSendQuoteEditedEmail();

    /**
     * Set quote edited email sent
     *
     * @param boolean $quoteEditedEmailSent
     * @return $this
     */
    public function setQuoteEditedEmailSent($quoteEditedEmailSent);

    /**
     * Get quote edited email sent
     *
     * @return boolean
     */
    public function getQuoteEditedEmailSent();

    /**
     * Set send proposal accepted email
     *
     * @param boolean $sendProposalAcceptedEmail
     * @return $this
     */
    public function setSendProposalAcceptedEmail($sendProposalAcceptedEmail);

    /**
     * Get Send proposal accepted email
     *
     * @return boolean
     */
    public function getSendProposalAcceptedEmail();

    /**
     * Set proposal accepeted email sent
     *
     * @param boolean $proposalAcceptedEmailSent
     * @return $this
     */
    public function setProposalAcceptedEmailSent($proposalAcceptedEmailSent);

    /**
     * Get proposal accepted email sent
     *
     * @return boolean
     */
    public function getProposalAcceptedEmailSent();

    /**
     * Set send proposal expired email
     *
     * @param boolean $sendProposalExpiredEmail
     * @return $this
     */
    public function setSendProposalExpiredEmail($sendProposalExpiredEmail);

    /**
     * Get send proposal expired email
     *
     * @return boolean
     */
    public function getSendProposalExpiredEmail();

    /**
     * Set proposal expired email sent
     *
     * @param boolean $proposalExpiredEmailSent
     * @return $this
     */
    public function setProposalExpiredEmailSent($proposalExpiredEmailSent);

    /**
     * Get proposal expired email sent
     *
     * @return boolean
     */
    public function getProposalExpiredEmailSent();

    /**
     * Set send proposal email
     *
     * @param boolean $sendProposalEmail
     * @return $this
     */
    public function setSendProposalEmail($sendProposalEmail);

    /**
     * Get send proposal email
     *
     * @return boolean
     */
    public function getSendProposalEmail();

    /**
     * Set proposal email sent
     *
     * @param boolean $proposalEmailSent
     * @return $this
     */
    public function setProposalEmailSent($proposalEmailSent);

    /**
     * Get proposal email sent
     *
     * @return boolean
     */
    public function getProposalEmailSent();

    /**
     * Set send reminder email
     *
     * @param boolean $sendReminderEmail
     * @return $this
     */
    public function setSendReminderEmail($sendReminderEmail);

    /**
     * Get send reminder email
     *
     * @return boolean
     */
    public function getSendReminderEmail();

    /**
     * Set  reminder email sent
     *
     * @param boolean $reminderEmailSent
     * @return $this
     */
    public function setReminderEmailSent($reminderEmailSent);

    /**
     * Get reminder email sent
     *
     * @return boolean
     */
    public function getReminderEmailSent();

    /**
     * Set fixed shipping price
     *
     * @param float $fixedShippingPrice
     * @return $this
     */
    public function setFixedShippingPrice($fixedShippingPrice);

    /**
     * Get fixed shipping price
     *
     * @return float
     */
    public function getFixedShippingPrice();
}
