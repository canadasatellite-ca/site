<?php

namespace CanadaSatellite\Theme\Plugin\Model;

use Magento\Framework\Event\ManagerInterface;
use MageWorx\OptionFeatures\Helper\Data as Helper;
use MageWorx\OptionFeatures\Model\Price as AdvancedPricingPrice;

class AroundGetFinalPrice
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Core event manager proxy
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var AdvancedPricingPrice
     */
    protected $advancedPricingPrice;

    /**
     * @param ManagerInterface $eventManager
     * @param Helper $helper
     * @param AdvancedPricingPrice $advancedPricingPrice
     */
    function __construct(
        ManagerInterface $eventManager,
        AdvancedPricingPrice $advancedPricingPrice,
        Helper $helper
    ) {
        $this->eventManager         = $eventManager;
        $this->advancedPricingPrice = $advancedPricingPrice;
        $this->helper               = $helper;
    }

    /**
     * @param \Magento\Catalog\Model\Product\Type\Price $subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Product $product
     * @param null $qty
     * @return mixed
     */
    function aroundGetFinalPrice($subject, $proceed, $qty, $product)
    {
        //magento recalculates prices
        $result = $proceed($qty, $product);
        // Validate the current product and configuration, (!) important
        if (!$this->validate($product)) {
            return $result;
        }

        $totalPrice = 0;
        $this->eventManager->dispatch('catalog_product_get_final_price', ['product' => $product, 'qty' => $qty]);

        $basePrice = min($product->getData('final_price'), $subject->getBasePrice($product, $qty));
        $optionIds = $product->getCustomOption('option_ids');
        $qty = $qty ?: 1;
        $this->advancedPricingPrice->setProductQty($qty);
        foreach (explode(',', $optionIds->getValue()) as $optionId) {
            /** @var \Magento\Catalog\Model\Product\Option $option */
            $option = $product->getOptionById($optionId);
            if (!$option) {
                continue;
            }

            $confItemOption = $product->getCustomOption('option_' . $option->getId());
            /** @var \Magento\Catalog\Model\Product\Option\Type\DefaultType $group */
            $group       = $option->groupFactory($option->getType())
                                  ->setOption($option)
                                  ->setConfigurationItemOption($confItemOption);
            $optionPrice = $group->getOptionPrice($confItemOption->getValue(), $basePrice);

            // divide the option price into qty if the "one_time" option is enabled
            if ($option->getData(Helper::KEY_ONE_TIME) && $this->helper->isOneTimeEnabled()) {
                $optionPrice = $optionPrice / $qty;
            }
            if ($optionPrice > 0) {
                $totalPrice += $optionPrice;
            }
        }

        if (!$this->helper->isAbsolutePriceEnabled() || !$product->getAbsolutePrice()) {
            $totalPrice += $basePrice;
        }

        return $totalPrice > 0 ? $totalPrice : $result;
    }

    /**
     * Validate product and configuration
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    protected function validate($product)
    {
        if (!$product->hasCustomOptions()) {
            return false;
        }

        $optionIds = $product->getCustomOption('option_ids');
        if (!$optionIds) {
            return false;
        }

        return true;
    }
}
