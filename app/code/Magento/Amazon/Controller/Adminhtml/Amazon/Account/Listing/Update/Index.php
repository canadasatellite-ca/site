<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Listing\Update;

use Magento\Amazon\Ui\FrontendUrl;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 */
class Index extends Action
{
    /** @var PageFactory */
    protected $resultPageFactory;
    /**
     * @var FrontendUrl
     */
    private $frontendUrl;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param FrontendUrl $frontendUrl
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        FrontendUrl $frontendUrl
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->frontendUrl = $frontendUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Generates user selected listing update page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('id')) {

            /** @var string */
            $title = __('Product Listing Update');

            /** @var PageFactory */
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Magento_Amazon::channel_amazon_index');
            $resultPage->getConfig()->getTitle()->prepend($title);

            return $resultPage;
        }

        /** @var Redirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $this->messageManager
            ->addErrorMessage(__('Unable to load Amazon account settings. Please try again.'));
        return $resultRedirect->setUrl($this->frontendUrl->getHomeUrl());
    }
}
