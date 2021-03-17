<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_REASSIGN_ORDER
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */
namespace Itoris\ReassignOrder\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public $magentoConfigTable = 'core_config_data';
    const XML_PATH_MODULE_ENABLED = 'itoris_reassignorder/general/enabled';
    const XML_PATH_MODULE_EMAIL_TEMPLATE = 'itoris_reassignorder/general/email_template';
    const XML_PATH_MODULE_NOTIFY_CUSTOMER = 'itoris_reassignorder/general/notify_customer';
    const XML_PATH_MODULE_OVERWRITE_CUSTOMER_NAME = 'itoris_reassignorder/general/overwrite_customer_name';
    const XML_PATH_MODULE_AUTO_REASSIGN = 'itoris_reassignorder/general/auto_reassign';
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Itoris\ReassignOrder\Helper\Data $helper */
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->create('Itoris\ReassignOrder\Helper\Data');
        $setup->startSetup();
        $configNote = $helper->getBackendConfig()->getValue(self::XML_PATH_MODULE_ENABLED);
        if(!isset($configNote)){
            $setup->run("
            INSERT INTO {$setup->getTable($this->magentoConfigTable)}
            (path, value)
            VALUES('".self::XML_PATH_MODULE_ENABLED."', '1')
            ");
        }
        $configNote = $helper->getBackendConfig()->getValue(self::XML_PATH_MODULE_EMAIL_TEMPLATE);
        if(!isset($configNote)){
            $setup->run("
            INSERT INTO {$setup->getTable($this->magentoConfigTable)}
            (path, value)
            VALUES('".self::XML_PATH_MODULE_EMAIL_TEMPLATE."', 'itoris_email_reassignorder')
            ");
        }
        $configNote = $helper->getBackendConfig()->getValue(self::XML_PATH_MODULE_NOTIFY_CUSTOMER);
        if(!isset($configNote)){
            $setup->run("
            INSERT INTO {$setup->getTable($this->magentoConfigTable)}
            (path, value)
            VALUES('".self::XML_PATH_MODULE_NOTIFY_CUSTOMER."', '1')
            ");
        }
        $configNote = $helper->getBackendConfig()->getValue(self::XML_PATH_MODULE_OVERWRITE_CUSTOMER_NAME);
        if(!isset($configNote)){
            $setup->run("
            INSERT INTO {$setup->getTable($this->magentoConfigTable)}
            (path, value)
            VALUES('".self::XML_PATH_MODULE_OVERWRITE_CUSTOMER_NAME."', '0')
            ");
        }
        $configNote = $helper->getBackendConfig()->getValue(self::XML_PATH_MODULE_AUTO_REASSIGN);
        if(!isset($configNote)){
            $setup->run("
            INSERT INTO {$setup->getTable($this->magentoConfigTable)}
            (path, value)
            VALUES('".self::XML_PATH_MODULE_AUTO_REASSIGN."', '0')
            ");
        }
        $setup->endSetup();
    }
}
