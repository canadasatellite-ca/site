<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

use CanadaSatellite\AstIntegration\LogicProcessors\AstQueueItem;
use CanadaSatellite\AstIntegration\LogicProcessors\OrderCustomOptionsHelper;
use CanadaSatellite\DynamicsIntegration\Utils\ProductProfitCalculator;
use Exception;
use Magento\Framework\Exception\NoSuchEntityException;

class DynamicsCrm {
    private $customerHelper;
    private $priceListHelper;
    private $productComposer;
    private $orderComposer;
    private $orderNoteComposer;
    private $simComposer;
    private $restApi;
    private $logger;
    private $productHelper;
    private $providerResolver;
    private $astManager;
    private $publisher;
    private $config;
    private $productRepository;


    function __construct(
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\CustomerHelper       $customerHelper,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\PriceListHelper      $priceListHelper,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\ProductModelComposer $productComposer,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\OrderModelComposer   $orderComposer,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\OrderNoteComposer    $orderNoteComposer,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\SimModelComposer     $simComposer,
        \CanadaSatellite\DynamicsIntegration\Rest\RestApi                     $restApi,
        \CanadaSatellite\DynamicsIntegration\Logger\Logger                    $logger,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\ProductHelper        $productHelper,
        \CanadaSatellite\DynamicsIntegration\Utils\SatelliteProviderResolver  $providerResolver,
        \CanadaSatellite\AstIntegration\AstManagement\AstManager              $astManager,
        \CanadaSatellite\SimpleAmqp\Publisher                                 $publisher,
        \CanadaSatellite\DynamicsIntegration\Config\Config                    $config,
        \Magento\Catalog\Model\ProductRepository                              $productRepository
    ) {
        $this->customerHelper = $customerHelper;
        $this->priceListHelper = $priceListHelper;
        $this->productComposer = $productComposer;
        $this->orderComposer = $orderComposer;
        $this->orderNoteComposer = $orderNoteComposer;
        $this->simComposer = $simComposer;
        $this->restApi = $restApi;
        $this->logger = $logger;
        $this->productHelper = $productHelper;
        $this->providerResolver = $providerResolver;
        $this->astManager = $astManager;
        $this->publisher = $publisher;
        $this->config = $config;
        $this->productRepository = $productRepository;
    }

    /**
     * @return string Dynamics product id.
     */
    function createOrUpdateProduct($sku, $product) {
        $crmProduct = $this->productComposer->compose($product);

        $productId = $this->restApi->findProductIdBySku($sku);
        if ($productId === false) {
            $productId = $this->restApi->createProduct($crmProduct);
            $productIsNew = true;
        } else {
            //$this->logger->info("Find product: $productId");
            $this->restApi->updateProduct($productId, $crmProduct);
            $productIsNew = false;
        }

        // TODO: Create/update product price in price list(s).
        $crmPriceListItem = $this->productComposer->composePriceListItem($product, $productId);
        $priceListId = $this->priceListHelper->getDefaultPriceListId();
        $productPriceLevelId = $this->restApi->findProductPriceLevelIdByProductId($productId, $priceListId);
        if ($productPriceLevelId === false) {
            $this->logger->info("Creating product price level");
            $productPriceLevelId = $this->restApi->createProductPriceLevel($productId, $crmPriceListItem);
        } else {
            $this->logger->info("Found product price level with id $productPriceLevelId");
            $this->restApi->updateProductPriceLevel($productPriceLevelId, $crmPriceListItem);
        }

        $this->logger->info("Product price list level created with id $productPriceLevelId");


        // TODO: Update profit/margins.
        $this->logger->info("[DynamicsCrm::createOrUpdateProduct] Start calculating profit/margin for product");
        $crmProduct = $this->restApi->getProductById($productId);

        $calculator = new ProductProfitCalculator($this->logger, $product, $crmProduct->new_shippingcost, $crmProduct->currentcost, $crmProduct->new_saleprice);

        $currencyExchange = $calculator->calculateCurrencyExchange();
        $this->logger->info("[DynamicsCrm::createOrUpdateProduct] Currency exchange for product: $currencyExchange");

        $processingFees = $calculator->calculateProcessingFees();
        $this->logger->info("[DynamicsCrm::createOrUpdateProduct] Processing fees for product: $processingFees");

        $standardCost = $calculator->calculateStandardCost();
        $this->logger->info("[DynamicsCrm::createOrUpdateProduct] Standard cost for product: $standardCost");

        $profit = $calculator->calculateProfit();
        $this->logger->info("[DynamicsCrm::createOrUpdateProduct] Profit for product: $profit");
        $margin = $calculator->calculateMargin();
        $this->logger->info("[DynamicsCrm::createOrUpdateProduct] Margin for product: $margin");

        $profitData = array(
            'new_currencyexchange' => $currencyExchange,
            'new_processingfees' => $processingFees,
            'standardcost' => $standardCost,
            'new_profit' => $profit,
            'new_margin' => $margin,

            // Force product revise for dynamics properties update
            'statecode' => 3,
            'statuscode' => -1
        );

        $this->logger->info("[DynamicsCrm::createOrUpdateProduct] Trying to update product profit/margin...");
        $this->restApi->updateProduct($productId, $profitData);
        $this->logger->info("[DynamicsCrm::createOrUpdateProduct] Product profit/margin updated");

        // Sync product dynamic properties
        $this->productHelper->syncProductDynamicProperties($productIsNew, $productId, $product->getSku());
        // Force product published status
        $this->restApi->updateProduct($productId, ['statecode' => 0, 'statuscode' => -1]);

        return $productId;
    }


