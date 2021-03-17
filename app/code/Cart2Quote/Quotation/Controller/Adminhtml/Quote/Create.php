<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action;

/**
 * Class Create
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Create extends \Magento\Sales\Controller\Adminhtml\Order\Create\Save
{
    /**
     * @var \Cart2Quote\Quotation\Model\Admin\Quote\EmailSender
     */
    protected $emailSender;

    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var \Magento\Quote\Model\CustomerManagement
     */
    protected $customerManagement;

    /**
     * @var array
     */
    protected $_errors;

    /**
     * @var \Cart2Quote\Quotation\Model\Admin\Quote\QuoteCreator
     */
    protected $quoteCreator;

    /**
     * Create constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Cart2Quote\Quotation\Model\Admin\Quote\EmailSender $emailSender
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Quote\Model\CustomerManagement $customerManagement
     * @param \Cart2Quote\Quotation\Model\Admin\Quote\QuoteCreator $quoteCreator
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Cart2Quote\Quotation\Model\Admin\Quote\EmailSender $emailSender,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Quote\Model\CustomerManagement $customerManagement,
        \Cart2Quote\Quotation\Model\Admin\Quote\QuoteCreator $quoteCreator
    ) {
        $this->emailSender = $emailSender;
        $this->quoteFactory = $quoteFactory;
        $this->_customerFactory = $customerFactory;
        $this->_addressFactory = $addressFactory;
        $this->customerManagement = $customerManagement;
        $this->quoteCreator = $quoteCreator;

        parent::__construct(
            $context,
            $productHelper,
            $escaper,
            $resultPageFactory,
            $resultForwardFactory
        );
    }

    /**
     * Saving quote and create quotation
     *
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     * Based on: \Magento\Sales\Controller\Adminhtml\Order\Create\Save::execute
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            // check if the creation of a new customer is allowed
            if (!$this->_authorization->isAllowed('Magento_Customer::manage')
                && !$this->_getSession()->getCustomerId()
                && !$this->_getSession()->getQuote()->getCustomerIsGuest()
            ) {
                return $this->resultForwardFactory->create()->forward('denied');
            }

            $this->_getOrderCreateModel()->getQuote()->setCustomerId($this->_getSession()->getCustomerId());
            $this->_processActionData('save');
            $paymentData = $this->getRequest()->getPost('payment');
            if ($paymentData) {
                $paymentData['checks'] = [
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_INTERNAL,
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL,
                ];
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
            }

            //validate the quotedata
            $this->_validate();

            //prepare the quote
            $quoteCreateModel = $this->_getOrderCreateModel()
                ->setIsValidate(true)
                ->importPostData($this->getRequest()->getPost('order'));

            //first unset customer is guest before preparing the customer
            // at this point the customer is created in the backend so it can't be a guest
            $quoteCreateModel->getQuote()->setCustomerIsGuest("0");

            //prepare the customer data
            $quoteCreateModel->_prepareCustomer();

            $quote = $quoteCreateModel->getQuote();
            $customer = $quote->getCustomer();
            if ($customer) {
                if ($customer->getId() == null) {
                    //New customer gets created
                    //Customer registration email is also sent by this function
                    $this->customerManagement->populateCustomerInfo($quote);
                    $quoteCreateModel->getQuote()->updateCustomerData($quoteCreateModel->getQuote()->getCustomer());
                }
            }
            //save the quote
            $quote = $quoteCreateModel->saveQuote();

            //get quote id
            $quoteId = $quote->getQuote()->getId();

            //load quote based on quote id (to check later if it already exists)
            $quotation = $this->quoteFactory->create()->load($quoteId);

            //create the quotation quote if it doesn't already exist
            if (!$quotation->getId()) {
                /** @var $quoteCreateModel \Cart2Quote\Quotation\Model\Quote */
                $quoteCreateModel = $this->quoteFactory->create();
                $quotation = $quoteCreateModel->create($quote->getQuote())->load($quoteId);
                $quotation->setQuotationCreatedBy($this->quoteCreator->getQuoteCreator($quotation));
                $quotation->save();
                $this->_getSession()->clearStorage();
                $this->messageManager->addSuccess(__('You created the quote.'));
                $this->_eventManager->dispatch('admin_quotation_quote_create_after', ['quote' => $quotation]);
            } else {
                $this->_getSession()->clearStorage();
                $this->messageManager->addSuccess(__('You updated the quote.'));
            }

            $this->_getSession()->clearStorage();

            if ($this->_authorization->isAllowed('Cart2Quote_Quotation::actions_view')) {
                $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $quotation->getId()]);
            } else {
                $resultRedirect->setPath('quotation/quote/index');
            }
        } catch (\Magento\Framework\Exception\PaymentException $e) {
            $this->_getOrderCreateModel()->saveQuote();
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('quotation/quote_create');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('quotation/quote_create');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Quote saving error: %1', $e->getMessage()));
            $resultRedirect->setPath('quotation/quote_create');
        }

        return $resultRedirect;
    }

    /**
     * Validate
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _validate()
    {
        $customerId = $this->_getOrderCreateModel()->getSession()->getCustomerId();
        if ($customerId === null) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please select a customer'));
        }

        if (!$this->_getOrderCreateModel()->getSession()->getStore()->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please select a store'));
        }

        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                $this->messageManager->addError($error);
            }
            throw new \Magento\Framework\Exception\LocalizedException(__('Validation is failed.'));
        }

        return $this;
    }
}
