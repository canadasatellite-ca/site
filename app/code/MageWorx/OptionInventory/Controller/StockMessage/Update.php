<?php
namespace MageWorx\OptionInventory\Controller\StockMessage;
use Df\Framework\W\Result\Json;
use Magento\Framework\App\Action\Action;
class Update extends Action {
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
	 * @return Json
     */
    function execute() {return Json::i([
    	'result' => $this->stockProvider->updateOptionsStockMessage(df_json_decode(df_request('opConfig')))
	]);}
}