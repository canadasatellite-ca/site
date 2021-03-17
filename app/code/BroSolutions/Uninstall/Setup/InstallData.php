<?php
namespace BroSolutions\Uninstall\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


/**
 * Class InstallData
 *
 * @package BroSolutions\Uninstall\Setup
 */
class InstallData implements InstallDataInterface
{
    const REMOVE_MODULES_PRODUCT_ATTRIBUTES = [
      'CollinsHarper_CanadaPost' => [
          'ship_req_signature',
          'ship_req_proof_of_age',
          'hs_tariff_code',
          'origin_province',
          'restrict_shipping_methods',
          'allowed_shipping_methods'
      ],
      'CollinsHarper_Core' => [
          'ready_to_ship',
          'shipping_length',
          'shipping_width',
          'shipping_height'
      ]
    ];

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        foreach (self::REMOVE_MODULES_PRODUCT_ATTRIBUTES as $attributes) {
            foreach ($attributes as $attribute) {
                $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, $attribute);
            }
        }
    }
}
