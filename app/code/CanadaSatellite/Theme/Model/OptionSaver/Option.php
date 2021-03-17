<?php

namespace CanadaSatellite\Theme\Model\OptionSaver;

use Magento\Catalog\Api\ProductCustomOptionRepositoryInterface as OptionRepository;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionBase\Model\Product\Option\Attributes as OptionAttributes;
use MageWorx\OptionTemplates\Model\OptionSaver\Option as OptionSaverOption;
use MageWorx\OptionTemplates\Model\OptionSaver\Value as OptionValueDataCollector;

class Option extends OptionSaverOption
{

    /**
     * @var OptionValueDataCollector
     */
    protected $optionValueDataCollector;

    /**
     * @var OptionAttributes
     */
    protected $optionAttributes;

    /**
     * Option constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param OptionValueDataCollector $optionValueDataCollector
     * @param OptionRepository $optionRepository
     * @param OptionAttributes $optionAttributes
     * @param BaseHelper $baseHelper
     * @param string|null $connectionName
     */

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        OptionValueDataCollector $optionValueDataCollector,
        OptionRepository $optionRepository,
        OptionAttributes $optionAttributes,
        BaseHelper $baseHelper,
        string $connectionName = null)
    {
        parent::__construct(
            $context,
            $currencyFactory,
            $storeManager,
            $config,
            $optionValueDataCollector,
            $optionRepository,
            $optionAttributes,
            $baseHelper,
            $connectionName);
    }

    /**
     * Collect option's data
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionInterface $option
     * @param array $optionData
     * @return void
     */
    protected function collectOptionData($product, &$option, &$optionData)
    {
        $option['group_option_id'] = $option->getData('group_option_id');

        $data = [
            'option_id' => $option->getData('option_id'),
            'product_id' => $product->getData($product->getResource()->getLinkField()),
            'group_option_id' => $option->getData('group_option_id'),
            'type' => $option->getData('type'),
            'is_require' => $option->getData('is_require'),
            'sku' => $option->getData('sku'),
            'max_characters' => $option->getData('max_characters'),
            'file_extension' => $option->getData('file_extension'),
            'image_size_x' => $option->getData('image_size_x'),
            'image_size_y' => $option->getData('image_size_y'),
            'sort_order' => $option->getData('sort_order'),
            'currency_code' => $option->getData('currency_code')
        ];

        foreach ($this->optionAttributes->getData() as $attribute) {
            if (!$attribute->hasOwnTable()) {
                $data[$attribute->getName()] = $attribute->prepareDataBeforeSave($option);
            }
        }

        $catalogProductOptionTable = $this->getTable(OptionSaverOption::TABLE_NAME_CATALOG_PRODUCT_OPTION);
        $optionData[OptionSaverOption::TABLE_NAME_CATALOG_PRODUCT_OPTION][$option->getOptionId()] = $this->_prepareDataForTable(
            new \Magento\Framework\DataObject($data),
            $catalogProductOptionTable
        );

        $this->collectPriceData($option, $optionData);
        $this->collectTitleData($option, $optionData);

        if (!empty($option->getValues())) {
            $this->optionValueDataCollector->collectValuesBeforeInsert($option, $optionData);
        }
    }
}