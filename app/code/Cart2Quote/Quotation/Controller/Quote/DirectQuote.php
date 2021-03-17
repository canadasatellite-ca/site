<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote;

/**
 * Class DirectQuote
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class DirectQuote extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Checkout\Model\Type\Onepage
     */
    private $createQuote;

    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    private $quotationFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender
     */
    private $sender;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * DirectQuote constructor.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory
     * @param \Magento\Checkout\Model\Type\Onepage $createQuote
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Psr\Log\LoggerInterface $logger,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender,
        \Cart2Quote\Quotation\Model\QuoteFactory $quotationFactory,
        \Magento\Checkout\Model\Type\Onepage $createQuote,
        \Cart2Quote\Quotation\Helper\Data $helper,
        \Magento\Framework\App\Action\Context $context
    ) {
        parent::__construct($context);

        $this->createQuote = $createQuote;
        $this->quotationFactory = $quotationFactory;
        $this->sender = $sender;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->helper = $helper;
    }

    /**
     * Execute (controller entrypoint)
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $quote = $this->createQuote->getQuote();
            $this->helper->isDirectQuoteAllowed($quote);

            if ($this->customerSession->isLoggedIn()) {
                $quote->setCustomer($this->customerRepository->getById($this->customerSession->getCustomerId()));

                $quoteModel = $this->quotationFactory->create();
                $quotation = $quoteModel->create($quote)->load($quote->getId());
                $quotation->saveQuote();

                $this->sender->send($quotation);
                $this->_eventManager->dispatch(
                    'quotation_frontend_create_direct_quote_after',
                    ['quote' => $quotation]
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->logger->error($exception);
            $this->messageManager->addErrorMessage($exception->getMessage());

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRefererUrl());
        }

        return $this->resultRedirectFactory->create()->setUrl(
            $this->_url->getUrl('quotation/quote/success', ['id' => $quotation->getId()])
        );
    }
}