    function deleteProduct($sku) {
        $productId = $this->restApi->findProductIdBySku($sku);

        if ($productId === false) {
            return;
        }

        $this->restApi->deleteProduct($productId);
    }

    /**
     * @param \CanadaSatellite\DynamicsIntegration\Model\Order $order
     * @return string GUID in Dynamics
     * @throws Exception
     */
    function createOrUpdateOrder($order) {
        $this->logger->info("[createOrUpdateOrder] -> Enter");

        $orderId = $order->getIncrementId();
        $crmId = $this->restApi->findOrderByNumber($orderId);
        if ($crmId === false) {
            $this->logger->info("[createOrUpdateOrder] Order not found in CRM. Creating...");
            $customer = $order->getCustomer();
            if ($customer === null) {
                $this->logger->info("[createOrUpdateOrder] Order $orderId has no customer id. Stop processing.");
                throw new Exception("Order $orderId has no customer id.");
            }

            $customerId = $order->getCustomerId();

            $this->logger->info("[createOrUpdateOrder] Get or create customer account in CRM...");
            $accountId = $this->customerHelper->createOrUpdateCustomer($customer);
            $this->logger->info("[createOrUpdateOrder] Customer account: $accountId.");

            $crmOrder = $this->orderComposer->compose($order, true, $accountId, $customerId);

            $crmId = $this->restApi->createOrder($crmOrder);

            $orderDynamicProps = $this->orderComposer->getOrderDynamicProperties($order);
            $this->restApi->updateDynamicPropertiesInOrder($crmId, $orderDynamicProps);

            return $crmId;
        }

        $this->logger->info("[createOrUpdateOrder] Updating order $crmId");
        $crmOrder = $this->orderComposer->compose($order);
        $this->restApi->updateOrder($crmId, $crmOrder);

        $orderDynamicProps = $this->orderComposer->getOrderDynamicProperties($order);
        $this->restApi->updateDynamicPropertiesInOrder($crmId, $orderDynamicProps);

        return $crmId;
    }

    function getOrder($orderId) {
        $this->logger->info("[getOrder] Enter");

        $crmOrderId = $this->restApi->findOrderByNumber($orderId);
        $crmOrder = $this->restApi->getOrderById($crmOrderId);
        if ($crmOrder === false) {
            $this->logger->info("[getOrder] Order ($orderId) not found");
            return null;
        }

        $this->logger->info("[getOrder] Order received");
        return $crmOrder;
    }

    /**
     * @param string $orderId
     * @param string $note
     * @throws Exception
     */
    function createOrderNote($orderId, $note) {
        $this->logger->info("[createOrderNote] Enter");

        $crmId = $this->restApi->findOrderByNumber($orderId);
        if ($crmId === false) {
            $this->logger->info("[createOrderNote] Order $orderId not found. Creating...");
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $orderRepository = $objectManager->get('Magento\Sales\Model\Order');
            $envelopeFactory = $objectManager->get('CanadaSatellite\DynamicsIntegration\Envelope\OrderEnvelopeFactory');
            $orderFactory = $objectManager->get('CanadaSatellite\DynamicsIntegration\Model\OrderFactory');
            $envelope = $envelopeFactory->create($orderRepository->loadByIncrementId($orderId));
            $order = $orderFactory->fromEnvelope(json_decode(json_encode($envelope)));
            $crmId = $this->createOrUpdateOrder($order);
            $this->logger->info("[createOrderNote] Order created. Guid: $crmId");
        }

        $this->logger->info("[createOrderNote] Creating note for order $crmId");
        $crmNote = $this->orderNoteComposer->compose($note);
        return $this->restApi->createOrderNote($crmId, $crmNote);
    }

