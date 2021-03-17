<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote\Request;

/**
 * Class Quickquote
 *
 * @package Cart2Quote\Quotation\Controller\Quote\Request
 */
class QuickQuote extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\CreateQuote
     */
    private $createQuote;

    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    private $quotationFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    private $quoteSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender
     */
    private $sender;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Cart2Quote\Quotation\Helper\Cloning
     */
    private $cloningHelper;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    private $helper;

    /**
     * QuickQuote constructor.
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory
     * @param \Cart2Quote\Quotation\Model\Quote\CreateQuote $createQuote
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Cart2Quote\Quotation\Helper\Cloning $cloningHelper
     * @param \Cart2Quote\Quotation\Helper\Data $helper
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory,
        \Cart2Quote\Quotation\Model\Quote\CreateQuote $createQuote,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Cart2Quote\Quotation\Helper\Cloning $cloningHelper,
        \Cart2Quote\Quotation\Helper\Data $helper,
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);

        $this->checkoutSession = $checkoutSession;
        $this->createQuote = $createQuote;
        $this->quotationFactory = $quotationFactory;
        $this->quoteSession = $quoteSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->sender = $sender;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->cloningHelper = $cloningHelper;
        $this->helper = $helper;
    }

    /**
     * Execute (controller entrypoint)
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setRefererUrl();
        }
        try {
            $quote = $this->createQuote->getQuote();

            if ($this->customerSession->isLoggedIn()) {
                $quote->setCustomer($this->customerRepository->getById($this->customerSession->getCustomerId()));
            } else {
                $email = $this->getRequest()->getParam('email', null);
                $firstname = $this->getRequest()->getParam('firstname', null);
                $lastname = $this->getRequest()->getParam('lastname', null);

                if (!isset($email, $firstname, $lastname)) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Missing required parameters.')
                    );
                }

                $quote->setCustomerEmail($email);
                $quote->setCustomerFirstname($firstname);
                $quote->setCustomerLastname($lastname);
                $quote->setCustomerIsGuest(true);
                $quote->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
            }

            $customerNote = $this->getRequest()->getParam('remarks', null);
            $quote->setCustomerNote($customerNote);

            $quoteModel = $this->quotationFactory->create();
            $quotation = $quoteModel->create($quote)->load($quote->getId());
            $quotation->saveQuote();

            //Duplicate quote for Frontend Quote Changes Visibility feature
            if ($this->helper->quoteChangesVisibility()) {
                $this->cloningHelper->createOriginalQuote($quotation);
            }

            $this->sender->send($quotation);
            $this->_eventManager->dispatch(
                'quotation_event_after_quick_quote_request',
                ['quote' => $quotation]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->logger->error($exception);
            $this->messageManager->addErrorMessage(
                __('Something went wrong while processing your quote. Please try again later.')
            );

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }

        return $this->resultRedirectFactory->create()->setUrl(
            $this->_url->getUrl('quotation/quote/success', ['id' => $quotation->getId()])
        );
    }
}
