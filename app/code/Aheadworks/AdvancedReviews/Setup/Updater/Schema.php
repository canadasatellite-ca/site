<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Setup\Updater;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review as ReviewResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Queue as QueueResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\AbuseReport as AbuseReportResourceModel;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment as CommentResourceModel;
use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Status;
use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Type;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Email\Subscriber as EmailSubscriberResourceModel;

/**
 * Class Schema
 * @package Aheadworks\AdvancedReviews\Setup\Updater
 */
class Schema
{
    /**
     * Update to 1.1.0 version
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function update110(SchemaSetupInterface $setup)
    {
        $this
            ->addReviewAttachmentTable($setup)
            ->addAbuseReportTable($setup)
            ->changeCommentTable($setup)
            ->changeEmailQueueTable($setup);

        return $this;
    }

    /**
     * Update to 1.2.0 version
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function update120(SchemaSetupInterface $setup)
    {
        $this->addEmailColumnToReviewTable($setup);
        $this->addProsAndConsColumnsToReviewTable($setup);
        $this->addAgreementsColumnToReviewTable($setup);
        $this->setReviewSummaryColumnNullable($setup);
        $this->addEmailSubscriberTable($setup);
        $this->addSecurityCodeColumnToEmailQueueTable($setup);
        $this->addFeaturedReviewColumnToReviewTable($setup);

        return $this;
    }

    /**
     * Add review attachments table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addReviewAttachmentTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(ReviewResourceModel::REVIEW_ATTACHMENT_TABLE_NAME))
            ->addColumn(
                'review_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Review Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                Table::DEFAULT_TEXT_SIZE,
                ['nullable' => false],
                'Name'
            )->addColumn(
                'file_name',
                Table::TYPE_TEXT,
                Table::DEFAULT_TEXT_SIZE,
                ['nullable' => false],
                'File Name On The Server'
            )->addForeignKey(
                $installer->getFkName(
                    ReviewResourceModel::REVIEW_ATTACHMENT_TABLE_NAME,
                    'review_id',
                    ReviewResourceModel::MAIN_TABLE_NAME,
                    'id'
                ),
                'review_id',
                $installer->getTable(ReviewResourceModel::MAIN_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )->setComment('AW Advanced Reviews Review Attachments Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add abuse report table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addAbuseReportTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(AbuseReportResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => true,
                    'primary' => true
                ],
                'Report Id'
            )->addColumn(
                'entity_type',
                Table::TYPE_TEXT,
                40,
                ['nullable' => false],
                'Entity Type'
            )->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Entity Id'
            )->addColumn(
                'status',
                Table::TYPE_TEXT,
                128,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Status'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ],
                'Created At'
            )->addIndex(
                $installer->getIdxName(
                    AbuseReportResourceModel::MAIN_TABLE_NAME,
                    ['entity_type', 'entity_id', 'status']
                ),
                ['entity_type', 'entity_id', 'status']
            )->addIndex(
                $installer->getIdxName(AbuseReportResourceModel::MAIN_TABLE_NAME, ['created_at']),
                ['created_at']
            )->setComment('AW Advanced Reviews Abuse Report Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Change comment table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function changeCommentTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(CommentResourceModel::MAIN_TABLE_NAME);
        $connection = $installer->getConnection();

        if ($connection->isTableExists($tableName)) {
            $connection->addColumn(
                $tableName,
                'type',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 40,
                    'nullable' => false,
                    'default' => Type::ADMIN,
                    'after' => 'id',
                    'comment' => 'Comment type'
                ]
            );
            $connection->addColumn(
                $tableName,
                'status',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => Status::getDefaultStatus(),
                    'after' => 'review_id',
                    'comment' => 'Comment status'
                ]
            );
            $connection->addColumn(
                $tableName,
                'nickname',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 128,
                    'nullable' => false,
                    'after' => 'status',
                    'comment' => 'Nickname'
                ]
            );
            $connection->addColumn(
                $tableName,
                'created_at',
                [
                    'type' => Table::TYPE_TIMESTAMP,
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT,
                    'after' => 'content',
                    'comment' => 'Created At'
                ]
            );
            $connection->addIndex(
                $tableName,
                $installer->getIdxName(CommentResourceModel::MAIN_TABLE_NAME, ['status']),
                ['status']
            );
            $connection->addIndex(
                $tableName,
                $installer->getIdxName(CommentResourceModel::MAIN_TABLE_NAME, ['type']),
                ['type']
            );
            $connection->addIndex(
                $tableName,
                $installer->getIdxName(CommentResourceModel::MAIN_TABLE_NAME, ['created_at']),
                ['created_at']
            );
        }

        return $this;
    }

    /**
     * Change email queue table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function changeEmailQueueTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(QueueResourceModel::MAIN_TABLE_NAME);
        $connection = $installer->getConnection();

        if ($connection->tableColumnExists($tableName, 'object_id')) {
            $connection->changeColumn(
                $tableName,
                'object_id',
                'object_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => false,
                    'unsigned' => true,
                    'comment' => 'Related object ID'
                ]
            );
        }

        return $this;
    }

    /**
     * Change review table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addEmailColumnToReviewTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(ReviewResourceModel::MAIN_TABLE_NAME);
        $connection = $installer->getConnection();

        if ($connection->isTableExists($tableName)) {
            $connection->addColumn(
                $tableName,
                'email',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'after' => 'customer_id',
                    'comment' => 'Email'
                ]
            );
        }

        return $this;
    }

    /**
     * Change review table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addProsAndConsColumnsToReviewTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(ReviewResourceModel::MAIN_TABLE_NAME);
        $connection = $installer->getConnection();

        if ($connection->isTableExists($tableName)) {
            $connection->addColumn(
                $tableName,
                'pros',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'after' => 'content',
                    'comment' => 'Product Advantages'
                ]
            );
            $connection->addColumn(
                $tableName,
                'cons',
                [
                    'type' => Table::TYPE_TEXT,
                    'nullable' => true,
                    'after' => 'pros',
                    'comment' => 'Product Disadvantages'
                ]
            );
        }
        return $this;
    }

    /**
     * Change review table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addAgreementsColumnToReviewTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(ReviewResourceModel::MAIN_TABLE_NAME);
        $connection = $installer->getConnection();

        if ($connection->isTableExists($tableName)) {
            $connection->addColumn(
                $tableName,
                'are_agreements_confirmed',
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'nullable' => true,
                    'comment' => 'Are terms and conditions confirmed'
                ]
            );
        }
        return $this;
    }

    /**
     * Make summary column in the review table nullable
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function setReviewSummaryColumnNullable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(ReviewResourceModel::MAIN_TABLE_NAME);
        $connection = $installer->getConnection();

        if ($connection->tableColumnExists($tableName, 'summary')) {
            $connection->modifyColumn(
                $tableName,
                'summary',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Review summary'
                ]
            );
        }

        return $this;
    }

    /**
     * Add email subscribers table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addEmailSubscriberTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable(EmailSubscriberResourceModel::MAIN_TABLE_NAME)
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'identity' => true,
                    'primary' => true,
                ],
                'Email Subscriber ID'
            )->addColumn(
                'website_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Website Id'
            )->addColumn(
                'email',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Subscriber email'
            )->addColumn(
                'is_review_approved_email_enabled',
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '1',
                ],
                'Is Review Approved Email Enabled'
            )->addColumn(
                'is_new_comment_email_enabled',
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '1',
                ],
                'Is New Comment Email Enabled'
            )->addColumn(
                'is_review_reminder_email_enabled',
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                    'default' => '1',
                ],
                'Is Review Reminder Email Enabled'
            )->addIndex(
                $installer->getIdxName(
                    EmailSubscriberResourceModel::MAIN_TABLE_NAME,
                    ['website_id', 'email']
                ),
                ['website_id', 'email']
            )->addForeignKey(
                $installer->getFkName(
                    EmailSubscriberResourceModel::MAIN_TABLE_NAME,
                    'website_id',
                    'store_website',
                    'website_id'
                ),
                'website_id',
                $installer->getTable('store_website'),
                'website_id',
                Table::ACTION_CASCADE
            )->setComment('AW Advanced Reviews Email Subscribers Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Change email queue table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addSecurityCodeColumnToEmailQueueTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(QueueResourceModel::MAIN_TABLE_NAME);
        $connection = $installer->getConnection();

        if ($connection->isTableExists($tableName)) {
            $connection->addColumn(
                $tableName,
                'security_code',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 128,
                    'nullable' => true,
                    'comment' => 'Security code'
                ]
            );
        }

        return $this;
    }

    /**
     * Add is featured review column to review table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addFeaturedReviewColumnToReviewTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(ReviewResourceModel::MAIN_TABLE_NAME);
        $connection = $installer->getConnection();

        if ($connection->isTableExists($tableName)) {
            $connection->addColumn(
                $tableName,
                'is_featured',
                [
                    'type' => Table::TYPE_BOOLEAN,
                    'nullable' => true,
                    'comment' => 'Is featured'
                ]
            );
        }
        return $this;
    }
}
