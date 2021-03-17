<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Controller\Quote\Ajax;
/**
 * Trait UpdateQuote
 *
 * @package Cart2Quote\Quotation\Controller\Quote
 */
trait UpdateQuote
{
    /**
     * Update customer's quote
     *
     * @return void
     */
    private function processAction()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			try {
            $this->updateFields();
            $this->updateQuotationProductData();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
		}
	}
    /**
     * Update the quotation fields
     *
     * @return void
     */
    private function updateFields()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_eventManager->dispatch(
            'quotation_controller_before_update_quote',
            [
                'quote' => $this->quoteSession->getQuote(),
                'action' => $this
            ]
        );
        $payload = json_decode($this->getRequest()->getContent());
        $this->quoteSession->addGuestFieldData(json_decode(
            $payload->{\Cart2Quote\Quotation\Model\Session::QUOTATION_GUEST_FIELD_DATA},
            true
        ));
        $this->quoteSession->addConfigData(json_decode(
            $payload->{\Cart2Quote\Quotation\Model\Session::QUOTATION_STORE_CONFIG_DATA},
            true
        ));
        $this->quoteSession->addFieldData(json_decode(
            $payload->{\Cart2Quote\Quotation\Model\Session::QUOTATION_FIELD_DATA},
            true
        ));
        $this->quoteSession->addProductData(json_decode(
            $payload->{\Cart2Quote\Quotation\Model\Session::QUOTATION_PRODUCT_DATA},
            true
        ));
        $this->_eventManager->dispatch(
            'quotation_controller_after_update_quote',
            [
                'quote' => $this->quoteSession->getQuote(),
                'action' => $this
            ]
        );
		}
	}
}
