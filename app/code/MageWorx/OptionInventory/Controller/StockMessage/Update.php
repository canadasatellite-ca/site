<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionInventory\Controller\StockMessage;

use Magento\Framework\App\Action\Action;

/**
 * Class Update.
 * This controller updates options stock message on the product page
 */
class Update extends Action
{
    /**
     * @var \MageWorx\OptionInventory\Model\StockProvider|null
     */
    protected $stockProvider = null;

    /**
     * Update constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \MageWorx\OptionInventory\Model\StockProvider $stockProvider
     */
    function __construct(
        \Magento\Framework\App\Action\Context $context,
        \MageWorx\OptionInventory\Model\StockProvider $stockProvider
    ) {
        parent::__construct($context);
        $this->stockProvider = $stockProvider;
    }

    /**
	 * 2021-05-24 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
	 * "Refactor the `MageWorx_OptionInventory` module": https://github.com/canadasatellite-ca/site/issues/126
	 * @used-by \Magento\Framework\App\Action\Action::dispatch()
     */
    function execute() {
        $this->getRequest()->getParams();
        $options = json_decode($this->getRequest()->getPost('opConfig'), true);
        $options = $this->stockProvider->updateOptionsStockMessage($options);
        $this->getResponse()->setBody(\Zend_Json::encode(['result' => $options]));
    }
}