    /**
     * @param \CanadaSatellite\DynamicsIntegration\Model\ActivationForm $request
     * @return mixed ActivationRequest entity id in Dynamics
     * @throws Exception
     */
    function createOrUpdateActivationRequest($request) {
        $this->logger->info("[createOrUpdateActivationRequest] Enter");

        $requestId = $request->getId();
        $email = $request->getEmail();
        $firstName = $request->getFirstName();
        $lastName = $request->getLastName();
        $companyName = $request->getCompanyName();
        $orderNumber = $request->getOrderNumber();
        $simNumber = $request->getSimNumber();
        $order = $request->getOrder();
        $customer = $request->getCustomer();
        $notes = $request->getNotes();
        $status = $request->getStatus();

        $this->logger->info("[createOrUpdateActivationRequest] Request id: $requestId Email: $email FirstName: $firstName LastName: $lastName CompanyName: $companyName OrderNumber: $orderNumber SimNumber: $simNumber Notes: $notes Status: $status");

        $additionalInfo = '';

        $crmSim = $this->restApi->getSimByNumber($simNumber);
        if ($crmSim !== false) {
            $crmSimId = $crmSim->cs_simid;
            $this->logger->info("[createOrUpdateActivationRequest] SIM $simNumber id in Dynamics: $crmSimId");
        } else {
            $crmSimId = null;
            $this->logger->info("[createOrUpdateActivationRequest] SIM $simNumber is not found in Dynamics");
            $additionalInfo = "SIM: $simNumber";
        }

        if ($order !== null) {
            $crmOrderId = $this->createOrUpdateOrder($order);
            $this->logger->info("[createOrUpdateActivationRequest] Order $orderNumber id in Dynamics: $crmOrderId");
        } else {
            $crmOrderId = null;
            $this->logger->info("[createOrUpdateActivationRequest] Order $orderNumber is not found in Dynamics");

            if ($additionalInfo !== '') {
                $additionalInfo .= " | ";
            }
            if ($orderNumber !== '' && $orderNumber !== null) {
                $additionalInfo .= "Order: $orderNumber";
            }
        }

        if ($customer !== null) {
            $crmAccountId = $this->customerHelper->createOrUpdateCustomer($customer);
        } else {
            $crmAccountId = null;
        }

        $crmModel = array(
            'cs_requestnumber' => $requestId,
            'cs_status' => $status == 2 ? 100000001 : 100000000,
            'cs_emailaddress' => $email,
            'cs_firstname' => $firstName,
            'cs_lastname' => $lastName,
            'cs_companyname' => $companyName,
            'cs_notes' => $notes,
            'cs_additionalinfo' => $additionalInfo,
        );

        if ($crmSimId !== null) {
            $crmModel['cs_sim@odata.bind'] = "/cs_sims($crmSimId)";
        }
        if ($crmOrderId !== null) {
            $crmModel['cs_order@odata.bind'] = "/salesorders($crmOrderId)";
        }
        if ($crmAccountId !== null) {
            $crmModel['cs_account@odata.bind'] = "/accounts($crmAccountId)";
        }

        $desiredActivationDate = $request->getDesiredActivationDate();
        $completedDate = $request->getCompletedDate();
        $phoneNumber = $request->getPhoneNumber();
        $dataNumber = $request->getDataNumber();
        $expirationDate = $request->getExpirationDate();
        $comments = $request->getComments();
        $this->logger->info("[createOrUpdateActivationRequest] Desired activation date $desiredActivationDate Completed date $completedDate Phone number $phoneNumber Data number $dataNumber Expiration date $expirationDate Comments $comments");

        $crmModel['cs_desiredactivationdate'] = $desiredActivationDate;
        $crmModel['cs_completeddate'] = $completedDate;
        $crmModel['cs_phonenumber'] = $phoneNumber;
        $crmModel['cs_datanumber'] = $dataNumber;
        $crmModel['cs_expirationdate'] = $expirationDate;
        $crmModel['cs_comments'] = $comments;

        $crmId = $this->restApi->createOrUpdateActivationRequest($crmModel);
        $this->logger->info("Activation request $requestId created or updated in Dynamics with id $crmId");

        if ($crmSimId !== null) {
            $this->logger->info("[createOrUpdateActivationRequest] Activate SIM $crmSimId in Dynamics CRM for activation request $requestId...");
            $this->activateSim($crmSim, $phoneNumber, $dataNumber, $completedDate, $expirationDate);
            $this->logger->info("[createOrUpdateActivationRequest] SIM $crmSimId is activated in Dynamics CRM for activation request $requestId...");
        } else {
            $this->logger->info("[createOrUpdateActivationRequest] SIM $crmSimId in Dynamics CRM for activation request $requestId is not found. Skipping activation...");
        }

        // AST Activation Processing BEGIN

        if ($crmSim === false || is_null($crmSim->new_plan_key)) {
            $this->logger->info("[createOrUpdateActivationRequest] CRM SIM PlanKey is null. AST request will not be processed. SIM = $simNumber");
            return $crmId;
        }

        $astPlansHelper = new \CanadaSatellite\AstIntegration\LogicProcessors\AstPlansHelper();
        $reference = $orderNumber . ' - ' . $lastName;

        if ($this->providerResolver->isSimIridium($simNumber)) {
            $planData = $astPlansHelper->get('iridium', $crmSim->new_plan_key);

            $result = $this->astManager->iridiumActivateSim($simNumber,
                $reference,
                $planData['ServiceTypeId'],
                $planData['PlanId'],
                $planData['PlanOptions'],
                $planData['BillProfileId']);
        } else if ($this->providerResolver->isSimInmarsat($simNumber)) {
            $astPlansHelper = new \CanadaSatellite\AstIntegration\LogicProcessors\AstPlansHelper();
            $planData = $astPlansHelper->get('inmarsat', $crmSim->new_plan_key);

            $result = $this->astManager->inmarsatActivateSim($simNumber,
                $reference,
                $planData['ServiceTypeId'],
                $planData['Category'],
                $planData['PackageId'],
                $planData['RatePlanId'],
                $planData['BillProfileId']);
        } else {
            $result = null;
        }

        if (!is_null($result) && isset($planData)) {
            $itemVoucher = null;
            if (array_key_exists('Voucher', $planData)) {
                $itemVoucher = $planData['Voucher'];
            }

            switch ($result->Status) {
                case 'Failed':
                    $this->logger->info("[createOrUpdateActivationRequest] AST API returned Failed status. SIM = $simNumber. Request id = $requestId");
                    break;
                case 'Succeeded':
                    $queueItem = new AstQueueItem($result->DataId, $simNumber, $itemVoucher);
                    $queueItem->finalize($result->MSISDN, $this->astManager, $this->restApi);
                    break;
                case 'Queued':
                case 'Waiting':
                    $queueItem = new AstQueueItem($result->DataId, $simNumber, $itemVoucher);
                    $queueItem->nextTime = time() + 60;

                    $this->publisher->publish($this->config->getAstQueue(), $queueItem);
                    break;
            }
        } else {
            $this->logger->info("[createOrUpdateActivationRequest] AST API error. SIM = $simNumber. Request id = $requestId");
        }

        // AST Activation Processing END

        return $crmId;
    }

