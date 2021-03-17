<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 *
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\Grid
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    use \Cart2Quote\Features\Traits\Model\ResourceModel\Quote\Grid\Collection {
        _initSelect as private _traitInitSelect;
    }

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'quotation_quote_grid_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'quote_grid_collection';
    /**
     * @var \Magento\Framework\DB\Helper
     */
    protected $coreResourceHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\DB\Helper $coreResourceHelper
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Framework\DB\Helper $coreResourceHelper,
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'quotation_quote',
        $resourceModel = \Cart2Quote\Quotation\Model\ResourceModel\Quote::class
    ) {
        $this->coreResourceHelper = $coreResourceHelper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        return $this->_traitInitSelect();
    }
}
