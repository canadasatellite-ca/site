<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Dashboard;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class AbstractDashboard
 * Adminhtml dashboard tab abstract
 *
 * @package Cart2Quote\Quotation\Block\Adminhtml\Dashboard
 */
abstract class AbstractDashboard extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Dashboard\Quote
     */
    protected $_dataHelper = null;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport\DashboardCollectionFactory
     */
    protected $_collectionFactory;

    /**
     * AbstractDashboard constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport\DashboardCollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport\DashboardCollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return array|AbstractCollection|\Magento\Eav\Model\Entity\Collection\Abstract
     */
    public function getCollection()
    {
        return $this->getDataHelper()->getCollection();
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->getDataHelper()->getCount();
    }

    /**
     * Get data helper
     *
     * @return \Magento\Backend\Helper\Dashboard\AbstractDashboard
     */
    public function getDataHelper()
    {
        return $this->_dataHelper;
    }

    /**
     * @return $this
     */
    protected function _prepareData()
    {
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_prepareData();
        return parent::_prepareLayout();
    }
}
