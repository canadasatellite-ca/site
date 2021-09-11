<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

use CanadaSatellite\AstIntegration\LogicProcessors\OrderCustomOptionsHelper;
use CanadaSatellite\DynamicsIntegration\Utils\OrderItemProfitCalculator;
use CanadaSatellite\DynamicsIntegration\Utils\OrderProfitCalculator;
use CanadaSatellite\DynamicsIntegration\Utils\ProductProfitCalculator;

class OrderModelComposer {
    private $priceListHelper;
    private $currencyHelper;
    private $orderStatusHelper;
    private $productHelper;
    private $mapper;
    private $restApi;
    private $logger;
    private $orderRepository;


    function __construct(
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\PriceListHelper   $priceListHelper,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\CurrencyHelper    $currencyHelper,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\OrderStatusHelper $orderStatusHelper,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\ProductHelper     $productHelper,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\DynamicsMapper    $mapper,
        \CanadaSatellite\DynamicsIntegration\Rest\RestApi                  $restApi,
        \CanadaSatellite\DynamicsIntegration\Logger\Logger                 $logger,
        \Magento\Sales\Model\Order                                         $orderRepository
    ) {
        $this->priceListHelper = $priceListHelper;
        $this->currencyHelper = $currencyHelper;
        $this->orderStatusHelper = $orderStatusHelper;
        $this->productHelper = $productHelper;
        $this->mapper = $mapper;
        $this->restApi = $restApi;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @return array OrderModel.
     */
    function compose($order, $insert = false, $accountId = null, $customerId = null) {
        $this->logger->info("Composing order");

        $createdAt = $order->getCreatedAt();
        $shippedAt = $order->getShippedAt();
        $shippingAmount = $order->getShippingAmount();
        //$this->logger->info("Order create at: $createdAt");

        $orderId = $order->getIncrementId();
        $priceLevelId = $this->priceListHelper->getDefaultPriceListId();
        // Force base currency for Dynamics.
        $transactionCurrencyId = $this->currencyHelper->getCurrencyIdByCode('CAD');

        $data = array(
            'name' => $orderId,
            'pricelevelid@odata.bind' => "/pricelevels($priceLevelId)",
            'transactioncurrencyid@odata.bind' => "/transactioncurrencies($transactionCurrencyId)",
        );
        $status = $this->orderStatusHelper->getStatusId($order->getStatus());
        if ($status !== null) {
            $data['new_orderstatus'] = $status;
        }

        if (isset($accountId)) {
            $data['customerid_account@odata.bind'] = "/accounts($accountId)";
        }
        if (isset($customerId)) {
            $data['cs_accountnumber'] = $customerId;
        }
        if ($createdAt !== null) {
            $data['cs_orderdate'] = $createdAt;
        }
        if ($shippedAt !== null) {
            $data['new_shippingdate'] = $shippedAt;
        }
        if ($shippingAmount !== null) {
            $data['freightamount'] = $shippingAmount;
        }

        $billingData = $this->getBillingData($order);
        $shippingData = $this->getShippingData($order);

        $data = array_merge_recursive($data, $billingData, $shippingData);

        $this->logger->info("Insert: $insert");
        if ($insert === true) {
            $items = $this->getOrderItems($order);
            $data['order_details'] = $items;

            // TODO: Calculate order profit/margin.
            $this->logger->info("[OrderModelComposer::compose] Start calculating profit/margin for order");
            $calculator = new OrderProfitCalculator($this->logger, $order->getItems());

            $profit = $calculator->calculateProfit();
            $this->logger->info("[OrderModelComposer::compose] Order profit: $profit");

            $margin = $calculator->calculateMargin();
            $this->logger->info("[OrderModelComposer::compose] Order margin: $margin");

            $data['new_profit'] = $profit;
            $data['new_margin'] = $margin;
        }

        return $data;
    }

    private function getBillingData($order) {
        $billingAddress = $order->getBillingAddress();
        return $this->buildAddressData('billing', $billingAddress);
    }

    private function getShippingData($order) {
        $shippingAddress = $order->getShippingAddress();
        return $this->buildAddressData('shipping', $shippingAddress);
    }

    private function buildAddressData($addressType, $address) {
        if ($address === null) {
            return array();
        }

        if ($addressType == 'billing') {
            $prefix = 'billto_';
        } else if ($addressType == 'shipping') {
            $prefix = 'shipto_';
        }

        $data = array(
            $prefix . 'name' => $address->getFirstname() . ' ' . $address->getLastname(),
            $prefix . 'city' => $address->getCity(),
            $prefix . 'country' => $address->getCountry(),
            $prefix . 'postalcode' => $address->getPostcode(),
            $prefix . 'telephone' => $address->getPhone(),
        );
        $company = $address->getCompany();
        $streetLines = $address->getStreet();
        $region = $address->getRegion();
        $fax = $address->getFax();
        if (isset($company)) {
            $data[$prefix . 'contactname'] = $company;
        }
        if (isset($streetLines)) {
            if (count($streetLines) >= 1) {
                $data[$prefix . 'line1'] = $streetLines[0];
            }
            if (count($streetLines) >= 2) {
                $data[$prefix . 'line2'] = $streetLines[1];
            }
            if (count($streetLines) >= 3) {
                $data[$prefix . 'line3'] = $streetLines[2];
            }
        }
        if (isset($region)) {
            $data[$prefix . 'stateorprovince'] = $region;
        }
        if (isset($fax)) {
            $data[$prefix . 'fax'] = $fax;
        }

        return $data;
    }

    /**
     * @param \CanadaSatellite\DynamicsIntegration\Model\Order $order
     */
    private function getOrderItems($order) {
        $items = array();

        $this->logger->info("Entered getOrderItems");
        foreach ($order->getItems() as $item) {
            $sku = $item->getSku();
            $product = $item->getProduct();
            $this->logger->info("Getting product id for $sku");
            $crmProduct = $this->productHelper->createOrUpdate($product);
            $productId = $crmProduct->productid;
            $this->logger->info("Got product id $productId");

            // Set order item shipping price and current cost in CAD - this data available only in CRM.
            $shippingCost = $crmProduct->new_shippingcost_base;
            $this->logger->info("Product shipping cost in CAD: $shippingCost");
            $currentCost = $crmProduct->currentcost_base;
            $this->logger->info("Product current cost in CAD: $currentCost");
            $product = $item->getProduct();
            $product->setShippingCost($shippingCost);
            $product->setCurrentCost($currentCost);

            // Price in default currency - CAD.
            $price = $item->getPrice();
            $qty = $item->getQty();
            $tax = $item->getTax();
            $discount = $item->getDiscount();
            $total = $item->getTotal();

            $this->logger->info("Product $productId $price $qty $tax $discount $total");

            $detail = array(
                'productid@odata.bind' => "/products($productId)",
                'ispriceoverridden' => true,
                'uomid@odata.bind' => "/uoms(16b94c87-47ff-4583-884b-fba2cea56106)",
            );
            if ($price !== null) {
                $detail['priceperunit'] = $price;
            }
            if ($qty !== null) {
                $detail['quantity'] = $qty;
            }
            if ($tax !== null) {
                $detail['tax'] = $tax;
            }
            if ($discount !== null) {
                $detail['manualdiscountamount'] = $discount;
            }
            if ($total !== null) {
                $detail['extendedamount'] = $total;
            }

            // TODO: Calculate item profit/margin.
            $this->logger->info("[OrderModelComposer::getOrderItems] Start calculating profit/margin for item");
            $calculator = new OrderItemProfitCalculator($this->logger, $item);

            $profit = $calculator->calculateProfit();
            $this->logger->info("[OrderModelComposer::getOrderItems] Item profit: $profit");

            $margin = $calculator->calculateMargin();
            $this->logger->info("[OrderModelComposer::getOrderItems] Item margin: $margin");

            $detail['new_profit'] = $profit;
            $detail['new_margin'] = $margin;

            $items[] = $detail;
        }

        $this->logger->info("Exited getOrderItems");
        return $items;
    }

    /**
     * @param \CanadaSatellite\DynamicsIntegration\Model\Order $order
     * @return array[][] array[order item index][dynamic property guid] = property value
     */
    function getOrderDynamicProperties($order) {
        $result = [];
        $this->logger->info("[OrderModelComposer::getOrderDynamicProperties] Loading order id {$order->getIncrementId()} visible items");
        $magentoItems = $this->orderRepository->loadByIncrementId($order->getIncrementId())->getAllVisibleItems();
        $productCache = [];

        foreach ($magentoItems as $mItem) {
            /** @type \Magento\Sales\Model\Order\Item $mItem */

            $options = new OrderCustomOptionsHelper($mItem, false);
            if (empty($options->getAllOptions())) {
                array_push($result, null);
                continue;
            }

            $sku = $mItem->getProduct()->getSku();
            if (array_key_exists($sku, $productCache)) {
                $this->logger->info("[OrderModelComposer::getOrderDynamicProperties] Item $sku dynamic properties found in cache");
                $dynamicsProperties = $productCache[$sku];
            } else {
                $this->logger->info("[OrderModelComposer::getOrderDynamicProperties] Item $sku dynamic properties not found in cache");
                $productGuid = $this->restApi->findProductIdBySku($sku);
                $dynamicsProperties = [];
                foreach ($this->restApi->getProductDynamicProperties($productGuid) as $prop) {
                    $dynamicsProperties[$prop->description] = $prop;
                }
                $productCache[$sku] = $dynamicsProperties;
            }

            $itemResult = [];
            foreach ($options->getAllOptionIds() as $id => $label) {
                if (!array_key_exists("id:$id", $dynamicsProperties)) {
                    continue;
                }
                $dynProp = $dynamicsProperties["id:$id"];
                $value = $options->getOptionValue($label);
                if (empty($value)) {
                    continue;
                }
                $itemResult[$dynProp->dynamicpropertyid] = $value;
            }

            array_push($result, $itemResult);
        }

        $this->logger->info("[OrderModelComposer::getOrderDynamicProperties] Dynamic properties for order {$order->getIncrementId()}: " . json_encode($result));

        return $result;
    }
}