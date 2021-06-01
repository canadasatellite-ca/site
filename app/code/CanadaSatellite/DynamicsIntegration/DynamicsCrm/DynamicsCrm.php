<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

use CanadaSatellite\DynamicsIntegration\Utils\ProductProfitCalculator;
use Exception;

class DynamicsCrm {
	private $customerHelper;
	private $priceListHelper;
	private $productComposer;
	private $orderComposer;
	private $orderNoteComposer;
	private $simComposer;
	private $restApi;
	private $logger;

	function __construct(
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\CustomerHelper $customerHelper,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\PriceListHelper $priceListHelper,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\ProductModelComposer $productComposer,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\OrderModelComposer $orderComposer,
        \CanadaSatellite\DynamicsIntegration\DynamicsCrm\OrderNoteComposer $orderNoteComposer,
		\CanadaSatellite\DynamicsIntegration\DynamicsCrm\SimModelComposer $simComposer,
		\CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi,
		\CanadaSatellite\DynamicsIntegration\Logger\Logger $logger
	) {
		$this->customerHelper = $customerHelper;
		$this->priceListHelper = $priceListHelper;
		$this->productComposer = $productComposer;
		$this->orderComposer = $orderComposer;
		$this->orderNoteComposer = $orderNoteComposer;
		$this->simComposer = $simComposer;
		$this->restApi = $restApi;
		$this->logger = $logger;
	}

	/**
	* @return string Dynamics product id.
	*/ 
	function createOrUpdateProduct($sku, $product) {
		$crmProduct = $this->productComposer->compose($product);

		$productId = $this->restApi->findProductIdBySku($sku);	
		if ($productId === false) {
			$productId = $this->restApi->createProduct($crmProduct);
		}
		else {
			//$this->logger->info("Find product: $productId");
			$this->restApi->updateProduct($productId, $crmProduct);
		}

		// TODO: Create/update product price in price list(s).
		$crmPriceListItem = $this->productComposer->composePriceListItem($product, $productId);
		$priceListId = $this->priceListHelper->getDefaultPriceListId();
		$productPriceLevelId = $this->restApi->findProductPriceLevelIdByProductId($productId, $priceListId);
		if ($productPriceLevelId === false) {
			$this->logger->info("Creating product price level");
			$productPriceLevelId = $this->restApi->createProductPriceLevel($productId, $crmPriceListItem);
		}
		else {
			$this->logger->info("Found product price level with id $productPriceLevelId");
			$this->restApi->updateProductPriceLevel($productPriceLevelId, $crmPriceListItem);
		}
		
		$this->logger->info("Product price list level created with id $productPriceLevelId");

		// TODO: Update profit/margins.
		$this->logger->info("[DynamicsCrm::createOrUpdateProduct] Start calculating profit/margin for product");
		$crmProduct = $this->restApi->getProductById($productId);

		//$this->logger->info("Product from CRM:" . var_export($crmProduct, true));
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
		);

		$this->logger->info("[DynamicsCrm::createOrUpdateProduct] Trying to update product profit/margin...");
		$this->restApi->updateProduct($productId, $profitData);
		$this->logger->info("[DynamicsCrm::createOrUpdateProduct] Product profit/margin updated");

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
	* @return Order id.
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
			return $this->restApi->createOrder($crmOrder);
		}

		$this->logger->info("[createOrUpdateOrder] Updating order $crmId");
		$crmOrder = $this->orderComposer->compose($order);
		$this->restApi->updateOrder($crmId, $crmOrder);
		return $crmId;
	}

	function getOrder($orderId) {
		$this->logger->info("[getOrder] Enter");
		
		$crmOrderId = $this->restApi->findOrderByNumber($orderId);
		$crmOrder = $this->restApi->getOrderById($crmOrderId);
		if ($crmOrder === false) {
			$this->logger->info("[getOrder] Order ($orderId) not found");
			return;
		}

		$this->logger->info("[getOrder] Order received");
		return $crmOrder;
	}

	/**
	* @param $orderId string
	* @param $note string
	* @throws Exception
	*/
	function createOrderNote($orderId, $note)
	{
		$this->logger->info("[createOrderNote] Enter");

		$crmId = $this->restApi->findOrderByNumber($orderId);
		if ($crmId === false) {
			throw new Exception("Order $orderId not found");
		}

		$this->logger->info("[createOrderNote] Creating note for order $crmId");
		$crmNote = $this->orderNoteComposer->compose($note);
		$this->restApi->createOrderNote($crmId, $crmNote);
	}

	/**
	 * @param CanadaSatellite\DynamicsIntegration\Model\ActivationForm 
	 * @return ActivationRequest entity id in Dynamics
	 */
	function createOrUpdateActivationRequest($request)
	{
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

		return $crmId;
	}

	function createSim($sim) {
		$this->logger->info("Start compose SIM model from #".$sim->getSimNumber());
		$crmSim = $this->simComposer->compose($sim);
		$this->logger->info("Send request to CRM with new SIM - ".json_encode($crmSim));
		$simId = $this->restApi->createSim($crmSim);
		$this->logger->info("SIM created on CRM. SimId = $simId");
		return $simId;
	}

	private function activateSim($crmSim, $satelliteNumber, $dataNumber, $activationDate, $expirationDate)
	{
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

		$this->restApi->updateSim($crmSimId, $crmModel);

		if ($oldExpirationDateUtc !== $newExpirationDateUtc) {
			$this->logger->info("[ActivateSim] Expiration date for SIM $crmSimId ($crmSimNumber) is changed from $oldExpirationDateUtc to $newExpirationDateUtc");
		}
	}
}