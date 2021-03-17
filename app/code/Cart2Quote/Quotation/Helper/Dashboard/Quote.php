<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper\Dashboard;

use Magento\Framework\App\ObjectManager;

/**
 * Class Quote
 * Adminhtml dashboard helper for quotes
 *
 * @api
 * @package Cart2Quote\Quotation\Helper\Dashboard
 */
class Quote extends \Magento\Backend\Helper\Dashboard\AbstractDashboard
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport\DashboardCollection
     */
    protected $_quoteCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Quote constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport\DashboardCollection $quoteCollection
     * @param \Magento\Store\Model\StoreManagerInterface|null $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport\DashboardCollection $quoteCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager = null
    ) {
        $this->_quoteCollection = $quoteCollection;
        $this->_storeManager = $storeManager ?: ObjectManager::getInstance()
            ->get(\Magento\Store\Model\StoreManagerInterface::class);

        parent::__construct($context);
    }

    /**
     * @return void
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _initCollection()
    {
        $isFilter = $this->getParam('store') || $this->getParam('website') || $this->getParam('group');

        $this->_collection = $this->_quoteCollection->prepareSummary($this->getParam('period'), 0, 0, $isFilter);

        if ($this->getParam('store')) {
            $this->_collection->addFieldToFilter('quote.store_id', $this->getParam('store'));
        } elseif ($this->getParam('website')) {
            $storeIds = $this->_storeManager->getWebsite($this->getParam('website'))->getStoreIds();
            $this->_collection->addFieldToFilter('quote.store_id', ['in' => implode(',', $storeIds)]);
        } elseif ($this->getParam('group')) {
            $storeIds = $this->_storeManager->getGroup($this->getParam('group'))->getStoreIds();
            $this->_collection->addFieldToFilter('quote.store_id', ['in' => implode(',', $storeIds)]);
        } elseif (!$this->_collection->isLive()) {
            $this->_collection->addFieldToFilter(
                'quote.store_id',
                ['eq' => $this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId()]
            );
        }

        $this->_collection->load();
    }
}
