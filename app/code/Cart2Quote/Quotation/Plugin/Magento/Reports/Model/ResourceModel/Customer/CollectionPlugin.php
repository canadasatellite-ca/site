<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Plugin\Magento\Reports\Model\ResourceModel\Customer;

/**
 * Class CollectionPlugin
 *
 * @package Cart2Quote\Quotation\Plugin\Magento\Reports\Model\ResourceModel\Customer
 */
class CollectionPlugin extends \Magento\Reports\Model\ResourceModel\Customer\Collection
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
     */
    protected $quoteResource;

    /**
     * CollectionPlugin constructor
     *
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\DataObject\Copy\Config $fieldsetConfig
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $orderResource
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $quoteResource
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param string $modelName
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\DataObject\Copy\Config $fieldsetConfig,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemFactory,
        \Magento\Sales\Model\ResourceModel\Order\Collection $orderResource,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $quoteResource,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        $modelName = self::CUSTOMER_MODEL_NAME
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $entitySnapshot,
            $fieldsetConfig,
            $quoteRepository,
            $quoteItemFactory,
            $orderResource,
            $connection,
            $modelName
        );

        $this->quoteResource = $quoteResource;
    }

    /**
     * Plugin style version of _afterLoad()
     *
     * @param $subject \Magento\Reports\Model\ResourceModel\Customer\Collection
     * @param callable $proceed
     * @param bool $printQuery
     * @param bool $logQuery
     * @return \Magento\Reports\Model\ResourceModel\Customer\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundLoad(
        $subject,
        $proceed,
        $printQuery = false,
        $logQuery = false
    ) {
        $before = $subject->isLoaded();
        $result = $proceed($printQuery, $logQuery);
        $after = $subject->isLoaded();

        //plugin style version of _afterLoad()
        if ($before != $after) {
            $this->_addQuotesStatistics($result);
        }

        return $result;
    }

    /**
     * Add quotes statistics to collection items
     *
     * @param $result \Magento\Reports\Model\ResourceModel\Customer\Collection
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addQuotesStatistics($result)
    {
        $customerIds = $result->getColumnValues($result->getResource()->getIdFieldName());
        if ($result->_addOrderStatistics && !empty($customerIds)) {
            $select = $this->quoteResource->getConnection()->select();
            $select->from(
                ['main_table' => $this->quoteResource->getTable('quotation_quote')],
                [
                    'quotes_count' => 'COUNT(main_table.quote_id)'
                ]
            )->joinLeft(
                ['quote' => $this->quoteResource->getTable('quote')],
                'quote.entity_id=main_table.quote_id',
                'customer_id'
            )->where(
                'main_table.state NOT IN (?)',
                [
                    \Cart2Quote\Quotation\Model\Quote\Status::STATUS_NEW,
                    \Cart2Quote\Quotation\Model\Quote\Status::STATE_CANCELED
                ]
            )->where(
                'quote.customer_id IN(?)',
                $customerIds
            )->group(
                'quote.customer_id'
            );

            foreach ($this->quoteResource->getConnection()->fetchAll($select) as $quotesInfo) {
                $result->getItemById($quotesInfo['customer_id'])->addData($quotesInfo);
            }
        }

        return $this;
    }
}
