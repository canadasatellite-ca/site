<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Helper;

use Magento\Catalog\Model\Product;
use Magento\InventoryConfigurationApi\Api\Data\StockItemConfigurationInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventorySales\Model\IsProductSalableForRequestedQtyCondition\ProductSalabilityError;
use Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\InventorySales\Model\IsProductSalableCondition\BackOrderNotifyCustomerCondition;
use Magento\CatalogInventory\Model\StockStateProvider;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class StockCheck
 * @package Cart2Quote\Quotation\Helper
 */
class StockCheck extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface
     */
    protected $isProductSalableForRequestedQty;

    /**
     * @var \Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface
     */
    private $stockByWebsiteId;

    /**
     * @var \Magento\InventorySales\Model\IsProductSalableCondition\BackOrderNotifyCustomerCondition
     */
    private $backOrderNotifyCustomerCondition;

    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    private $objectFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * @var \Magento\CatalogInventory\Model\StockStateProvider
     */
    private $stockStateProvider;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * StockCheck constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\DataObject\Factory $objectFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $moduleManager = $context->getModuleManager();

        //Magento 2.3.x compatibility
        if (interface_exists(GetStockItemConfigurationInterface::class) && $moduleManager->isEnabled('Magento_InventoryConfigurationApi')) {
            $this->getStockItemConfiguration = $objectManager->get(GetStockItemConfigurationInterface::class);
            $this->isProductSalableForRequestedQty = $objectManager->get(IsProductSalableForRequestedQtyInterface::class);
            $this->stockByWebsiteId = $objectManager->get(StockByWebsiteIdResolverInterface::class);
            $this->backOrderNotifyCustomerCondition = $objectManager->get(BackOrderNotifyCustomerCondition::class);
        } else {
            //Magento 2.2.x compatibility
            $this->stockStateProvider = $objectManager->get(StockStateProvider::class);
            $this->stockRegistry = $objectManager->get(StockRegistryInterface::class);
        }

        $this->objectFactory = $objectFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @return bool
     */
    public function isMoveToCartAllowed()
    {
        return true;
    }

    /**
     * Check if product is disabled.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function checkProductDisabled(\Magento\Catalog\Model\Product $product)
    {
        $status = $product->getData('status');
        return $status == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED ? true : false;
    }

    /**
     * Check product for disabled or quantity.
     *
     * @param bool $qtyCheck
     * @param \Magento\Catalog\Model\Product $product
     * @param float $qty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function productCheck($qtyCheck, \Magento\Catalog\Model\Product $product, $qty)
    {
        $disabled = $this->checkProductDisabled($product);

        if ($disabled) {
            $this->disabledProductErrorMsgQuote($product);
        } elseif ($qtyCheck) {
            $this->checkQuantity($product, $qty);
        }
    }

    /**
     * @param \Magento\Framework\DataObject $config
     * @return float
     */
    public function getQtyFromConfig($config)
    {
        return isset($config['qty']) ? (float)$config['qty'] : 1.00;
    }

    /**
     * Prepare configurable product type.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $config
     * @param boolean $qtyCheck
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareConfigurableProduct(
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\DataObject $config,
        $qtyCheck
    ) {
        if (isset($config['super_attribute'])) {
            $childProduct = $product->getTypeInstance(true)
                ->getProductByAttributes(
                    $config['super_attribute'],
                    $product
                );
            $qty = $this->getQtyFromConfig($config);
            $this->productCheck($qtyCheck, $childProduct, $qty);
        }
    }

    /**
     * Prepare bundle product type.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $config
     * @param boolean $qtyCheck
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareBundleProduct(
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\DataObject $config,
        $qtyCheck
    ) {
        if (!$product->isAvailable()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Product is Out Of Stock'));
        }
        $qty = $this->getQtyFromConfig($config);

        foreach ($product->getExtensionAttributes()->getBundleProductOptions() as $option) {
            $optionId = $option->getOptionId();
            $productLinks = $option->getProductLinks();

            foreach ($productLinks as $link) {
                if (isset($config['bundle_option'][$optionId])) {
                    if ((is_array($config['bundle_option'][$optionId]) && in_array($link->getId(), $config['bundle_option'][$optionId]))
                        || $link->getId() == $config['bundle_option'][$optionId]
                    ) {
                        $childQty = (float)$config['bundle_option_qty'][$optionId];
                        $this->prepareSelectedBundleOption($link, $qty, $childQty, $qtyCheck);
                    }
                }
            }
        }
    }

    /**
     * Prepare selected bundle product type stock.
     *
     * @param \Magento\Bundle\Model\Link $product
     * @param float $qty
     * @param float $childQty
     * @param boolean $qtyCheck
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareSelectedBundleOption(\Magento\Bundle\Model\Link $link, $qty, $childQty, $qtyCheck)
    {
        $productId = $link->getEntityId();
        $catalogProduct = $this->productRepository->getById($productId);
        $qty = $qty * $childQty;

        $this->productCheck($qtyCheck, $catalogProduct, $qty);
    }

    /**
     * Prepare grouped product type.
     *
     * @param \Magento\Framework\DataObject $config
     * @param boolean $qtyCheck
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareGroupedProduct(\Magento\Framework\DataObject $config, $qtyCheck)
    {
        if (isset($config['super_group'])) {
            foreach ($config['super_group'] as $productId => $qty) {
                if ($qty > 0) {
                    $product = $this->productRepository->getById($productId);
                    $this->productCheck($qtyCheck, $product, $qty);
                }
            }
        }
    }

    /**
     * Prepare simple product type.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $config
     * @param boolean $qtyCheck
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareSimpleProduct(
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\DataObject $config,
        $qtyCheck
    ) {
        $qty = $this->getQtyFromConfig($config);
        $this->productCheck($qtyCheck, $product, $qty);
    }

    /**
     * Throw new LocalizedException error when product is disabled and trying to add to quote
     *
     * @param \Magento\Catalog\Model\Product $product
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function disabledProductErrorMsgQuote(\Magento\Catalog\Model\Product $product)
    {
        throw new \Magento\Framework\Exception\LocalizedException(__(
                'Product %1 with SKU %2 is disabled and has not been added to quote.',
                $product->getName(),
                $product->getSku()
            )
        );
    }

    /**
     * Check stock settings for quantity.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param float $qty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkQuantity(\Magento\Catalog\Model\Product $product, $qty)
    {
        $result = $this->objectFactory->create();
        $result->setHasError(false);
        $sku = $product->getSku();
        $backorderedAllowed = false;
        $websiteId = (int)$product->getStore()->getWebsiteId();

        if (!$this->getStockItemConfiguration) {
            return $this->deprecatedQuantityCheck($product, $qty);
        }

        $stockId = (int)$this->stockByWebsiteId->execute($websiteId)->getStockId();
        $isSalableResult = $this->isProductSalableForRequestedQty->execute($sku, $stockId, $qty);
        $stockItemConfiguration = $this->getStockItemConfiguration->execute($sku, $stockId);
        $productSalableResult = $this->backOrderNotifyCustomerCondition->execute($sku, $stockId, $qty);

        if ($isSalableResult->isSalable() === false) {
            foreach ($isSalableResult->getErrors() as $error) {
                $result->setHasError(true)->setMessage($error->getMessage())
                    ->setQuoteMessageIndex('qty');
            }
        }

        if ($stockItemConfiguration->getBackorders() === StockItemConfigurationInterface::BACKORDERS_YES_NONOTIFY) {
            $backorderedAllowed = true;
        }

        if ($productSalableResult->getErrors()) {
            /** @var ProductSalabilityError $error */
            foreach ($productSalableResult->getErrors() as $error) {
                $result->setMessage($error->getMessage());
                $backorderedAllowed = true;
            }
        }

        if (!$backorderedAllowed && $result->getHasError()) {
            throw new \Magento\Framework\Exception\LocalizedException(__($result->getMessage()));
        }
    }

    /**
     * Quantity check for Magento versions older than 2.3.0.
     * Can be removed when removing Magento 2.2 support.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param  float $qty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deprecatedQuantityCheck(\Magento\Catalog\Model\Product $product, $qty)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        $stockItem->setProductName($product->getName());
        $result = $this->stockStateProvider->checkQuoteItemQty(
            $stockItem,
            $qty,
            $qty,
            $qty
        );

        if ($result->getHasError()
            || $result->getMessage()
            && $stockItem->getBackorders() == \Magento\CatalogInventory\Model\Stock::BACKORDERS_NO
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(__($result->getMessage()));
        }
    }
}
