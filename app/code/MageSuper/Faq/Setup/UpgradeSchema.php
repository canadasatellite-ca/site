<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageSuper\Faq\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $configWriter;

    /**
     *
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(WriterInterface $configWriter
    )
    {
        $this->configWriter = $configWriter;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            if (!$installer->tableExists('md_pcategory_question')) {
                $table = $installer->getConnection()->newTable($installer->getTable('md_pcategory_question'));
                $table->addColumn(
                    'pcategory_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'ProductCategory Id'
                )
                    ->addColumn(
                        'question_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                            'primary' => true,
                        ],
                        'Question ID'
                    )
                    ->addIndex(
                        $installer->getIdxName('md_pcategory_question', ['question_id']),
                        ['question_id']
                    )
                    ->addForeignKey(
                        $installer->getFkName('md_pcategory_question', 'question_id', 'md_faq', 'question_id'),
                        'question_id',
                        $installer->getTable('md_faq'),
                        'question_id',
                        Table::ACTION_CASCADE
                    )
                    /*->addForeignKey(
                        $installer->getFkName('md_pcategory_question', 'pcategory_id', 'catalog_category_entity', 'entity_id'),
                        'pcategory_id',
                        $installer->getTable('catalog_category_entity'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )*/
                    ->addIndex(
                        $installer->getIdxName(
                            'md_pcategory_question',
                            [
                                'question_id',
                                'pcategory_id'
                            ],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        [
                            'question_id',
                            'pcategory_id'
                        ],
                        [
                            'type' => AdapterInterface::INDEX_TYPE_UNIQUE
                        ]
                    )
                    ->setComment('Faq To ProductCategory link table');
                $installer->getConnection()->createTable($table);
            }
        }

        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            if (!$installer->tableExists('md_pcategory_category')) {
                $table = $installer->getConnection()->newTable($installer->getTable('md_pcategory_category'));
                $table->addColumn(
                    'category_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Category Id'
                )
                    ->addColumn(
                        'pcategory_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                            'primary' => true,
                        ],
                        'ProductCategory ID'
                    )
                    ->addIndex(
                        $installer->getIdxName('md_pcategory_category', ['category_id']),
                        ['category_id']
                    )
                    ->addForeignKey(
                        $installer->getFkName('md_pcategory_category', 'category_id', 'md_categories', 'category_id'),
                        'category_id',
                        $installer->getTable('md_categories'),
                        'category_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $installer->getFkName('md_pcategory_category', 'pcategory_id', 'catalog_category_entity', 'entity_id'),
                        'pcategory_id',
                        $installer->getTable('catalog_category_entity'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addIndex(
                        $installer->getIdxName(
                            'md_pcategory_category',
                            [
                                'category_id',
                                'pcategory_id'
                            ],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        [
                            'category_id',
                            'pcategory_id'
                        ],
                        [
                            'type' => AdapterInterface::INDEX_TYPE_UNIQUE
                        ]
                    )
                    ->setComment('Faq To ProductCategory to QuestionCategory link table');
                $installer->getConnection()->createTable($table);
            }
        }

        if (version_compare($context->getVersion(), '0.0.4', '<')) {
            if (!$installer->tableExists('md_category_product')) {
                $table = $installer->getConnection()->newTable($installer->getTable('md_category_product'));
                $table->addColumn(
                    'category_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Category Id'
                )
                    ->addColumn(
                        'product_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'unsigned' => true,
                            'nullable' => false,
                            'primary' => true,
                        ],
                        'Product ID'
                    )
                    ->addIndex(
                        $installer->getIdxName('md_category_product', ['category_id']),
                        ['category_id']
                    )
                    ->addForeignKey(
                        $installer->getFkName('md_category_product', 'category_id', 'md_categories', 'category_id'),
                        'category_id',
                        $installer->getTable('md_categories'),
                        'category_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $installer->getFkName('md_category_product', 'product_id', 'catalog_product_entity', 'entity_id'),
                        'product_id',
                        $installer->getTable('catalog_product_entity'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addIndex(
                        $installer->getIdxName(
                            'md_pcategory_category',
                            [
                                'category_id',
                                'product_id'
                            ],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        [
                            'category_id',
                            'product_id'
                        ],
                        [
                            'type' => AdapterInterface::INDEX_TYPE_UNIQUE
                        ]
                    )
                    ->setComment('Faq To Question Category to Product link table');
                $installer->getConnection()->createTable($table);
            }
        }
        if (version_compare($context->getVersion(), '0.0.5', '<')) {
            $table = $installer->getTable('md_categories');
            $installer->getConnection()->addColumn(
                $table,
                'parent_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 11,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Parent Category Id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.6', '<')) {
            $table = $installer->getTable('md_categories');
            $installer->getConnection()->addColumn(
                $table,
                'path',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Category Path Id'
                ]
            );
            $installer->getConnection()->query('UPDATE md_categories as c SET path=CONCAT(\'0/\',category_id)');
        }

        if (version_compare($context->getVersion(), '0.0.7', '<')) {
            $table = $installer->getTable('md_faq');
            $installer->getConnection()->addColumn(
                $table,
                'remote_ip',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 20,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Remote Ip'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.0.8', '<')) {
            $table = $installer->getTable('md_faq');
            $installer->getConnection()->addColumn(
                $table,
                'score_v3',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
                    'length' => 4,
                    'nullable' => false,
                    'comment' => 'Score V3'
                ]
            );
        }


        $installer->endSetup();
    }
}