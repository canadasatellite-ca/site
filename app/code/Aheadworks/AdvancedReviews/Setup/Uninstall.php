<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review as ReviewResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\AbuseReport as AbuseReportResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Customer\Nickname as CustomerNicknameResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue as QueueResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Indexer\Statistics as StatisticsIndexerResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment as CommentResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Email\Subscriber as EmailSubscriberResourceModel;

/**
 * Class Uninstall
 *
 * @package Aheadworks\AdvancedReviews\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * {@inheritdoc}
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this
            ->uninstallTables($installer)
            ->uninstallConfigData($installer)
            ->uninstallFlagData($installer)
        ;

        $installer->endSetup();
    }

    /**
     * Uninstall all module tables
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallTables(SchemaSetupInterface $installer)
    {
        $installer->getConnection()->dropTable(
            $installer->getTable(ReviewResourceModel::REVIEW_ATTACHMENT_TABLE_NAME)
        );
        $installer->getConnection()->dropTable(
            $installer->getTable(AbuseReportResourceModel::MAIN_TABLE_NAME)
        );
        $installer->getConnection()->dropTable(
            $installer->getTable(QueueResourceModel::MAIN_TABLE_NAME)
        );
        $installer->getConnection()->dropTable(
            $installer->getTable(StatisticsIndexerResourceModel::MAIN_TABLE_NAME)
        );
        $installer->getConnection()->dropTable(
            $installer->getTable(CustomerNicknameResourceModel::MAIN_TABLE_NAME)
        );
        $installer->getConnection()->dropTable(
            $installer->getTable(ReviewResourceModel::REMINDER_ORDER_ITEM_TABLE_NAME)
        );
        $installer->getConnection()->dropTable(
            $installer->getTable(ReviewResourceModel::SHARED_STORE_TABLE_NAME)
        );
        $installer->getConnection()->dropTable(
            $installer->getTable(ReviewResourceModel::MAIN_TABLE_NAME)
        );
        $installer->getConnection()->dropTable(
            $installer->getTable(CommentResourceModel::MAIN_TABLE_NAME)
        );
        $installer->getConnection()->dropTable(
            $installer->getTable(EmailSubscriberResourceModel::MAIN_TABLE_NAME)
        );
        return $this;
    }

    /**
     * Uninstall module data from config
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallConfigData(SchemaSetupInterface $installer)
    {
        $configTable = $installer->getTable('core_config_data');
        $installer->getConnection()->delete($configTable, "`path` LIKE 'aw_advanced_reviews%'");
        return $this;
    }

    /**
     * Uninstall module data from flag table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function uninstallFlagData(SchemaSetupInterface $installer)
    {
        $flagTable = $installer->getTable('flag');
        $installer->getConnection()->delete($flagTable, "`flag_code` LIKE 'aw_advanced_reviews%'");
        return $this;
    }
}
