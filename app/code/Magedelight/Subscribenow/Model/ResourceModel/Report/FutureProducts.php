<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_Subscribenow
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Model\ResourceModel\Report;

/**
 * Bestsellers report resource model
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FutureProducts extends \Magento\Sales\Model\ResourceModel\Report\AbstractReport
{
    const AGGREGATION_DAILY = 'md_subscribenow_futureproducts_aggregated_daily';
    const AGGREGATION_MONTHLY = 'md_subscribenow_futureproducts_aggregated_monthly';
    const AGGREGATION_YEARLY = 'md_subscribenow_futureproducts_aggregated_yearly';

    protected $resource;
    protected $productSubscribersCollectionFactory;
    protected $attributeRepositoryInterface;
    protected $timezone;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Reports\Model\FlagFactory $reportsFlagFactory
     * @param \Magento\Framework\Stdlib\DateTime\Timezone\Validator $timezoneValidator
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param array $ignoredProductTypes
     * @param string $connectionName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Reports\Model\FlagFactory $reportsFlagFactory,
        \Magento\Framework\Stdlib\DateTime\Timezone\Validator $timezoneValidator,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magedelight\Subscribenow\Model\ResourceModel\ProductSubscribers\CollectionFactory $productSubscribersCollectionFactory,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepositoryInterface,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        $connectionName = null
    ) {
        parent::__construct(
            $context,
            $logger,
            $localeDate,
            $reportsFlagFactory,
            $timezoneValidator,
            $dateTime,
            $connectionName
        );

        $this->resource = $resource;
        $this->productSubscribersCollectionFactory = $productSubscribersCollectionFactory;
        $this->attributeRepositoryInterface = $attributeRepositoryInterface;
        $this->timezone = $timezone;
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::AGGREGATION_DAILY, 'id');
    }

    /**
     * Aggregate Orders data by order created at
     *
     * @param string|int|\DateTime|array|null $from
     * @param string|int|\DateTime|array|null $to
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function aggregate($from = null, $to = null)
    {
        $mainTable = $this->getMainTable();
        $connection = $this->getConnection();
        //$this->getConnection()->beginTransaction();

        try {
            $this->truncateTable();

            $aggregated_product = [];

            $collection = $this->getProductSubscribersCollection();
            foreach ($collection as $subscription) {
                $dates = $this->getFutureDates($subscription);

                foreach($dates as $date)
                {
                    if(!isset($aggregated_product[$date][$subscription->getData('store_id')][$subscription->getData('product_id')]))
                    {
                        $aggregated_product[$date][$subscription->getData('store_id')][$subscription->getData('product_id')]['product_sku'] = $subscription->getData('product_sku');
                        $aggregated_product[$date][$subscription->getData('store_id')][$subscription->getData('product_id')]['product_name'] = $subscription->getData('product_name');
                    }

                    $old_qty_ordered = $aggregated_product[$date][$subscription->getData('store_id')][$subscription->getData('product_id')]['qty_ordered'] ?? 0;
                    $aggregated_product[$date][$subscription->getData('store_id')][$subscription->getData('product_id')]['qty_ordered'] = (int) $old_qty_ordered + (int) $subscription->getData('qty_ordered');

                    $old_subscription_count = $aggregated_product[$date][$subscription->getData('store_id')][$subscription->getData('product_id')]['subscription_count'] ?? 0;
                    $aggregated_product[$date][$subscription->getData('store_id')][$subscription->getData('product_id')]['subscription_count'] = (int) $old_subscription_count + 1;
                }
            }
            
            $insertBatches = [];
            foreach($aggregated_product as $period => $aggregare_store)
            {
                foreach($aggregare_store as $store_id => $aggregate_product)
                {
                    foreach($aggregate_product as $product_id => $info)
                    {
                        $insertBatches[] = [
                            'period'             => $period,
                            'store_id'           => $store_id,
                            'product_id'         => $product_id,
                            'product_sku'        => $info['product_sku'],
                            'product_name'       => $info['product_name'],
                            'qty_ordered'        => $info['qty_ordered'],
                            'subscription_count' => $info['subscription_count']
                        ];
                    }
                }
            }

            $tableName = $this->resource->getTableName(self::AGGREGATION_DAILY);
            foreach(array_chunk($insertBatches, 100) as $batch)
            {
                $connection->insertMultiple($tableName, $batch);
            }

            $this->updateReportMonthlyYearly(
                $connection,
                'month',
                'qty_ordered',
                $mainTable,
                $this->getTable(self::AGGREGATION_MONTHLY)
            );
            $this->updateReportMonthlyYearly(
                $connection,
                'year',
                'qty_ordered',
                $mainTable,
                $this->getTable(self::AGGREGATION_YEARLY)
            );
            
            $this->_setFlagData(\Magedelight\Subscribenow\Model\Flag::REPORT_SUBSCRIBENOW_FUTUREPRODUCTS_FLAG_CODE);
        } catch (\Exception $e) {echo $e->getMessage();exit;
            throw $e;
        }

        return $this;
    }

    public function getProductSubscribersCollection()
    {
        $today = $this->timezone->date(null, null, false)->format('Y-m-d H:i:s');

        $collection = $this->productSubscribersCollectionFactory->create();
        $resource = $collection->getResource();
        $collection
            //->addFieldToFilter('next_occurrence_date', ['gteq' => $today])
            ->addFieldToFilter('subscription_status', \Magedelight\Subscribenow\Model\Source\ProfileStatus::ACTIVE_STATUS)
            ;

        $collection
            ->getSelect()
            ->columns(
                [
                    'qty_ordered' => new \Zend_Db_Expr("json_extract(order_item_info, '$.qty')"),
                ]
            )
            ->join(
                ['catalog_product_entity' => $resource->getTable('catalog_product_entity')],
                'catalog_product_entity.entity_id = main_table.product_id',
                ['sku AS product_sku']
            )
            ->group("main_table.subscription_id")
        ;
        //echo $collection->getSelect();exit;
        return $collection;
    }

    public function truncateTable()
    {
        $tables = [
            $this->resource->getTableName(self::AGGREGATION_DAILY),
            $this->resource->getTableName(self::AGGREGATION_MONTHLY),
            $this->resource->getTableName(self::AGGREGATION_YEARLY),
        ];
        $connection = $this->resource->getConnection();

        foreach ($tables as $table) {
            $connection->truncateTable($table);   
        }
    }

    public function getFutureDates($subscription)
    {
        $next_occurrence_date = $subscription->getData('next_occurrence_date');
        $next_occurrence_date_dateonly = date('Y-m-d', strtotime($next_occurrence_date));
        $dates = [$next_occurrence_date_dateonly];

        $is_trial = $subscription->getData('is_trial');
        $trial_bill_count = $subscription->getData('trial_count');
        $trial_billing_period = $subscription->getData('trial_period_unit');
        $trial_billing_frequency = $subscription->getData('trial_period_frequency');
        $trial_period_max_cycles = $subscription->getData('trial_period_max_cycle');
        if($trial_period_max_cycles == 0)
        {
            $trial_period_max_cycles = 365;
        }

        $bill_count = $subscription->getData('total_bill_count');
        $billing_period = $subscription->getData('billing_period');
        $billing_frequency = $subscription->getData('billing_frequency');
        $period_max_cycles = $subscription->getData('period_max_cycles');
        if($period_max_cycles == 0)
        {
            $period_max_cycles = 365;
        }
        
        /**
         * this is becuase we are already adding next_occurance_date as first date
         * so in case of trial and real billing, we have to skip first date
         */
        $skipped_firstdate = false;
        if($is_trial)
        {
            for($i=$trial_bill_count; $i<$trial_period_max_cycles; $i++)
            {
                if($this->canAddNewDate($skipped_firstdate, $dates, $i, $trial_bill_count))
                {
                    $this->pushNewDate($trial_billing_period, $trial_billing_frequency, $dates);
                }
            }
        }

        for($i=$bill_count; $i<$period_max_cycles; $i++)
        {
            if($this->canAddNewDate($skipped_firstdate, $dates, $i, $bill_count))
            {
                $this->pushNewDate($billing_period, $billing_frequency, $dates);
            }
        }

        return $dates;
    }

    public function pushNewDate($billing_period, $billing_frequency, &$dates)
    {
        $previous_date = end($dates);

        switch ($billing_period) {
            //day
            case 1:
            default:
                $dates[] = date('Y-m-d', strtotime($previous_date. ' + '.($billing_frequency * 1).' days'));
                break;

            //week
            case 2:
                $dates[] = date('Y-m-d', strtotime($previous_date. ' + '.($billing_frequency * 7).' days'));
                break;

            //month
            case 3:
                $dates[] = date('Y-m-d', strtotime($previous_date. ' + '.($billing_frequency * 1).' months'));
                break;
        }
    }
    
    /**
     * this is becuase we are already adding next_occurance_date as first date
     * so in case of trial and real billing, we have to skip first date
     */
    public function canAddNewDate(&$skipped_firstdate, $dates, $i, $passed_occurance)
    {
        if(!$skipped_firstdate && count($dates) == 1 && ($i == $passed_occurance))
        {
            $skipped_firstdate = true;
            return false;
        }

        return true;
    }

    public function updateReportMonthlyYearly($connection, $type, $column, $mainTable, $aggregationTable)
    {
        $periodSubSelect = $connection->select();
        $ratingSubSelect = $connection->select();
        $ratingSelect = $connection->select();

        switch ($type) {
            case 'year':
                $periodCol = $connection->getDateFormatSql('t.period', '%Y-01-01');
                break;
            case 'month':
                $periodCol = $connection->getDateFormatSql('t.period', '%Y-%m-01');
                break;
            default:
                $periodCol = 't.period';
                break;
        }

        $columns = [
            'period' => 't.period',
            'store_id' => 't.store_id',
            'product_id' => 't.product_id',
            'product_sku' => 't.product_sku',
            'product_name' => 't.product_name',
        ];

        if ($type == 'day') {
            $columns['id'] = 't.id';  // to speed-up insert on duplicate key update
        }

        $cols = array_keys($columns);
        $cols['total_qty'] = new \Zend_Db_Expr('SUM(t.' . $column . ')');
        $cols['total_subscription'] = new \Zend_Db_Expr('SUM(t.subscription_count)');
        $periodSubSelect->from(
            ['t' => $mainTable],
            $cols
        )->group(
            ['t.store_id', $periodCol, 't.product_id']
        )->order(
            ['t.store_id', $periodCol, 'total_qty DESC']
        );

        $cols = $columns;
        $cols[$column] = 't.total_qty';
        $cols['subscription_count'] = 't.total_subscription';
        
        $cols['prevStoreId'] = new \Zend_Db_Expr('(@prevStoreId := t.`store_id`)');
        $cols['prevPeriod'] = new \Zend_Db_Expr("(@prevPeriod := {$periodCol})");
        $ratingSubSelect->from($periodSubSelect, $cols);

        $cols = $columns;
        $cols['period'] = $periodCol;
        $cols[$column] = 't.' . $column;
        $cols['subscription_count'] = 't.subscription_count';
        
        $ratingSelect->from($ratingSubSelect, $cols);

        $sql = $ratingSelect->insertFromSelect($aggregationTable, array_keys($cols));
        $connection->query("SET @pos = 0, @prevStoreId = -1, @prevPeriod = '0000-00-00'");
        $connection->query($sql);
        return $this;
    }
}