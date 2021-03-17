<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Email\Sender;
/**
 * Trait Sender
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email\Sender
 */
trait Sender
{
    /**
     * Get Sender email identifier
     *
     * @return string
     */
    private function getSendEmailIdentifier()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->sendEmailIdentifier;
		}
	}
    /**
     * Get email sender identifier
     *
     * @return string
     */
    private function getEmailSentIdentifier()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->emailSentIdentifier;
		}
	}
    /**
     * Sends quote request email to the customer.
     * - Email will be sent immediately in two cases:
     * - - if asynchronous email sending is disabled in global settings
     * - - if $forceSyncMode parameter is set to TRUE
     * - Otherwise, email will be sent later during running of
     * - corresponding cron job.
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param bool $forceSyncMode
     * @return bool
     */
    private function send(\Cart2Quote\Quotation\Model\Quote $quote, $forceSyncMode = false)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote->setData($this->sendEmailIdentifier, true);
        $quote->setData($this->emailSentIdentifier, null);
        if (!$this->globalConfig->getValue('sales_email/general/async_sending') || $forceSyncMode) {
            if ($this->checkAndSend($quote)) {
                $quote->setData($this->emailSentIdentifier, true);
                $quote->save();
                return true;
            } else {
                $quote->save();
                //return false when check and send returns false
                return false;
            }
        }
        $quote->save();
        //always return true on async sends
        return true;
		}
	}
    /**
     * Prepare email template with variables
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return void
     */
    private function prepareTemplate(\Cart2Quote\Quotation\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$transport = [
            'quote' => $quote,
            'billing' => $quote->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($quote),
            'store' => $quote->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($quote),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($quote),
            'attach_pdf' => $quote->getAttachPdf(),
            'attach_doc' => $quote->getAttachDoc(),
            'quote_data' => [
                'quote_id' => (string)$quote->getQuoteId(),
                'customer_name' => (string)$quote->getCustomerName(),
                'expiry_date_string' => (string)$quote->getExpiryDateString(),
                'shipping_method' => (string)$quote->getShippingMethod(),
                'customer_note' => (string)$quote->getCustomerNote(),
                'email_customer_note' => (string)$quote->getEmailCustomerNote(),
                'shipping_description' => (string)$quote->getShippingDescription(),
                'quotation_created_at' => (string)$quote->getQuotationCreatedAt(),
                'created_at_formatted' => (string)$quote->getCreatedAtFormatted(1),
                'expiry_date_formatted' => (string)$quote->getExpiryDateFormatted(1),
            ],
            'reciever_name' => $this->identityContainer->getRecieverName(),
        ];
        //legacy support for older M2 version like 2.2.5
        foreach ($transport['quote_data'] as $key => $value) {
            $transport['quote_data' . '_' . $key] = $value;
        }
        if (!$quote->getCustomerIsGuest()) {
            $transport['customer_has_account'] = true;
        }
        $this->eventManager->dispatch(
            'email_quote_set_template_vars_before',
            ['sender' => $this, 'transport' => $transport]
        );
        $this->templateContainer->setTemplateVars($transport);
        parent::prepareTemplate($quote);
		}
	}
    /**
     * Get payment info block as html
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return string
     */
    private function getPaymentHtml(\Cart2Quote\Quotation\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return '';
		}
	}
}
