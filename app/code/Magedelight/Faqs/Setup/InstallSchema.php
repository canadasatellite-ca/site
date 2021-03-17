<?php
/* Magedelight
 * Copyright (C) 2016 Magedelight <info@magedelight.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category Magedelight
 * @package Magedelight_Faqs
 * @copyright Copyright (c) 2016 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 * 
 */
namespace Magedelight\Faqs\Setup;
 
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
 
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
     // @codingStandardsIgnoreStart
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {    // @codingStandardsIgnoreEnd
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('md_faq');
        $tableComment = 'Faq management for faq module';
        $columns = [
            'question_id' => [
                'type' => Table::TYPE_INTEGER,
                'size' => null,
                'options' => ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'comment' => 'Question Id',
            ],
            'question' => [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'options' => ['nullable' => false, 'default' => ''],
                'comment' => 'Question',
            ],
            'answer' => [
                'type' => Table::TYPE_TEXT,
                'size' => 2048,
                'options' => ['nullable' => true, 'default' => null],
                'comment' => 'Answer',
            ],
            'store_id' => [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'options' => ['nullable' => false, 'default' => 0],
                'comment' => 'Store Id',
            ],
            
            'position' => [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'options' => ['nullable' => false, 'default' => ''],
                'comment' => 'Position',
            ],
            'tags' => [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'options' => ['nullable' => false, 'default' => ''],
                'comment' => 'Tags',
            ],
            'status' => [
                'type' => Table::TYPE_BOOLEAN,
                'size' => null,
                'options' => ['nullable' => false, 'default' => 0],
                'comment' => 'Question status',
            ],
            'is_most_viewed' => [
                'type' => Table::TYPE_INTEGER,
                'size' => 11,
                'options' => ['nullable' => false, 'default' => 2],
                'comment' => 'Question Most Viewed',
            ],
            'created_by' => [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'options' => ['nullable' => false, 'default' => 'admin'],
                'comment' => 'Question created owner',
            ],
            'creation_time' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                '2M',
                'nullable' => false,
                'size' => null,
                'options'=>  [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                ],
                'comment' => 'Question Creation Time',
            ],
            'update_time' => [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                '2M',
                'nullable' => false,
                'size' => null,
                'options'=> [
                    'nullable' => false,
                    'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
                ],
                'comment' => 'Question Modification Time',
            ],
            'customer_id' => [
                    'type' => Table::TYPE_INTEGER,
                    'size' => null,
                    'options' => ['nullable' => false, 'default' => '0'],
                    'comment' => 'Question customer',
            ],
            'question_type' => [
                'type' => Table::TYPE_BOOLEAN,
                'size' => null,
                'options' => ['nullable' => false, 'default' => 0],
                'comment' => 'Question Type',
            ],
            'customer_name' => [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'options' => ['nullable' => false, 'default' => ''],
                'comment' => 'Guest Name',
            ],
            'customer_email' => [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'options' => ['nullable' => false, 'default' => ''],
                'comment' => 'Guest Email',
            ]
        ];
        
        $table = $installer->getConnection()->newTable($tableName);
        foreach ($columns as $name => $values) {
            $table->addColumn(
                $name,
                $values['type'],
                $values['size'],
                $values['options'],
                $values['comment']
            );
        }
        $table->addColumn(
                    'like',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    255,
                    ['identity' => false, 'nullable' => false, 'default' => 0, 'primary' => false],
                    'Question Likes'
                )
                ->addColumn(
                    'dislike',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    255,
                    ['identity' => false, 'nullable' => false, 'default' => 0, 'primary' => false],
                    'Question Dislikes'
                )
                ->addColumn(
                    'question_margin_bottom',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Question Margin Bottom'
                )
                ->addColumn(
                    'font_size',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Font Size'
                )
                ->addColumn(
                    'text_color',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Text Color'
                )
                ->addColumn(
                    'text_color_active',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Text Color Active'
                )
                ->addColumn(
                    'background',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Background'
                )
                ->addColumn(
                    'backgrond_active',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Backgrond Active'
                )
                ->addColumn(
                    'icon',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Icon'
                )
                ->addColumn(
                    'border_width',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Border Width'
                )
                ->addColumn(
                    'border_color',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Border Color'
                )
                ->addColumn(
                    'border_radius',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Border Radius'
                )
                ->addColumn(
                    'icon_class',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Icon Class'
                )
                ->addColumn(
                    'icon_active',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Icon Active'
                )
                ->addColumn(
                    'animation_type',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Animation Type'
                )
                ->addColumn(
                    'animation_speed',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => false, 'primary' => false],
                    'Animation Speed'
                );
        $table->addIndex(
            $installer->getIdxName($tableName, ['question_id']),
            ['question_id']
        )->addIndex(
            $setup->getIdxName($tableName, ['question'], AdapterInterface::INDEX_TYPE_FULLTEXT),
            ['question'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        );
        $table->setComment($tableComment);
        $installer->getConnection()->createTable($table);
        if (!$installer->tableExists('md_categories')) {
            $table = $installer->getConnection()
            ->newTable($installer->getTable('md_categories'));
                $table->addColumn(
                  'category_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Category Id'
                    )     
                    ->addColumn(
                        'title',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        null,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Category Title'
                    )
                    ->addColumn(
                        'position',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Position'
                    )
                    ->addColumn(
                        'url_key',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        250,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Category Url Key'
                    )
                    ->addColumn(
                        'description',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        null,
                        ['identity' => false, 'nullable' => true, 'default' => null, 'primary' => false],
                        'Description'
                    )
                    ->addColumn(
                        'page_layout',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Page Layout'
                    )
                    ->addColumn(
                        'list_mode',
                        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        null,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'List Mode'
                    )
                    ->addColumn(
                        'grid_column',
                        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        null,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Grid Column'
                    )
                    ->addColumn(
                        'store_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Store Id'
                    )
                    ->addColumn(
                        'customer_groups',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Customer Groups'
                    )
                    ->addColumn(
                        'page_title',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Page Title'
                    )
                    ->addColumn(
                        'meta_keywords',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        null,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Category Meta Keywords'
                    )
                    ->addColumn(
                        'meta_description',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        null,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Category Meta Description'
                    )
                    ->addColumn(
                        'status',
                        \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        null,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Category Status'
                    )
                    ->addColumn(
                        'image',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Category Image'
                    )
                    ->addColumn(
                        'image_url',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Category Image Url'
                    )
                    ->addColumn(
                        'question_margin_bottom',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Question Margin Bottom'
                    )
                    ->addColumn(
                        'font_size',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Font Size'
                    )
                    ->addColumn(
                        'text_color',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Text Color'
                    )
                    ->addColumn(
                        'text_color_active',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Text Color Active'
                    )
                    ->addColumn(
                        'background',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Background'
                    )
                    ->addColumn(
                        'backgrond_active',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Backgrond Active'
                    )
                    ->addColumn(
                        'icon',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Icon'
                    )
                    ->addColumn(
                        'border_width',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Border Width'
                    )
                    ->addColumn(
                        'border_color',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Border Color'
                    )
                    ->addColumn(
                        'border_radius',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Border Radius'
                    )
                    ->addColumn(
                        'icon_class',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Icon Class'
                    )
                    ->addColumn(
                        'icon_active',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Icon Active'
                    )
                    ->addColumn(
                        'animation_type',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Animation Type'
                    )
                    ->addColumn(
                        'animation_speed',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Animation Speed'
                    )
                    ->addColumn(
                        'created_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        null,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Category Creation Time'
                    )
                    ->addColumn(
                        'updated_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                        null,
                        ['identity' => false, 'nullable' => false, 'primary' => false],
                        'Category Updation Time'
                    )
                    ->addIndex(
                        $installer->getIdxName($installer->getTable('md_categories'), ['category_id']),
                        ['category_id']
                    )->addIndex(
                        $setup->getIdxName(
                            $installer->getTable('md_categories'),
                            ['title'],
                            AdapterInterface::INDEX_TYPE_FULLTEXT
                        ),
                        ['title'],
                        ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
                    )
                ->setComment('Question Category Table');
             $installer->getConnection()->createTable($table);
        }
        
        if (!$installer->tableExists('md_faq_product')) {
            $table = $installer->getConnection()
                    ->newTable($installer->getTable('md_faq_product'));
            $table->addColumn(
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
                        $installer->getIdxName('md_faq_product', ['product_id']),
                        ['product_id']
                    )
                    ->addForeignKey(
                        $installer->getFkName('md_faq_product', 'question_id', 'md_faq', 'question_id'),
                        'question_id',
                        $installer->getTable('md_faq'),
                        'question_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $installer->getFkName('md_faq_product', 'product_id', 'catalog_product_entity', 'entity_id'),
                        'product_id',
                        $installer->getTable('catalog_product_entity'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->addIndex(
                        $installer->getIdxName(
                            'md_faq_product',
                            [
                                'question_id',
                                'product_id'
                                    ],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        [
                        'question_id',
                        'product_id'
                            ],
                        [
                            'type' => AdapterInterface::INDEX_TYPE_UNIQUE
                            ]
                    )
                    ->setComment('Faq To product Link Table');
            $installer->getConnection()->createTable($table);
        }
        if (!$installer->tableExists('md_category_question')) {
            $table = $installer->getConnection()->newTable($installer->getTable('md_category_question'));
            $table->addColumn(
                  'category_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'Category Id'
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
                        $installer->getIdxName('md_category_question', ['question_id']),
                        ['question_id']
                    )
                    ->addForeignKey(
                        $installer->getFkName('md_category_question', 'question_id', 'md_faq', 'question_id'),
                        'question_id',
                        $installer->getTable('md_faq'),
                        'question_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $installer->getFkName('md_category_question', 'category_id', 'md_categories', 'category_id'),
                        'category_id',
                        $installer->getTable('md_categories'),
                        'category_id',
                        Table::ACTION_CASCADE
                    )
                    ->addIndex(
                        $installer->getIdxName(
                            'md_category_question',
                            [
                                'question_id',
                                'category_id'
                            ],
                            AdapterInterface::INDEX_TYPE_UNIQUE
                        ),
                        [
                            'question_id',
                            'category_id'
                        ],
                        [
                            'type' => AdapterInterface::INDEX_TYPE_UNIQUE
                        ]
                    )
                    ->setComment('Faq To Category link table');
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
