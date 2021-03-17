<?php
/**
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2016] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 *
 * @category    Cart2Quote
 * @package     Desk
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Desk\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $this->createTableTicketStatus($installer);
        $this->createTableTicketPriority($installer);
        $this->createTableTicket($installer);
        $this->createTableTicketMessage($installer);

        $setup->endSetup();
    }

    /**
     * Create table ticket status
     *
     * @param SchemaSetupInterface $installer
     *
     * @return void
     */
    protected function createTableTicketStatus($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('desk_ticket_status'))
            ->addColumn(
                'status_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Status id'
            )
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'Status code'
            )
            ->setComment('Ticket statuses');
        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table ticket priority
     *
     * @param SchemaSetupInterface $installer
     *
     * @return void
     */
    protected function createTableTicketPriority($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('desk_ticket_priority'))
            ->addColumn(
                'priority_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Status id'
            )
            ->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                ['nullable' => false],
                'Priority code'
            )
            ->setComment('Ticket statuses');
        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table ticket
     *
     * @param SchemaSetupInterface $installer
     * @return void
     */
    protected function createTableTicket($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('desk_ticket'))
            ->addColumn(
                'ticket_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Review entity id'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Ticket create date'
            )->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Ticket updated at'
            )->addColumn(
                'status_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Status of the ticket'
            )->addColumn(
                'priority_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Priority of the ticket'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Customer Assigned to the ticket'
            )->addColumn(
                'assignee_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Admin assigned to the ticket'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'default' => '0'],
                'Store id'
            )->addColumn(
                'subject',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                2048,
                ['nullable' => false],
                'The subject of the ticket'
            )->addColumn(
                'deleted',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'default' => 0],
                'deleted flag'
            )->addForeignKey(
                $installer->getFkName('desk_ticket', 'customer_id', 'customer_entity', 'entity_id'),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->addForeignKey(
                $installer->getFkName('desk_ticket', 'assignee_id', 'admin_user', 'user_id'),
                'assignee_id',
                $installer->getTable('admin_user'),
                'user_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->addForeignKey(
                $installer->getFkName('desk_ticket', 'status_id', 'desk_ticket_status', 'status_id'),
                'status_id',
                $installer->getTable('desk_ticket_status'),
                'status_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->addForeignKey(
                $installer->getFkName('desk_ticket', 'priority_id', 'desk_ticket_priority', 'priority_id'),
                'priority_id',
                $installer->getTable('desk_ticket_priority'),
                'priority_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->addForeignKey(
                $installer->getFkName('desk_ticket', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            )->setComment('Ticket');
        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table ticket message
     *
     * @param SchemaSetupInterface $installer
     *
     * @return void
     */
    protected function createTableTicketMessage($installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable('desk_ticket_message'))
            ->addColumn(
                'message_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Message id'
            )->addColumn(
                'ticket_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Ticket id'
            )->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created at'
            )->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated at'
            )->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Customer assigned to the ticket'
            )->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Admin assigned to the ticket'
            )->addColumn(
                'is_private',
                \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                null,
                ['nullable' => false, 'default' => 0],
                'isPrivate flag'
            )->addColumn(
                'message',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
                ['nullable' => false],
                'Message text'
            )->addForeignKey(
                $installer->getFkName('desk_ticket_message', 'ticket_id', 'desk_ticket', 'ticket_id'),
                'ticket_id',
                $installer->getTable('desk_ticket'),
                'ticket_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->addForeignKey(
                $installer->getFkName('desk_ticket_message', 'customer_id', 'customer_entity', 'entity_id'),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->addForeignKey(
                $installer->getFkName('desk_ticket_message', 'user_id', 'admin_user', 'user_id'),
                'user_id',
                $installer->getTable('admin_user'),
                'user_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->setComment('Ticket Message');
        $installer->getConnection()->createTable($table);
    }
}
