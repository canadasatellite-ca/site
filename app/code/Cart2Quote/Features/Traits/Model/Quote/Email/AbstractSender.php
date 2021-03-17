<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Email;
use Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteSenderInterface;
/**
 * Trait AbstractSender
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email
 */
trait AbstractSender
{
    /**
     * Send the mail if this mail is enabled
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param array|null $attachments
     * @return bool
     */
    private function checkAndSend(
        \Cart2Quote\Quotation\Model\Quote $quote,
        $attachments = null
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->identityContainer->setStore($quote->getStore());
        if (!$this->identityContainer->isEnabled()) {
            return false;
        }
        $this->prepareTemplate($quote);
        /** @var SenderBuilder $sender */
        $sender = $this->getSender();
        try {
            $sender->send($attachments);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
        try {
            $sender->sendCopyTo($attachments);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
        return true;
		}
	}
    /**
     * Prepare template
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     */
    private function prepareTemplate(\Cart2Quote\Quotation\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->templateContainer->setTemplateOptions($this->getTemplateOptions());
        if (!$quote->getCustomerIsGuest()) {
            $templateId = $this->identityContainer->getTemplateId();
            $customerName = $quote->getBillingAddress()->getName();
        } else {
            $templateId = $this->identityContainer->getTemplateId();
            $customerName = $quote->getCustomerName();
        }
        $this->identityContainer->setCustomerName($customerName);
        $this->identityContainer->setCustomerEmail($quote->getCustomerEmail());
        $this->templateContainer->setTemplateId($templateId);
		}
	}
    /**
     * Get template options
     *
     * @return array
     */
    private function getTemplateOptions()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->identityContainer->getStore()->getStoreId()
        ];
		}
	}
    /**
     * Get sender object
     *
     * @return SenderBuilder|\Magento\Sales\Model\Order\Email\SenderBuilder
     */
    private function getSender()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->senderBuilderFactory->create(
            [
                'templateContainer' => $this->templateContainer,
                'identityContainer' => $this->identityContainer,
            ]
        );
		}
	}
    /**
     * Get the shipping address formated (html)
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return null|string
     */
    private function getFormattedShippingAddress(\Cart2Quote\Quotation\Model\Quote $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $quote->getIsVirtual()
            ? null
            : $this->addressRenderer->formatQuoteAddress($quote->getShippingAddress(), 'html');
		}
	}
    /**
     * Get the billing address formatted (html)
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return null|string
     */
    private function getFormattedBillingAddress($quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->addressRenderer->formatQuoteAddress($quote->getBillingAddress(), 'html');
		}
	}
}
