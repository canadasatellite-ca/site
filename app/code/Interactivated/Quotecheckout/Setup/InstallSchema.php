<?php

namespace Interactivated\Quotecheckout\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
	/**
	 * @var \Magento\Framework\Logger\Monolog
	 */
	protected $_logger;

	/**
	 * @var \Magento\Directory\Model\ResourceModel\Country\Collection
	 */
	protected $_countryCollection;

	/**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $_config;

	/**
	 * @param \Magento\Framework\Logger\Monolog $logger
	 * @param \Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection
	 * @param \Magento\Config\Model\ResourceModel\Config $config
	 */
	public function __construct(
		\Magento\Framework\Logger\Monolog $logger,
		\Magento\Directory\Model\ResourceModel\Country\Collection $countryCollection,
		\Magento\Config\Model\ResourceModel\Config $config
	) {
		$this->_logger = $logger;
		$this->_countryCollection = $countryCollection;
		$this->_config = $config;
	}

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$setup->startSetup();

		$setup->endSetup();
    }
}
