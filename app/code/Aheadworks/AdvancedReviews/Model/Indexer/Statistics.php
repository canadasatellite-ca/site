<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Indexer;

use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Aheadworks\AdvancedReviews\Model\Indexer\Statistics\Action\Rows;
use Aheadworks\AdvancedReviews\Model\Indexer\Statistics\Action\Row;
use Aheadworks\AdvancedReviews\Model\Indexer\Statistics\Action\Full;

/**
 * Class Statistics
 * @package Aheadworks\AdvancedReviews\Model\Indexer
 */
class Statistics implements IndexerActionInterface, MviewActionInterface
{
    /**
     * @var Row
     */
    private $statisticsIndexerRow;

    /**
     * @var Rows
     */
    private $statisticsIndexerRows;

    /**
     * @var Full
     */
    private $statisticsIndexerFull;

    /**
     * @param Row $statisticsIndexerRow
     * @param Rows $statisticsIndexerRows
     * @param Full $statisticsIndexerFull
     */
    public function __construct(
        Row $statisticsIndexerRow,
        Rows $statisticsIndexerRows,
        Full $statisticsIndexerFull
    ) {
        $this->statisticsIndexerRow = $statisticsIndexerRow;
        $this->statisticsIndexerRows = $statisticsIndexerRows;
        $this->statisticsIndexerFull = $statisticsIndexerFull;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($ids)
    {
        $this->statisticsIndexerRows->execute($ids);
    }

    /**
     * {@inheritdoc}
     */
    public function executeFull()
    {
        $this->statisticsIndexerFull->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function executeList(array $ids)
    {
        $this->statisticsIndexerRows->execute($ids);
    }

    /**
     * {@inheritdoc}
     */
    public function executeRow($id)
    {
        $this->statisticsIndexerRow->execute($id);
    }
}