    function createSim($sim) {
        $this->logger->info("Start compose SIM model from #" . $sim->getSimNumber());
        $crmSim = $this->simComposer->compose($sim);
        $this->logger->info("Send request to CRM with new SIM - " . json_encode($crmSim));
        $simId = $this->restApi->createSim($crmSim);
        $this->logger->info("SIM created on CRM. SimId = $simId");
        return $simId;
    }

    private function activateSim($crmSim, $satelliteNumber, $dataNumber, $activationDate, $expirationDate) {
        $crmSimId = $crmSim->cs_simid;
        $crmSimNumber = $crmSim->cs_number;
        $oldExpirationDateUtc = $crmSim->cs_expirydate;

        $crmModel = array();
        if ($expirationDate !== null) {
            $newExpirationDateUtc = (new \DateTime($expirationDate))->format('Y-m-d\TH:i:s\Z');
            $crmModel['cs_expirydate'] = $newExpirationDateUtc;
        }
        if ($satelliteNumber !== null) {
            $crmModel['cs_satellitenumber'] = $satelliteNumber;
        }
        if ($dataNumber !== null) {
            $crmModel['cs_data'] = $dataNumber;
        }
        if ($activationDate !== null) {
            $crmModel['cs_activationdate'] = $activationDate;
        }

        if (!empty($crmModel)) {
            $this->restApi->updateSim($crmSimId, $crmModel);

            if (isset($newExpirationDateUtc) && $oldExpirationDateUtc !== $newExpirationDateUtc) {
                $this->logger->info("[ActivateSim] Expiration date for SIM $crmSimId ($crmSimNumber) is changed from $oldExpirationDateUtc to $newExpirationDateUtc");
            }
        }
    }

    /**
     * @param string $satelliteNumber
     * @return string|false
     */
    function getSimNumberBySatelliteNumber($satelliteNumber) {
        $this->logger->info("Resolving sim number by satellite number = $satelliteNumber");
        $crmSim = $this->restApi->getSimBySatelliteNumber($satelliteNumber);
        if ($crmSim) {
            $this->logger->info("Sim number resolved. Satellite number = $satelliteNumber. Sim number = {$crmSim->cs_simid}");
            return $crmSim->cs_simid;
        }
        $this->logger->info("Sim number resolve error. Satellite number = $satelliteNumber");
        return false;
    }
}
