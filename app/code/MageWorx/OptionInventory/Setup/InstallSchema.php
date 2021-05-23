<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OptionInventory\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var \MageWorx\OptionBase\Model\Installer
     */
    protected $optionBaseInstaller;

    /**
     *
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    function __construct(
        \MageWorx\OptionBase\Model\Installer $optionBaseInstaller
    ) {
        $this->optionBaseInstaller = $optionBaseInstaller;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->optionBaseInstaller->install();
    }
}
