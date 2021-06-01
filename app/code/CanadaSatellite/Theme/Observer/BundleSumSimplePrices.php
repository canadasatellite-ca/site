<?php

namespace CanadaSatellite\Theme\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Event\ManagerInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Bundle\Model\ResourceModel\BundleFactory;
use Magento\Framework\Registry;

class BundleSumSimplePrices implements ObserverInterface
{
    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $_eventManager;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var BundleFactory
     */
    private $bundleFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    function __construct(
        ManagerInterface $eventManager,
        ProductFactory $productFactory,
        BundleFactory $bundleFactory,
        Registry $coreRegistry
    ){
        $this->_eventManager = $eventManager;
        $this->productFactory = $productFactory;
        $this->bundleFactory = $bundleFactory;
        $this->coreRegistry = $coreRegistry;
    }

    function execute(EventObserver $observer)
    {
        $product = $observer->getEvent()->getProduct();
        if ($product->getTypeId() == 'bundle') {
            $this->coreRegistry->unregister('product');
            $initialBundleSelectionsData = [];
            if ($product->getBundleSelectionsData()) {
                $initialBundleSelectionsData = $product->getBundleSelectionsData();
            } else {
                $newoptions = [];
                $mageWorxOptions = [];

                $bundleProduct = $this->productFactory->create();
                $bundleProduct->load($product->getEntityId());
                $bundleProductOptions = $this->bundleFactory->create();

                for ($i = 0; $i < count($bundleProduct->getOptions()); $i++) {
                    $mageWorxOptions[] = $bundleProduct->getOptions()[$i]->getValues();
                }
                $options = $bundleProductOptions->getSelectionsData($product->getEntityId());
                foreach ($options as $option) {
                    $option = array(0 => $option);
                    $newoptions[] = $option;
                }
                $product->setMageOptions($mageWorxOptions);
                $initialBundleSelectionsData = $newoptions;
                $product->setStoreId(0);
                $product->setFlagCliCommand(1);
            }

            $sumParentPrice = 0;
            foreach ($initialBundleSelectionsData as $key => $childProductData) {
                $childId = $childProductData[0]['product_id'];
                $childProduct = $this->productFactory->create();
                $childProduct->load($childId);
                $initialBundleSelectionsData[$key][0]['selection_price_value'] = $childProduct->getFinalPrice();
                $sumParentPrice += ($childProduct->getFinalPrice()*$childProductData[0]['selection_qty']);
            }
            $product->setPrice($sumParentPrice);
            $product->setBundleSelectionsData($initialBundleSelectionsData);
            if ($product->getMageOptions()) {
                for ($i = 0; $i < count($product->getOptions()); $i++) {
                    $product->getOptions()[$i]->setValues($product->getMageOptions()[$i]);
                }
            }
            $this->coreRegistry->register('product', $product);
        }
    }

}