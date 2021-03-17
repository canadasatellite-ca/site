<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Controller\Quote\Ajax;
use Cart2Quote\Quotation\Model\QuotationCart as CustomerCart;
use Cart2Quote\Quotation\Model\Quote;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\Error;
use Magento\Framework\Validator\Exception as ValidatorException;
/**
 * Trait AjaxAbstract
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Ajax
 */
trait AjaxAbstract
{
    /**
     * Update quote data action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function execute()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        if (!$this->helper->isStockEnabledFrontend()) {
            $this->getOnepage()->getQuote()->setIsSuperMode(true);
            $this->getOnepage()->getQuote()->setHasError(false);
        }
        if ($this->_expireAjax()) {
            $response = $this->_ajaxRedirectResponse();
            return $response->setContents(json_encode('Session expired. Please submit your quote again.'));
        }
        $this->result = $this->dataObjectFactory->create();
        $this->_eventManager->dispatch(
            'quotation_controller_frontend_default_before',
            [
                'result' => $this->result,
                'action' => $this
            ]
        );
        $this->_eventManager->dispatch(
            sprintf('quotation_controller_frontend_%s_before', $this->getEventPrefix()),
            [
                'result' => $this->result,
                'action' => $this
            ]
        );
        $this->result->setData('success', true);
        $this->result->setData('error', false);
        try {
            $this->processAction();
        } catch (LocalizedException $exception) {
            $this->_eventManager->dispatch(
                'quotation_controller_frontend_default_localized_exception',
                [
                    'result' => $this->result,
                    'action' => $this
                ]
            );
            $this->_eventManager->dispatch(
                sprintf('quotation_controller_frontend_%s_localized_exception', $this->getEventPrefix()),
                [
                    'result' => $this->result,
                    'action' => $this
                ]
            );
            //modify error message on ajax actions when customer is logged in
            if ($this->isCustomerLoggedIn() && ($exception instanceof ValidatorException)) {
                $messages = $exception->getMessages();
                if (is_array($messages)) {
                    $newMessages = [];
                    /** @var Error $error */
                    foreach ($messages as $error) {
                        if ($error->getText()) {
                            $newMessages[] = [__($error->getText())];
                        }
                    }
                    //add extra message
                    $newMessages[] = [__(
                        'Please update your address in the customer dashboard to meet the requirements.'
                    )];
                    //recreate error
                    $exception = new ValidatorException(
                        null,
                        null,
                        $newMessages
                    );
                }
            }
            $this->logger->critical($exception);
            $this->result->setData('success', false);
            $this->result->setData('error', true);
            $this->result->setData('message', $exception->getMessage());
            $gotoSection = $this->getOnepage()->getCheckout()->getGotoSection();
            if ($gotoSection) {
                $this->result->setData('goto_section', $gotoSection);
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }
            $updateSection = $this->getOnepage()->getCheckout()->getUpdateSection();
            if ($updateSection) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $this->result->setData(
                        'update_section',
                        [
                            'name' => $updateSection,
                            'html' => $this->{$updateSectionFunction}(),
                        ]
                    );
                }
                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }
        } catch (\Exception $exception) {
            $this->_eventManager->dispatch(
                'quotation_controller_frontend_default_exception',
                [
                    'result' => $this->result,
                    'action' => $this
                ]
            );
            $this->_eventManager->dispatch(
                sprintf('quotation_controller_frontend_%s_exception', $this->getEventPrefix()),
                [
                    'result' => $this->result,
                    'action' => $this
                ]
            );
            $this->logger->critical($exception);
            $this->result->setData('success', false);
            $this->result->setData('error', true);
            $this->result->setData(
                'message',
                __('Something went wrong while processing your quote. Please try again later.')
            );
        }
        $this->_eventManager->dispatch(
            sprintf('quotation_controller_frontend_%s_after', $this->getEventPrefix()),
            [
                'result' => $this->result,
                'action' => $this
            ]
        );
        $this->_eventManager->dispatch(
            'quotation_controller_frontend_default_after',
            [
                'result' => $this->result,
                'action' => $this
            ]
        );
        return $this->resultJsonFactory->create()->setData($this->result->getData());
		}
	}
    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    private function _expireAjax()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if ($this->checkoutSession->getCartWasUpdated(true) &&
            !in_array($action, ['index', 'createQuote', 'updateQuote'])) {
            return true;
        }
        return false;
		}
	}
    /**
     * Get one page checkout model
     *
     * @return \Cart2Quote\Quotation\Model\Quote\CreateQuote
     */
    private function getOnepage()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->createQuote;
		}
	}
    /**
     * Get event prefix
     *
     * @return string
     */
    private function getEventPrefix()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return static::EVENT_PREFIX;
		}
	}
    /**
     * Overwrite this function to perform an ajax action on the RFQ page.
     *
     * @return bool
     */
    private function processAction()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return false;
		}
	}
    /**
     * Checking customer login status
     *
     * @return bool
     */
    private function isCustomerLoggedIn()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->_customerSession->isLoggedIn();
		}
	}
    /**
     * Update the fields from the quotation data on the session.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateQuotationProductData()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteProductData = $this->quoteSession->getData(
            \Cart2Quote\Quotation\Model\Session::QUOTATION_PRODUCT_DATA
        );
        if (is_array($quoteProductData)) {
            foreach ($quoteProductData as $fieldName => $productData) {
                foreach ($productData as $id => $value) {
                    $quoteItem = $this->quotationCart->getQuote()->getItemById($id);
                    if ($quoteItem) {
                        $oldQty = $quoteItem->getQty();
                        if (!$this->helper->isStockEnabledFrontend()) {
                            $quoteItem->setHasError(false);
                            $this->productHelper->setSkipSaleableCheck(true);
                        }
                        $quoteItem->setData($fieldName, strip_tags($value));
                        $buyRequest = $quoteItem->getBuyRequest();
                        $buyRequest->setData($fieldName, strip_tags($value));
                        try {
                            $alreadyExist = $this->tierItemModel->checkQtyExistTiers($id, $buyRequest->getData('qty'));
                            if ($alreadyExist) {
                                throw new \Magento\Framework\Exception\LocalizedException(__('Quantity already exist'));
                            }
                            $item = $this->quotationCart->updateItem($id, $buyRequest);
                            if ($item->getHasError()) {
                                throw new \Magento\Framework\Exception\LocalizedException(
                                    __($item->getStockStateResult()->getMessage())
                                );
                            }
                        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                            if (isset($oldQty)) {
                                $oldData['qty'][$id] = $oldQty;
                                $this->quoteSession->addProductData($oldData);
                            }
                            throw $exception;
                        }
                        if (is_string($item)) {
                            throw new \Magento\Framework\Exception\LocalizedException(__($item));
                        }
                        $this->_eventManager->dispatch(
                            'quotation_quote_update_item_complete',
                            ['item' => $item]
                        );
                    }
                }
            }
            $this->quotationCart->save();
        }
		}
	}
    /**
     * Overwrite of \Magento\Checkout\Controller\Action::_preDispatchValidateCustomer
     * - We don't need customer validation in our ajax calls, we validate the customer at a later point.
     *
     * @param bool $redirect
     * @param bool $addErrors
     * @return bool|\Magento\Framework\Controller\Result\Redirect
     */
    private function _preDispatchValidateCustomer($redirect = false, $addErrors = false)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($redirect || $addErrors) {
            return parent::_preDispatchValidateCustomer($redirect, $addErrors);
        }
        return true;
		}
	}
}
