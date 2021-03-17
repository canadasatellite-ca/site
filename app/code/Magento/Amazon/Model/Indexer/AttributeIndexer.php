<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\Indexer;

use Magento\Amazon\Logger\AscClientLogger;
use Magento\Amazon\Model\Indexer\Attribute\Action\Full;
use Magento\Amazon\Model\Indexer\Attribute\Action\Partial;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;

/**
 * Class AttributeIndexer
 */
class AttributeIndexer implements IndexerActionInterface, MviewActionInterface
{
    /** @var Full $attributeIndexerFull */
    protected $attributeIndexerFull;
    /** @var Partial $attributeIndexerPartial */
    protected $attributeIndexerPartial;
    /** @var AscClientLogger $ascClientLogger */
    protected $ascClientLogger;

    /**
     * @param Full $attributeIndexerFull
     * @param Partial $attributeIndexerPartial
     * @param AscClientLogger $ascClientLogger
     */
    public function __construct(
        Full $attributeIndexerFull,
        Partial $attributeIndexerPartial,
        AscClientLogger $ascClientLogger
    ) {
        $this->attributeIndexerFull = $attributeIndexerFull;
        $this->attributeIndexerPartial = $attributeIndexerPartial;
        $this->ascClientLogger = $ascClientLogger;
    }

    /**
     * Execute materialization on ids entities
     *
     * Implements MviewActionInterface method.
     *
     * Architectural intention is this method only called by framework's
     * Mview module, in response to changes in watched tables.
     *
     * @param int[] $ids
     * @return void
     */
    public function execute($ids = null)
    {
        if ($ids) {
            try {
                // execute partial reindex
                $this->ascClientLogger->debug(
                    'SQL trigger via Mview materializes changed IDs to indexer changelog.'
                );
                $this->attributeIndexerPartial->execute($ids);
            } catch (LocalizedException $e) {
                $this->ascClientLogger->critical($e);
            }
        }
    }

    /**
     * Execute full indexation
     *
     * Implements IndexerActionInterface method.
     *
     * Note: appears this is called only by 'Update By Schedule' Cron task,
     * as result of indexer:reindex CLI command, or during unit testing.
     *
     * @return void
     */
    public function executeFull()
    {
        try {
            $this->ascClientLogger->debug(
                'Full attribute reindex.'
            );
            $this->attributeIndexerFull->execute();
        } catch (LocalizedException $e) {
            $this->ascClientLogger->critical($e);
        }
    }

    /**
     * Execute partial indexation by ID list
     *
     * Implements IndexerActionInterface method.
     *
     * Note: appears this is only called by Adminhtml area.
     *
     * @param int[] $ids
     * @return void
     */
    public function executeList(array $ids = null)
    {
        if ($ids) {
            // execute partial reindex
            try {
                $this->ascClientLogger->debug(
                    'Partial attribute reindex, by ID list.'
                );
                $this->attributeIndexerPartial->execute($ids);
            } catch (LocalizedException $e) {
                $this->ascClientLogger->critical($e);
            }
        }
    }

    /**
     * Execute partial indexation by ID
     *
     * Implements IndexerActionInterface method.
     *
     * Note: appears this is only called by Adminhtml area.
     *
     * @param int $id
     * @return void
     */
    public function executeRow($id = null)
    {
        if ($id) {
            // execute partial reindex
            try {
                $this->ascClientLogger->debug(
                    'Partial attribute reindex, by single ID.'
                );
                $this->attributeIndexerPartial->execute([$id]);
            } catch (LocalizedException $e) {
                $this->ascClientLogger->critical($e);
            }
        }
    }
}
