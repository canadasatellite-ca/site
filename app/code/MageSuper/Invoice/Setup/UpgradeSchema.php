<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageSuper\Invoice\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

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

        if (version_compare($context->getVersion(), '0.0.1', '<')) {
            $installer->getConnection()->beginTransaction();
            try {
                $installer->getConnection()->query('UPDATE sales_shipment t1
                    INNER JOIN sales_order t2
                         ON t1.order_id = t2.entity_id
                         SET t1.increment_id = t2.increment_id');
                $installer->getConnection()->query('UPDATE sales_shipment_grid t1
                    INNER JOIN sales_order t2
                         ON t1.order_id = t2.entity_id
                         SET t1.increment_id = t2.increment_id');

                $installer->getConnection()->query('UPDATE sales_invoice t1
                    INNER JOIN sales_order t2
                         ON t1.order_id = t2.entity_id
                         SET t1.increment_id = t2.increment_id');
                $installer->getConnection()->query('UPDATE sales_invoice_grid t1
                    INNER JOIN sales_order t2
                         ON t1.order_id = t2.entity_id
                         SET t1.increment_id = t2.increment_id');

                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
        }
        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $installer->getConnection()->beginTransaction();
            try {
                $max = $installer->getConnection()->fetchOne('SELECT entity_id from sales_order order by entity_id DESC LIMIT 1');
                $installer->getConnection()->query('UPDATE sales_order t1 SET t1.increment_id = (t1.increment_id+23) WHERE t1.entity_id>=36015');
                $installer->getConnection()->query('UPDATE sales_order_grid t1 SET t1.increment_id = (t1.increment_id+23) WHERE t1.entity_id>=36015');

                for($i=0;$i<23;$i++){
                    $installer->getConnection()->query('INSERT INTO sequence_order_1 VALUE ()');
                }
                $installer->getConnection()->commit();
            } catch (\Exception $e) {
                $installer->getConnection()->rollBack();
                throw $e;
            }
            $installer->getConnection()->query('ALTER TABLE sales_order AUTO_INCREMENT=' . ($max + 23 + 1) . ';');
        }
        /*$installer->getConnection()->query('UPDATE sales_invoice t1 SET t1.order_id=(t1.order_id + 23) WHERE t1.order_id>=36015');
                $installer->getConnection()->query('UPDATE sales_invoice_grid t1 SET t1.order_id=(t1.order_id + 23) WHERE t1.order_id>=36015');
                $installer->getConnection()->query('UPDATE sales_shipment t1 SET t1.order_id=(t1.order_id + 23) WHERE t1.order_id>=36015');
                $installer->getConnection()->query('UPDATE sales_shipment_grid t1 SET t1.order_id=(t1.order_id + 23) WHERE t1.order_id>=36015');
                */
        $installer->endSetup();
    }
}