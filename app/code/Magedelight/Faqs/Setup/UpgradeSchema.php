<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magedelight\Faqs\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * Upgrade the Partialpayment module DB scheme
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.2') < 1) {
            $this->addSpecialColumn($setup);
        }
        $setup->endSetup();
    }
    
    protected function addSpecialColumn(SchemaSetupInterface $setup)
    {   
        $installer = $setup;
        $connection = $installer->getConnection();
        $giftwrapperSpecial = $installer->getConnection()->tableColumnExists($installer->getTable('md_faq'), 'page_title');
        if ($giftwrapperSpecial == false) {
            $specialColumns = [
                'page_title' => [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'options' => ['nullable' => false, 'default' => ''],
                'comment' => 'page title',
            ],
            
            'meta_keywords' => [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'options' => ['nullable' => false, 'default' => ''],
                'comment' => 'meta keywords',
            ],
            'meta_description' => [
                'type' => Table::TYPE_TEXT,
                'size' => 255,
                'options' => ['nullable' => false, 'default' => ''],
                'comment' => 'meta_description',
            ]
            ];
            foreach ($specialColumns as $name => $definition) {
                $connection->addColumn($installer->getTable('md_faq'), $name, $definition);
            }
        }
    }   
}
