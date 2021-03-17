<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Dashboard\Tab;

/**
 * Class Quotes
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Dashboard\Tab
 */
class Quotes extends \Cart2Quote\Quotation\Block\Adminhtml\Dashboard\Graph
{
    /**
     * Quotes constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport\DashboardCollectionFactory $collectionFactory
     * @param \Magento\Backend\Helper\Dashboard\Data $dashboardData
     * @param \Cart2Quote\Quotation\Helper\Dashboard\Quote $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport\DashboardCollectionFactory $collectionFactory,
        \Magento\Backend\Helper\Dashboard\Data $dashboardData,
        \Cart2Quote\Quotation\Helper\Dashboard\Quote $dataHelper,
        array $data = []
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $collectionFactory, $dashboardData, $data);
    }

    /**
     * Initialize object
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setHtmlId('quotes');
        parent::_construct();
    }

    /**
     * Prepare chart data
     *
     * @return void
     */
    protected function _prepareData()
    {
        $this->getDataHelper()->setParam('store', $this->getRequest()->getParam('store'));
        $this->getDataHelper()->setParam('website', $this->getRequest()->getParam('website'));
        $this->getDataHelper()->setParam('group', $this->getRequest()->getParam('group'));

        $this->setDataRows('quantity');
        $this->_axisMaps = ['x' => 'range', 'y' => 'quantity'];

        parent::_prepareData();
    }
}
