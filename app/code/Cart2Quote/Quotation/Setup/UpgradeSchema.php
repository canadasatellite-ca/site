<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade Schema script
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Cart2Quote\Quotation\Setup\QuoteSetup
     */
    protected $quoteSetup;

    /**
     * UpgradeSchema constructor.
     *
     * @param \Cart2Quote\Quotation\Setup\QuoteSetup $quoteSetup
     */
    public function __construct(
        \Cart2Quote\Quotation\Setup\QuoteSetup $quoteSetup
    ) {
        $this->quoteSetup = $quoteSetup;
    }

    /**
     * Upgrade schema action
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.20') < 0) {
            // Get module table
            $tableName = $this->quoteSetup->getTable('quotation_quote');

            // Check if the table already exists
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'hash' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => false,
                        'length' => 40,
                        'default' => '',
                        'comment' => 'hash value',
                    ],
                ];

                $connection = $this->quoteSetup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            // Get module table
            $tableName = $this->quoteSetup->getTable('quotation_quote');

            // Check if the table already exists
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'is_quote' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => false,
                        'length' => 5,
                        'default' => 1,
                        'comment' => 'check quote is live',
                    ],
                ];

                $connection = $this->quoteSetup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

            //add sort column to quotation_quote_status
            $this->quoteSetup->getConnection()
                ->addColumn(
                    $this->quoteSetup->getTable('quotation_quote_status'),
                    'sort',
                    'int'
                );
        }

        if (version_compare($context->getVersion(), '1.1.2') < 0) {
            // Get module table
            $tableName = $this->quoteSetup->getTable('quotation_quote_tier_item');

            // Check if the table already exists
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'comment' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => '64k',
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'comment per line item',
                    ],
                ];

                $connection = $this->quoteSetup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.1.3') < 0) {
            //Don't add this colum, we remove it at line 516 and reinstal it at 2.1.0/line 772
//            /**
//             * Alter quote table with is_quotation_quote
//             */
//            $this->quoteSetup->getConnection()->addColumn(
//                $this->quoteSetup->getTable('quote'),
//                'is_quotation_quote',
//                [
//                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
//                    'unsigned' => true,
//                    'default' => '0',
//                    'comment' => 'Is Quotation Quote'
//                ]
//            );
        }

        if (version_compare($context->getVersion(), '1.2.1') < 0) {
            // Get module table
            $tableName = $this->quoteSetup->getTable('quotation_quote_tier_item');

            // Check if the table already exists
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'item_has_comment' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => 1,
                        'nullable' => false,
                        'comment' => 'check item has comment',
                    ],
                    'make_optional' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => 0,
                        'nullable' => false,
                        'comment' => 'check make optional',
                    ],
                ];

                $connection = $this->quoteSetup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.2.2') < 0) {
            // Get module table
            $tableName = $this->quoteSetup->getTable('quotation_quote');

            // Check if the table already exists
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'expiry_date' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'quotation expiry date',
                    ],
                ];

                $connection = $this->quoteSetup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.2.3') < 0) {
            // Get module table
            $tableName = $this->quoteSetup->getTable('quotation_quote');

            // Check if the table already exists
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'reminder_date' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'quotation reminder date',
                    ],
                    'reminder_email_sent' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => 0,
                        'nullable' => false,
                        'comment' => 'check reminder email sent or not',
                    ],
                ];

                $connection = $this->quoteSetup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '1.2.4') < 0) {
            // Get module table
            $tableName = $this->quoteSetup->getTable('quotation_quote');

            // Check if the table already exists
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                // Declare data
                $columns = [
                    'expiry_email_sent' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => 0,
                        'nullable' => false,
                        'comment' => 'check expiry email sent or not',
                    ],
                    'expiry_enabled' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => 1,
                        'nullable' => false,
                        'comment' => 'check expiry enable or not',
                    ],
                    'reminder_enabled' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => 1,
                        'nullable' => false,
                        'comment' => 'check reminder enable or not',
                    ],
                ];

                $connection = $this->quoteSetup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            $tableName = $this->quoteSetup->getTable('quotation_quote_tier_item');
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                $connection = $this->quoteSetup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'original_base_price');
                if ($columnExists) {
                    $connection->changeColumn(
                        $tableName,
                        'original_base_price',
                        'base_original_price',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4'
                        ]
                    );
                }

                $connection->addColumn(
                    $tableName,
                    'base_custom_price',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'nullable' => false,
                        'length' => '12,4',
                        'comment' => 'Base custom price'
                    ]
                );
            }

            $tableName = $this->quoteSetup->getTable('quotation_quote');
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                $connection = $this->quoteSetup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'original_base_subtotal');
                if ($columnExists) {
                    $connection->changeColumn(
                        $tableName,
                        'original_base_subtotal',
                        'base_original_subtotal',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => true,
                            'default' => null,
                            'length' => '12,4',
                            'comment' => 'Base original subtotal'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'base_custom_price_total');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_custom_price_total',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base custom price total'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'custom_price_total');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'custom_price_total',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Custom price total'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'base_quote_adjustment');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_quote_adjustment',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base quote adjustment total'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'quote_adjustment');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'quote_adjustment',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Quote adjustment total'
                        ]
                    );
                }

                $quotationQuoteTable = $this->quoteSetup->getTable('quotation_quote');
                $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'email_sent');
                if ($columnExists) {
                    $connection->dropColumn($quotationQuoteTable, 'email_sent');
                }
                $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'send_email');
                if ($columnExists) {
                    $connection->dropColumn($quotationQuoteTable, 'send_email');
                }
                $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'reminder_email_sent');
                if ($columnExists) {
                    $connection->dropColumn($quotationQuoteTable, 'reminder_email_sent');
                }
                $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'expiry_email_sent');
                if ($columnExists) {
                    $connection->dropColumn($quotationQuoteTable, 'expiry_email_sent');
                }

                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_request_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send request email'
                    ]
                );
                $connection->addColumn(
                    $quotationQuoteTable,
                    'request_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Request email sent'
                    ]
                );

                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_quote_canceled_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send quote canceled email'
                    ]
                );
                $connection->addColumn(
                    $quotationQuoteTable,
                    'quote_canceled_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Quote canceled email sent'
                    ]
                );

                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_proposal_accepted_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send proposal accepted email'
                    ]
                );
                $connection->addColumn(
                    $quotationQuoteTable,
                    'proposal_accepted_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Proposal accepted email sent'
                    ]
                );

                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_proposal_expired_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send proposal expired email'
                    ]
                );
                $connection->addColumn(
                    $quotationQuoteTable,
                    'proposal_expired_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Proposal expired email sent'
                    ]
                );

                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_proposal_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send proposal email'
                    ]
                );
                $connection->addColumn(
                    $quotationQuoteTable,
                    'proposal_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Proposal email sent'
                    ]
                );

                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_reminder_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send reminder email'
                    ]
                );
                $connection->addColumn(
                    $quotationQuoteTable,
                    'reminder_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Reminder email sent'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $tableName = $this->quoteSetup->getTable('quotation_quote');
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                $connection = $this->quoteSetup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'fixed_shipping_price');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'fixed_shipping_price',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base custom price'
                        ]
                    );
                }
            }

            //remove is_quotation_quote
            $isQuotationQuoteColumnName = 'is_quotation_quote';
            $quoteTable = $this->quoteSetup->getTable('quote');
            $this->quoteSetup->getConnection()->resetDdlCache($quoteTable);
            if ($this->quoteSetup->getConnection()->tableColumnExists($quoteTable, $isQuotationQuoteColumnName)) {
                $this->quoteSetup->getConnection()->dropColumn($quoteTable, $isQuotationQuoteColumnName);
            }

            $linkedQuotationQuoteIdColumnName = 'linked_quotation_id';
            if (!$this->quoteSetup
                ->getConnection()
                ->tableColumnExists($quoteTable, $linkedQuotationQuoteIdColumnName)
            ) {
                $this->quoteSetup->getConnection()->addColumn(
                    $quoteTable,
                    $linkedQuotationQuoteIdColumnName,
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'unsigned' => true,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'Linked Quotation Quote'
                    ]
                );
                $quotationQuoteTable = $this->quoteSetup->getTable('quotation_quote');
                $this->quoteSetup->getConnection()->addForeignKey(
                    $setup->getFkName(
                        $quoteTable,
                        \Magento\Quote\Model\Quote::KEY_ENTITY_ID,
                        $quotationQuoteTable,
                        'quote_id'
                    ),
                    $quoteTable,
                    $linkedQuotationQuoteIdColumnName,
                    $quotationQuoteTable,
                    'quote_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
                );
            }

            $this->copyProductComment($setup);
            $this->removeUnusedProductComment($setup);
        }

        if (version_compare($context->getVersion(), '2.0.3.2') < 0) {
            $tableName = $this->quoteSetup->getTable('quotation_quote_tier_item');
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                $connection = $this->quoteSetup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'base_cost_price');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_cost_price',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base cost price'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'cost_price');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'cost_price',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Cost price'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'base_discount_amount');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_discount_amount',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base discount amount'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'discount_amount');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'discount_amount',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Discount amount'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'base_row_total');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_row_total',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base row total'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'row_total');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'row_total',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Row total'
                        ]
                    );
                }

                // todo: validate if the index exists
                $connection->addIndex(
                    $tableName,
                    $connection->getIndexName(
                        $tableName,
                        ['qty', 'item_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['qty', 'item_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                );
            }
        }

        if (version_compare($context->getVersion(), '2.1.0') < 0) {
            /**
             * Create table 'quotation_quote_sections'
             */
            $table = $this->quoteSetup->getConnection()->newTable(
                $this->quoteSetup->getTable('quotation_quote_sections')
            )->addColumn(
                'section_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Section ID'
            )->addColumn(
                'quote_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Quotation Quote Id'
            )->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false],
                'Label'
            )->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Sort Order'
            )->addIndex(
                $setup->getIdxName('quotation_quote_sections', ['section_id']),
                ['section_id']
            )->addIndex(
                $setup->getIdxName('quotation_quote_sections', ['quote_id']),
                ['quote_id']
            )->addForeignKey(
                $setup->getFkName(
                    'quotation_quote_sections',
                    'quote_id',
                    $this->quoteSetup->getTable('quotation_quote'),
                    'entity_id'
                ),
                'quote_id',
                $this->quoteSetup->getTable('quotation_quote'),
                'quote_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->setComment(
                'Quotation Quote Sections Table'
            );
            $this->quoteSetup->getConnection()->createTable($table);

            /**
             * Create table 'quotation_quote_sections'
             */
            $table = $this->quoteSetup->getConnection()->newTable(
                $this->quoteSetup->getTable('quotation_quote_section_items')
            )->addColumn(
                'section_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Section Item ID'
            )->addColumn(
                'section_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Section ID'
            )->addColumn(
                'item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Quote Item Id'
            )->addColumn(
                'sort_order',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Sort Order'
            )->addIndex(
                $setup->getIdxName('quotation_quote_section_items', ['section_id']),
                ['section_id']
            )->addIndex(
                $setup->getIdxName('quotation_quote_section_items', ['item_id']),
                ['item_id']
            )->addForeignKey(
                $setup->getFkName(
                    'quotation_quote_section_items',
                    'section_id',
                    $this->quoteSetup->getTable('quotation_quote_sections'),
                    'section_id'
                ),
                'section_id',
                $this->quoteSetup->getTable('quotation_quote_sections'),
                'section_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->addForeignKey(
                $setup->getFkName(
                    'quotation_quote_section_items',
                    'item_id',
                    $this->quoteSetup->getTable('quote_item'),
                    'item_id'
                ),
                'item_id',
                $this->quoteSetup->getTable('quote_item'),
                'item_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_NO_ACTION
            )->setComment(
                'Quotation Quote Sections Table'
            );
            $this->quoteSetup->getConnection()->createTable($table);
        }

        if (version_compare($context->getVersion(), '2.1.0') < 0) {
            $isQuotationQuoteColumnName = 'is_quotation_quote';
            $quoteTable = $this->quoteSetup->getTable('quote');
            if (!$this->quoteSetup->getConnection()->tableColumnExists($quoteTable, $isQuotationQuoteColumnName)) {
                /**
                 * Alter quote table with is_quotation_quote
                 */
                $this->quoteSetup->getConnection()->addColumn(
                    $this->quoteSetup->getTable('quote'),
                    'is_quotation_quote',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => '0',
                        'comment' => 'Is Quotation Quote'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '2.1.1') < 0) {
            $tableName = $this->quoteSetup->getTable('quotation_quote');
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                $connection = $this->quoteSetup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'quotation_created_at');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'quotation_created_at',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            'unsigned' => true,
                            'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                            'comment' => 'Quotation created date'
                        ]
                    );
                }
            }
        }

        if (version_compare($context->getVersion(), '2.1.6') < 0) {
            $this->quoteSetup->getConnection()->changeColumn(
                $this->quoteSetup->getTable('quote'),
                'customer_note',
                'customer_note',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'comment' => 'Quotation Customer Note'
                ]
            );
        }

        if (version_compare($context->getVersion(), '2.1.8') < 0) {
            $tableName = $this->quoteSetup->getTable('quotation_quote_tier_item');
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                $connection = $this->quoteSetup->getConnection();

                $columnExists = $connection->tableColumnExists($tableName, 'tax_amount');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'tax_amount',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'tax amount'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'base_tax_amount');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_tax_amount',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'base tax amount'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'base_price_incl_tax');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_price_incl_tax',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base price incl tax'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'price_incl_tax');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'price_incl_tax',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Price incl tax'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'base_row_total_incl_tax');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_row_total_incl_tax',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base row total incl tax'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'row_total_incl_tax');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'row_total_incl_tax',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Row total incl tax'
                        ]
                    );
                }
            }

            $tableName = $this->quoteSetup->getTable('quotation_quote');
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                $connection = $this->quoteSetup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'base_original_subtotal_incl_tax');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_original_subtotal_incl_tax',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base original subtotal incl tax'
                        ]
                    );
                }
                $connection = $this->quoteSetup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'original_subtotal_incl_tax');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'original_subtotal_incl_tax',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Original subtotal incl tax'
                        ]
                    );
                }
            }
        }

        if (version_compare($context->getVersion(), '2.2.3') < 0) {
            $tableName = $this->quoteSetup->getTable('quotation_quote_tier_item');
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                $connection = $this->quoteSetup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'original_tax_amount');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'original_tax_amount',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'original tax amount'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'original_base_tax_amount');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'original_base_tax_amount',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'original base tax amount'
                        ]
                    );
                }
            }
        }

        if (version_compare($context->getVersion(), '2.2.6') < 0) {
            $tableName = $this->quoteSetup->getTable('quotation_quote_tier_item');
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                $connection = $this->quoteSetup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'discount_tax_compensation_amount');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'discount_tax_compensation_amount',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => true,
                            'length' => '20,4',
                            'comment' => 'Discount Tax Compensation Amount'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'base_discount_tax_compensation_amount');
                if (!$columnExists) {
                    $connection->addColumn(
                        $tableName,
                        'base_discount_tax_compensation_amount',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => true,
                            'length' => '20,4',
                            'comment' => 'Base Discount Tax Compensation Amount	'
                        ]
                    );
                }
            }
        }

        if (version_compare($context->getVersion(), '2.2.6') < 0) {
            $tableName = $this->quoteSetup->getTable('quotation_quote_tier_item');
            if ($this->quoteSetup->getConnection()->isTableExists($tableName) == true) {
                $connection = $this->quoteSetup->getConnection();
                $columnExists = $connection->tableColumnExists($tableName, 'base_cost_price');
                $baseCostColumnExists = $connection->tableColumnExists($tableName, 'base_cost');
                if ($columnExists && !$baseCostColumnExists) {
                    $connection->changeColumn(
                        $tableName,
                        'base_cost_price',
                        'base_cost',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                            'nullable' => false,
                            'length' => '12,4',
                            'comment' => 'Base cost price'
                        ]
                    );
                }

                $columnExists = $connection->tableColumnExists($tableName, 'cost_price');
                if ($columnExists) {
                    $connection->dropColumn(
                        $tableName,
                        'cost_price'
                    );
                }
                if ($baseCostColumnExists) {
                    $connection->dropColumn(
                        $tableName,
                        'base_cost_price'
                    );
                }
            }
        }

        if (version_compare($context->getVersion(), '2.2.8') < 0) {
            $connection = $this->quoteSetup->getConnection();
            $quotationQuoteTable = $this->quoteSetup->getTable('quotation_quote');

            //add send_quote_edited_email
            $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'send_quote_edited_email');
            if (!$columnExists) {
                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_quote_edited_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send quote edited email',
                        'after' => 'reminder_email_sent'
                    ]
                );
            }

            //add quote_edited_email_sent
            $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'quote_edited_email_sent');
            if (!$columnExists) {
                $connection->addColumn(
                    $quotationQuoteTable,
                    'quote_edited_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Quote edited email sent',
                        'after' => 'send_quote_edited_email'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '2.2.10') < 0) {
            $connection = $this->quoteSetup->getConnection();
            $tableName = $this->quoteSetup->getTable('quotation_quote_sections');
            $columnExists = $connection->tableColumnExists($tableName, 'is_unassigned');
            if (!$columnExists) {
                $connection->addColumn(
                    $tableName,
                    'is_unassigned',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Is unassigned section',
                        'default' => '0',
                        'after' => 'sort_order'
                    ]
                );
            }

            //add internal_comment
            $quotationQuoteTable = $this->quoteSetup->getTable('quotation_quote');
            $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'internal_comment');
            if (!$columnExists) {
                $connection->addColumn(
                    $quotationQuoteTable,
                    'internal_comment',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'comment' => 'Quote internal message'
                    ]
                );
            }

            //add reporting table
            $table = 'quotation_aggregated';
            if (!$this->quoteSetup->getConnection()->isTableExists($table)) {
                $table = $this->quoteSetup->getConnection()->newTable(
                    $this->quoteSetup->getTable($table)
                )->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Id'
                )->addColumn(
                    'period',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                    null,
                    [],
                    'Period'
                )->addColumn(
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true],
                    'Store Id'
                )->addColumn(
                    'quote_status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    32,
                    ['nullable' => false, 'unsigned' => true],
                    'Quote Status'
                )->addColumn(
                    'quotes_count',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['nullable' => false, 'default' => '0'],
                    'Quotes count'
                )->addColumn(
                    'total_item_qty_quoted',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '20,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Quote Items'
                )->addColumn(
                    'total_quoted_amount',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '20,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Total Quoted Amount'
                )->addColumn(
                    'total_tax_amount',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '20,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Total Tax Amount'
                )->addColumn(
                    'total_shipping_amount',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '20,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Total Shipping Amount'
                )->addColumn(
                    'total_qty_quoted',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Total Qty Quoted'
                )->addColumn(
                    'total_qty_proposal',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Total Qty Proposed'
                )->addColumn(
                    'total_qty_ordered',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Total Qty Ordered'
                )->addColumn(
                    'total_qty_canceled',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false, 'default' => '0.0000'],
                    'Total Qty Canceled'
                )->addIndex(
                    $setup->getIdxName(
                        $table,
                        ['period', 'store_id', 'quote_status'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['period', 'store_id', 'quote_status'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )->addIndex(
                    $setup->getIdxName($table, ['store_id']),
                    ['store_id']
                )->addIndex(
                    $setup->getIdxName($table, ['quote_status']),
                    ['quote_status']
                )->addForeignKey(
                    $setup->getFkName($table, 'store_id', 'store', 'store_id'),
                    'store_id',
                    $this->quoteSetup->getTable('store'),
                    'store_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->setComment(
                    'Quotation Aggregated'
                );

                $this->quoteSetup->getConnection()->createTable($table);
            }
        }

        if (version_compare($context->getVersion(), '2.3.0') < 0) {
            $connection = $this->quoteSetup->getConnection();
            $quotationQuoteTable = $this->quoteSetup->getTable('quotation_quote');
            $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'created_by');
            if (!$columnExists) {
                $connection->addColumn(
                    $quotationQuoteTable,
                    'created_by',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'Created by admin or customer'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '3.0.3') < 0) {
            $connection = $this->quoteSetup->getConnection();
            $quotationQuoteTable = $this->quoteSetup->getTable('quotation_quote');

            //add send_proposal_rejected_email
            $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'send_proposal_rejected_email');
            if (!$columnExists) {
                $connection->addColumn(
                    $quotationQuoteTable,
                    'send_proposal_rejected_email',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Send proposal rejected email',
                        'after' => 'quote_edited_email_sent'
                    ]
                );
            }

            //add proposal_rejected_email_sent
            $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'proposal_rejected_email_sent');
            if (!$columnExists) {
                $connection->addColumn(
                    $quotationQuoteTable,
                    'proposal_rejected_email_sent',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Proposal rejected email sent',
                        'after' => 'send_proposal_rejected_email'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '3.0.4') < 0) {
            $connection = $this->quoteSetup->getConnection();
            $quotationQuoteTable = $this->quoteSetup->getTable('quotation_quote');

            //remove foreign key QUOTATION_QUOTE_SECTIONS_QUOTE_ID_QUOTATION_QUOTE_ENTITY_ID
            $connection->dropForeignKey(
                $this->quoteSetup->getTable('quotation_quote_sections'),
                $setup->getFkName(
                    'quotation_quote_sections',
                    'quote_id',
                    $this->quoteSetup->getTable('quotation_quote'),
                    'entity_id'
                )
            );

            //add foreign key QUOTATION_QUOTE_SECTIONS_QUOTE_ID_QUOTATION_QUOTE_QUOTE_ID
            $connection->addForeignKey(
                $setup->getFkName(
                    'quotation_quote_sections',
                    'quote_id',
                    $this->quoteSetup->getTable('quotation_quote'),
                    'quote_id'
                ),
                $this->quoteSetup->getTable('quotation_quote_sections'),
                'quote_id',
                $this->quoteSetup->getTable('quotation_quote'),
                'quote_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

            //remove foreign key FK_E10CF18C373AEE1ADB5317A97FC02DA1
            $connection->dropForeignKey(
                $this->quoteSetup->getTable('quotation_quote_section_items'),
                $setup->getFkName(
                    'quotation_quote_section_items',
                    'section_id',
                    $this->quoteSetup->getTable('quotation_quote_sections'),
                    'section_id'
                )
            );

            //add foreign key FK_E10CF18C373AEE1ADB5317A97FC02DA1 ?
            $connection->addForeignKey(
                $setup->getFkName(
                    'quotation_quote_section_items',
                    'section_id',
                    $this->quoteSetup->getTable('quotation_quote_sections'),
                    'section_id'
                ),
                $this->quoteSetup->getTable('quotation_quote_section_items'),
                'section_id',
                $this->quoteSetup->getTable('quotation_quote_sections'),
                'section_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

            //remove foreign key QUOTATION_QUOTE_SECTION_ITEMS_ITEM_ID_QUOTE_ITEM_ITEM_ID
            $connection->dropForeignKey(
                $this->quoteSetup->getTable('quotation_quote_section_items'),
                $setup->getFkName(
                    'quotation_quote_section_items',
                    'item_id',
                    $this->quoteSetup->getTable('quote_item'),
                    'item_id'
                )
            );

            //add foreign key QUOTATION_QUOTE_SECTION_ITEMS_ITEM_ID_QUOTE_ITEM_ITEM_ID
            $connection->addForeignKey(
                $setup->getFkName(
                    'quotation_quote_section_items',
                    'item_id',
                    $this->quoteSetup->getTable('quote_item'),
                    'item_id'
                ),
                $this->quoteSetup->getTable('quotation_quote_section_items'),
                'item_id',
                $this->quoteSetup->getTable('quote_item'),
                'item_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            );

            //add created_in_backend
            $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'created_in_backend');
            if (!$columnExists) {
                $connection->addColumn(
                    $quotationQuoteTable,
                    'created_in_backend',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'comment' => 'Is quote created in the backend',
                        'after' => 'is_quote'
                    ]
                );
            }

            //add admin_creator
            $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'admin_creator_id');
            if (!$columnExists) {
                $connection->addColumn(
                    $quotationQuoteTable,
                    'admin_creator_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'unsigned' => true,
                        'comment' => 'ID of admin that created the quote',
                        'after' => 'created_in_backend'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '3.0.5') < 0) {
            $connection = $this->quoteSetup->getConnection();

            //add reject_message column (originaly executed in 2.2.7 in UpgradeData.php)
            $quotationQuoteTable = $this->quoteSetup->getTable('quotation_quote');
            $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'reject_message');
            if (!$columnExists) {
                $connection->addColumn(
                    $setup->getTable('quotation_quote'),
                    'reject_message',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'The rejection message'
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '3.0.6') < 0) {
            $connection = $this->quoteSetup->getConnection();
            $quotationQuoteTable = $this->quoteSetup->getTable('quotation_quote');
            $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'cloned_quote_id');

            if (!$columnExists) {
                $connection->addColumn(
                    $quotationQuoteTable,
                    'cloned_quote_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'unsigned' => true,
                        'nullable' => true,
                        'comment' => 'Assigned cloned quote id'
                    ]
                );
            }

            $columnExists = $connection->tableColumnExists($quotationQuoteTable, 'cloned_quote');
            if (!$columnExists) {
                $connection->addColumn(
                    $quotationQuoteTable,
                    'cloned_quote',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'unsigned' => true,
                        'default' => false,
                        'nullable' => false,
                        'comment' => 'Is quote cloned'
                    ]
                );
            }
        }

        $setup->endSetup();
    }

    /**
     * Copy the product comments
     *
     * @param SchemaSetupInterface $setup
     * @return void
     */
    public function copyProductComment(SchemaSetupInterface $setup)
    {
        $sql = sprintf(
            "UPDATE %s as item
             INNER JOIN %s as tier
             ON tier.item_id = item.item_id
             SET item.description = (CONCAT(COALESCE(item.description, ''), COALESCE(tier.comment, '')));",
            $this->quoteSetup->getTable('quote_item'),
            $this->quoteSetup->getTable('quotation_quote_tier_item')
        );

        $this->quoteSetup->getConnection()->query($sql);
    }

    /**
     * Remove comment from tier item
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function removeUnusedProductComment(SchemaSetupInterface $setup)
    {
        $quotationTierItem = $this->quoteSetup->getTable('quotation_quote_tier_item');

        if ($this->quoteSetup->getConnection()->tableColumnExists($quotationTierItem, 'comment')) {
            $this->quoteSetup->getConnection()->dropColumn(
                $quotationTierItem,
                'comment'
            );
        }

        if ($this->quoteSetup->getConnection()->tableColumnExists($quotationTierItem, 'item_has_comment')) {
            $this->quoteSetup->getConnection()->dropColumn(
                $quotationTierItem,
                'item_has_comment'
            );
        }
    }
}
