<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

//Magento 2.2 fix
if (!interface_exists('Magento\Framework\App\Action\HttpPostActionInterface')) {
    class_alias(
        'Cart2Quote\Quotation\Legacy\Magento\Framework\App\Action\HttpPostActionInterface',
        'Magento\Framework\App\Action\HttpPostActionInterface'
    );
}

/**
 * Class MassDuplicateSelectCustomer
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class MassDuplicateSelectCustomer extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    const SELECTED_PARAM = 'selected';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonHelper;

    /**
     * MassDuplicateSelectCustomer constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper
    ) {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Customers list action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $selected = $this->getRequest()->getParam(static::SELECTED_PARAM);
        $isSelectedIdsValid = (is_array($selected) && !empty($selected));
        if (!$isSelectedIdsValid) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('An item needs to be selected. Select and try again.')
            );
        }
        $this->_getSession()->setDuplicateQuoteIds($this->jsonHelper->serialize($selected));

        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('grid');
            return $resultForward;
        }
        $resultPage = $this->resultPageFactory->create();

        /**
         * Set active menu item
         */
        $resultPage->setActiveMenu('Cart2Quote_Quotation::quotation_quote');

        $resultPage->getConfig()->getTitle()->prepend(__('Select Customer for Quote Duplication'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('Quotation'), __('Quotation'));
        $resultPage->addBreadcrumb(__('Quotes'), __('Quotes'));

        $this->_getSession()->unsCustomerData();
        $this->_getSession()->unsCustomerFormData();

        return $resultPage;
    }
}
