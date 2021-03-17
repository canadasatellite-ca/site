<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Interactivated\Price\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * InstallData constructor.
     * @param \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory
     * @param \Magestore\Webpos\Model\Staff\RoleFactory $roleFactory
     * @param \Magestore\Webpos\Model\Staff\AuthorizationRuleFactory $authorizationRuleFactory
     * @param \Magestore\Webpos\Model\Location\LocationFactory $locationFactory
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory,
        \Magestore\Webpos\Model\Staff\RoleFactory $roleFactory,
        \Magestore\Webpos\Model\Staff\AuthorizationRuleFactory $authorizationRuleFactory,
        \Magestore\Webpos\Model\Location\LocationFactory $locationFactory,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $collectionFactory
    ){
    }


    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();
        
        $setup->endSetup();
    }
}
