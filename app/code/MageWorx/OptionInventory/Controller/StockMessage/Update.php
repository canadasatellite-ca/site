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
	 * 1) "Refactor the `MageWorx_OptionInventory` module": https://github.com/canadasatellite-ca/site/issues/126
	 * 2) «array_keys() expects parameter 1 to be array, null given
	 * in app/code/MageWorx/OptionInventory/Model/StockProvider.php on line 253»:
	 * https://github.com/canadasatellite-ca/site/issues/125
	 * @used-by \Magento\Framework\App\Action\Action::dispatch()
	 * @return Json
     */
    function execute() {return !df_request_o()->isPost() ? df_400() : Json::i([
    	'result' => $this->stockProvider->updateOptionsStockMessage(df_json_decode(df_request('opConfig')))
	]);}
}