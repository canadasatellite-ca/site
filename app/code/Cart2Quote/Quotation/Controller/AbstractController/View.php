<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\AbstractController;

use Magento\Framework\View\Result\PageFactory;

/**
 * Class View
 *
 * @package Cart2Quote\Quotation\Controller\AbstractController
 */
abstract class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Cart2Quote\Quotation\Controller\AbstractController\QuoteLoaderInterface
     */
    protected $quoteLoader;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * View constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Cart2Quote\Quotation\Controller\AbstractController\QuoteLoaderInterface $quoteLoader
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        QuoteLoaderInterface $quoteLoader,
        PageFactory $resultPageFactory
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Quote view page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->quoteLoader->load($this->_request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        /** @var \Magento\Framework\View\Element\Html\Links $navigationBlock */
        $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('quotation/quote/history');
        }
        return $resultPage;
    }
}
