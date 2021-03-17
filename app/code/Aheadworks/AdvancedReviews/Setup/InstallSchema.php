<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as DataDefinition;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review as ReviewResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Customer\Nickname as CustomerNicknameResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue as QueueResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Indexer\Statistics as StatisticsIndexerResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment as CommentResourceModel;
use Aheadworks\AdvancedReviews\Setup\Updater\Schema;

/**
 * Class InstallSchema
 * @package Aheadworks\AdvancedReviews\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Schema
     */
    private $updater;

    /**
     * @param Schema $updater
     */
    public function __construct(
        Schema $updater
    ) {
        $this->updater = $updater;
    }

    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->createReviewTable($installer)
            ->createReviewSharedStoreTable($installer)
            ->createReminderReviewOrderItemTable($installer)
            ->createCustomerNicknameTable($installer)
            ->createStatisticsTable($installer)
            ->createEmailQueueTable($installer)
            ->createReviewCommentTable($installer)
        ;
        $this->updater->update110($setup);
        $this->updater->update120($setup);

        $installer->endSetup();
    }

    /**
     * Create review table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createReviewTable(SchemaSetupInterface $installer)
    {
        $reviewTable = $installer
            ->getConnection()
            ->newTable(
                $installer->getTable(ReviewResourceModel::MAIN_TABLE_NAME)
            )->addColumn(
                'id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => true,
                    'primary' => true
                ],
                'Review ID'
            )->addColumn(
                'created_at',
                DataDefinition::TYPE_DATETIME,
                null,
                [
                    'nullable' => false,
                ],
                'Review creation date'
            )->addColumn(
                'rating',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Review main rating'
            )
            ->addColumn(
                'summary',
                DataDefinition::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Review summary'
            )->addColumn(
                'nickname',
                DataDefinition::TYPE_TEXT,
                128,
                [
                    'nullable' => true,
                ],
                'Customer nickname'
            )->addColumn(
                'content',
                DataDefinition::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                ],
                'Review content'
            )->addColumn(
                'store_id',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Store ID'
            )->addColumn(
                'product_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Product ID'
            )
            ->addColumn(
                'status',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                ],
                'Review status'
            )->addColumn(
                'author_type',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false,
                ],
                'Review author type'
            )->addColumn(
                'customer_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true
                ],
                'Customer ID'
            )->addColumn(
                'is_verified_buyer',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0
                ],
                'Defines is review belongs to the verified buyer'
            )->addColumn(
                'votes_positive',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0'
                ],
                'Positive helpfulness votes'
            )->addColumn(
                'votes_negative',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '0'
                ],
                'Negative helpfulness votes'
            )->addColumn(
                'product_recommended',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0
                ],
                'Is product recommended by review author'
            )->addIndex(
                $installer->getIdxName(ReviewResourceModel::MAIN_TABLE_NAME, ['rating']),
                ['rating']
            )->addIndex(
                $installer->getIdxName(ReviewResourceModel::MAIN_TABLE_NAME, ['status']),
                ['status']
            )->addForeignKey(
                $installer->getFkName(
                    ReviewResourceModel::MAIN_TABLE_NAME,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                DataDefinition::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    ReviewResourceModel::MAIN_TABLE_NAME,
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                DataDefinition::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    ReviewResourceModel::MAIN_TABLE_NAME,
                    'customer_id',
                    'customer_entity',
                    'entity_id'
                ),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                DataDefinition::ACTION_SET_NULL
            )->setComment('AW Advanced Reviews Review Table');
        $installer->getConnection()->createTable($reviewTable);
        return $this;
    }

    /**
     * Create review shared store table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createReviewSharedStoreTable(SchemaSetupInterface $installer)
    {
        $reviewSharedStoreTable = $installer
            ->getConnection()
            ->newTable(
                $installer->getTable(ReviewResourceModel::SHARED_STORE_TABLE_NAME)
            )->addColumn(
                'review_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Review ID'
            )->addColumn(
                'store_id',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Store ID'
            )->addForeignKey(
                $installer->getFkName(
                    ReviewResourceModel::SHARED_STORE_TABLE_NAME,
                    'review_id',
                    ReviewResourceModel::MAIN_TABLE_NAME,
                    'id'
                ),
                'review_id',
                $installer->getTable(ReviewResourceModel::MAIN_TABLE_NAME),
                'id',
                DataDefinition::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    ReviewResourceModel::SHARED_STORE_TABLE_NAME,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                DataDefinition::ACTION_CASCADE
            )->setComment('AW Advanced Reviews Review Shared Store Table');
        $installer->getConnection()->createTable($reviewSharedStoreTable);
        return $this;
    }

    /**
     * Create reminder review order item table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createReminderReviewOrderItemTable(SchemaSetupInterface $installer)
    {
        $reminderReviewOrderItemTable = $installer
            ->getConnection()
            ->newTable(
                $installer->getTable(ReviewResourceModel::REMINDER_ORDER_ITEM_TABLE_NAME)
            )->addColumn(
                'review_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Review ID'
            )->addColumn(
                'order_item_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Order Item ID'
            )->addForeignKey(
                $installer->getFkName(
                    ReviewResourceModel::REMINDER_ORDER_ITEM_TABLE_NAME,
                    'review_id',
                    ReviewResourceModel::MAIN_TABLE_NAME,
                    'id'
                ),
                'review_id',
                $installer->getTable(ReviewResourceModel::MAIN_TABLE_NAME),
                'id',
                DataDefinition::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    ReviewResourceModel::REMINDER_ORDER_ITEM_TABLE_NAME,
                    'order_item_id',
                    'sales_order_item',
                    'item_id'
                ),
                'order_item_id',
                $installer->getTable('sales_order_item'),
                'item_id',
                DataDefinition::ACTION_CASCADE
            )->setComment('AW Advanced Reviews Reminder Review Order Item Table');
        $installer->getConnection()->createTable($reminderReviewOrderItemTable);
        return $this;
    }

    /**
     * Create customer nickname table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createCustomerNicknameTable($installer)
    {
        $customerNicknameTable = $installer
            ->getConnection()
            ->newTable(
                $installer->getTable(CustomerNicknameResourceModel::MAIN_TABLE_NAME)
            )->addColumn(
                'id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Customer nickname ID'
            )->addColumn(
                'customer_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Customer ID'
            )->addColumn(
                'nickname',
                DataDefinition::TYPE_TEXT,
                128,
                [
                    'nullable' => false,
                ],
                'Customer nickname'
            )->addForeignKey(
                $installer->getFkName(
                    CustomerNicknameResourceModel::MAIN_TABLE_NAME,
                    'customer_id',
                    'customer_entity',
                    'entity_id'
                ),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                DataDefinition::ACTION_CASCADE
            )->setComment('AW Advanced Reviews Customer Nickname Table');
        $installer->getConnection()->createTable($customerNicknameTable);
        return $this;
    }

    /**
     * Create statistics table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createStatisticsTable($installer)
    {
        $statisticsTable = $installer
            ->getConnection()
            ->newTable(
                $installer->getTable(StatisticsIndexerResourceModel::MAIN_TABLE_NAME)
            )->addColumn(
                'store_id',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Store ID'
            )->addColumn(
                'product_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Product ID'
            )->addColumn(
                'reviews_count',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0
                ],
                'Reviews count'
            )->addColumn(
                'aggregated_rating',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0
                ],
                'Aggregated main rating'
            )->addForeignKey(
                $installer->getFkName(
                    StatisticsIndexerResourceModel::MAIN_TABLE_NAME,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                DataDefinition::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    StatisticsIndexerResourceModel::MAIN_TABLE_NAME,
                    'product_id',
                    'catalog_product_entity',
                    'entity_id'
                ),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                DataDefinition::ACTION_CASCADE
            )->setComment('AW Advanced Reviews Statistics Table');
        $installer->getConnection()->createTable($statisticsTable);

        return $this;
    }

    /**
     * Create email queue table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createEmailQueueTable($installer)
    {
        $emailQueueTable = $installer
            ->getConnection()
            ->newTable(
                $installer->getTable(QueueResourceModel::MAIN_TABLE_NAME)
            )->addColumn(
                'id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => true,
                    'primary' => true
                ],
                'Email ID'
            )->addColumn(
                'store_id',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Store ID'
            )->addColumn(
                'type',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Email type'
            )->addColumn(
                'object_id',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Related object ID'
            )->addColumn(
                'status',
                DataDefinition::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Email status'
            )->addColumn(
                'created_at',
                DataDefinition::TYPE_DATETIME,
                null,
                [
                    'nullable' => false,
                ],
                'Email creation date'
            )->addColumn(
                'scheduled_at',
                DataDefinition::TYPE_DATETIME,
                null,
                [
                    'nullable' => false,
                ],
                'Email scheduled sending date'
            )->addColumn(
                'sent_at',
                DataDefinition::TYPE_DATETIME,
                null,
                [
                    'nullable' => true,
                ],
                'Email actual sending date'
            )->addColumn(
                'recipient_name',
                DataDefinition::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Rrecipient name'
            )->addColumn(
                'recipient_email',
                DataDefinition::TYPE_TEXT,
                255,
                [
                    'nullable' => false
                ],
                'Recipient email'
            )->addIndex(
                $installer->getIdxName(QueueResourceModel::MAIN_TABLE_NAME, ['status']),
                ['status']
            )->addForeignKey(
                $installer->getFkName(
                    QueueResourceModel::MAIN_TABLE_NAME,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                DataDefinition::ACTION_CASCADE
            )->setComment('AW Advanced Reviews Email Queue Table');
        $installer->getConnection()->createTable($emailQueueTable);

        return $this;
    }

    /**
     * Create review comment table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function createReviewCommentTable(SchemaSetupInterface $installer)
    {
        $reviewCommentTable = $installer
            ->getConnection()
            ->newTable(
                $installer->getTable(CommentResourceModel::MAIN_TABLE_NAME)
            )->addColumn(
                'id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => true,
                    'primary' => true
                ],
                'Comment ID'
            )->addColumn(
                'review_id',
                DataDefinition::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Review ID'
            )->addColumn(
                'content',
                DataDefinition::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                ],
                'Comment content'
            )->addForeignKey(
                $installer->getFkName(
                    CommentResourceModel::MAIN_TABLE_NAME,
                    'review_id',
                    ReviewResourceModel::MAIN_TABLE_NAME,
                    'id'
                ),
                'review_id',
                $installer->getTable(ReviewResourceModel::MAIN_TABLE_NAME),
                'id',
                DataDefinition::ACTION_CASCADE
            )->setComment('AW Advanced Reviews Review Comment Table');
        $installer->getConnection()->createTable($reviewCommentTable);
        return $this;
    }
}
