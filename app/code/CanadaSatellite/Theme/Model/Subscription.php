<?php

namespace CanadaSatellite\Theme\Model;

use Magedelight\Subscribenow\Helper\Data;
use Magedelight\Subscribenow\Model\Service\SubscriptionService;
use Magento\Bundle\Model\Product\TypeFactory as BundleTypeFactory;
use Magento\Catalog\Model\ProductFactory as ProductModelFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magedelight\Subscribenow\Model\Subscription as ParentSubscription;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\CartRepositoryInterface;
use Magedelight\Subscribenow\Model\Source\PurchaseOption;

class Subscription extends ParentSubscription
{
    protected $productMarker;

    /**
     * Product collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;

    /**
     * Product collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;

    private $bundleTypeFactory;

    private $bundleType;

    private $childProduct = null;
    private $hasParent = false;
    private $parentProduct = null;

    private $registry;

    function __construct(
        Data $helper,
        Json $serialize,
        SubscriptionService $service,
        Http $request,
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        PriceHelper $priceHelper,
        ProductModelFactory $productModelFactory,
        BundleTypeFactory $bundleTypeFactory,
        Registry $registry,
        CollectionFactory $collectionFactory)
    {
        $this->collection = $collectionFactory;
        $this->bundleTypeFactory = $bundleTypeFactory;
        $this->registry = $registry;
        parent::__construct(
            $helper,
            $serialize,
            $service,
            $request,
            $checkoutSession,
            $quoteRepository,
            $priceHelper,
            $productModelFactory,
            $bundleTypeFactory,
            $registry);
    }

    private function getProductModel($parentId)
    {
        if ($this->productMarker != $parentId || empty($this->productMarker)) {
            $this->productCollection = $this->collection->create();
            $this->productCollection
                ->addAttributeToFilter('entity_id', $parentId)
                ->addAttributeToSelect('*')
                ->setPageSize(1)
                ->load();
            $this->productMarker = $parentId;
        }
        return $this->productCollection->getItemById($parentId);
    }

    function isSubscriptionProduct($product)
    {
        $parentId = $this->getBundleParentId($product);
        if ($parentId) {
            $this->childProduct = $product;
        }

        if ($product && $parentId) {
            $parentProduct = $this->getProductModel($parentId);

            if ($this->isSubscriptionProduct($parentProduct)) {
                $this->hasParent = true;
                $this->parentProduct = $parentProduct;

                if ($this->service->isFutureSubscription($parentProduct)
                    || ($parentProduct->getAllowTrial() && $parentProduct->getTrialAmount() > 0 && $this->isProfileInTrial())
                ) {
                    $product->setCustomPrice(0); // Set child product price to zero
                }
                return true;
            }
        }

        if ($product->hasSkipDiscount() && $product->getSkipDiscount()) {
            return false;
        }

        if ($product->hasSkipValidateTrial() && $product->getSkipValidateTrial()) {
            return true;
        }

        $isSubscription = $product->getIsSubscription();
        $subscriptionType = $product->getSubscriptionType();

        if ($isSubscription && $subscriptionType == PurchaseOption::SUBSCRIPTION) {
            return true;
        } elseif ($isSubscription && $this->isProductWithSubscriptionOption($product)) {
            return true;
        }

        return false;
    }

    private function getBundleParentId($product)
    {
        if (!$this->bundleType) {
            $this->bundleType = $this->bundleTypeFactory->create();
        }
        $ids = $this->bundleType->getParentIdsByChild($product->getId());
        return ($ids && isset($ids[0])) ? $ids[0] : null;
    }

    private function isProductWithSubscriptionOption($product)
    {
        $infoRequest = $product->getCustomOption('info_buyRequest');

        if ((!$infoRequest || !$infoRequest->getValue()) && $this->childProduct) {
            $infoRequest = $this->childProduct->getCustomOption('info_buyRequest');
        }

        if ($infoRequest) {
            $requestData = $this->serialize->unserialize($infoRequest->getValue());
            if ($this->service->checkProductRequest($requestData)) {
                return true;
            }
        }
        return false;
    }

    private function isProfileInTrial()
    {
        $profile = $this->getCurrentProfile();
        if (!$profile) {
            return true;
        }

        if ($profile->getIsTrial() && $profile->isTrialPeriod()) {
            return true;
        }
        return false;
    }

    private function getCurrentProfile()
    {
        return $this->registry->registry('current_profile');
    }
}
