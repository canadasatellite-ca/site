<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */

/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */

namespace CanadaSatellite\AstIntegration\LogicProcessors;

use CanadaSatellite\AstIntegration\AstManagement\Models\IridiumTopupModel;

class TopUpOrderProcessor {
    private $astApi;
    private $dynamicsApi;
    private $logger;
    private $orderRepo;
    private $config;
    private $emailSender;

    public function __construct(
        \CanadaSatellite\AstIntegration\Rest\RestApi                     $astApi,
        \CanadaSatellite\DynamicsIntegration\Rest\RestApi                $dynamicsApi,
        \CanadaSatellite\AstIntegration\Logger\Logger                    $logger,
        \Magento\Sales\Model\Order                                       $orderRepo,
        \CanadaSatellite\DynamicsIntegration\Config\ConfigValuesProvider $config,
        \CanadaSatellite\AstIntegration\LogicProcessors\EmailSender      $emailSender
    ) {
        $this->astApi = $astApi;
        $this->dynamicsApi = $dynamicsApi;
        $this->logger = $logger;
        $this->orderRepo = $orderRepo;
        $this->config = $config;
        $this->emailSender = $emailSender;
    }

    public function processTopUpOrder($orderId) {
        $this->logger->info("[processTopUpOrder] Begin. Order Id: $orderId");

        $order = $this->orderRepo->loadByIncrementId($orderId);

        if ($order->getBaseTotalDue() != 0) {
            $this->logger->info("[processTopUpOrder] End. Order not paid. Order Id: $orderId. Total due: " . $order->getBaseTotalDue());
            return;
        }

        $dynOrder = $this->dynamicsApi->getOrderByIncrementId($orderId);
        if ($dynOrder === false) {
            $this->logger->info("[processTopUpOrder] End. Dynamics order not found. Order Id: $orderId");
            return;
        }

        if ($dynOrder->new_ast_integr_processed === 1) {
            $this->logger->info("[processTopUpOrder] End. Order already processed");
            return;
        }

        $this->logger->info("[processTopUpOrder] Updating Dynamics order...");
        $this->dynamicsApi->updateOrder($dynOrder->salesorderid, ['new_ast_integr_processed' => 1]);
        $this->logger->info("[processTopUpOrder] Dynamics order updated");

        $voucherCache = [];

        foreach ($order->getAllVisibleItems() as $item) {
            /** @type \Magento\Sales\Model\Order\Item $item */

            $this->logger->info("[processTopUpOrder] Processing order item {$item->getSku()}");

            $options = new OrderCustomOptionsHelper($item);
            $simNumber = $options->getFirstExistOptionValue(...$this->config->getTopupPhoneNumber());
            if (empty($simNumber)) continue;
            $targetSku = $options->getFirstExistOptionValue(...$this->config->getTopupTargetSku());
            if (empty($targetSku)) continue;

            $this->logger->info("[processTopUpOrder] Sim number: $simNumber. Target sku: $targetSku");

            // ===== Get voucher properties from Dynamics =====
            if (array_key_exists($targetSku, $voucherCache)) {
                $voucher = $voucherCache[$targetSku];
            } else {
                $voucher = $this->dynamicsApi->getAstVoucherBySku($targetSku);
                $voucherCache[$targetSku] = $voucher;
            }

            if (!$voucher) {
                $this->logger->info("[processTopUpOrder] Skip. Voucher def not found");
                continue;
            }

            $this->logger->info("[processTopUpOrder] Voucher def found. Guid: $voucher->new_ast_voucherid");

            // ===== Get SIM from Dynamics =====
            $sim = $this->dynamicsApi->getSimByNumberOrSatelliteNumber($simNumber);

            if (!$sim) {
                $this->logger->info("[processTopUpOrder] Skip. Sim not found");
                $this->sendEmail($simNumber, $order, "SIM entity not found in Dynamics");
                continue;
            }

            $this->logger->info("[processTopUpOrder] Sim found. Guid: $sim->cs_simid");

            // ===== Check SIM Sub Status =====
            if ($sim->new_substatus !== null) {
                // Trying to get formatted Sub Status value
                $key = 'new_substatus@OData.Community.Display.V1.FormattedValue';
                $subStatus = property_exists($sim, $key) ? $sim->$key : $sim->new_substatus;

                $this->logger->info("[processTopUpOrder] Skip. Sub Status is not null. Sub Status: $subStatus");

                // Do not send an EMail if SIM in auto recharge mode or already paid
                // Auto Recharge, Auto Recharge - PAID
                $ignoredStatuses = [100000000, 100000014];
                if (!in_array($ignoredStatuses, $sim->new_substatus)) {
                    $this->sendEmail($sim, $order, "Sub Status is not empty. Sub Status: $subStatus");
                }

                continue;
            }

            // ===== Check order quantity =====
            if ($item->getQtyOrdered() != 1) {
                $this->logger->err("[processTopUpOrder] Skip. Quantity != 1. Quantity: {$item->getQtyOrdered()}");
                $this->sendEmail($sim, $order, "Quantity of order product is not equals 1");
                continue;
            }

            // ===== Check SIM fields =====
            if ($sim->new_airtimevendor !== 100000000) { // Airtime Vendor == AST
                $this->logger->info("[processTopUpOrder] Skip. Airtime Vendor is not AST. Airtime Vendor: $sim->new_airtimevendor");
                $this->sendEmail($sim, $order, "Airtime Vendor is not AST");
                continue;
            }

            if ($sim->cs_simstatus !== 100000001 && $sim->cs_simstatus !== 100000002) { // Network Status == {Active, Expired}
                $this->logger->info("[processTopUpOrder] Skip. Network Status is not Active or Expired. Network Status: $sim->cs_simstatus");
                $this->sendEmail($sim, $order, "Network Status is not Active or Expired");
                continue;
            }

            if (is_null($sim->cs_currentminutes)) {
                $this->logger->info("[processTopUpOrder] Skip. Current minutes is null");
                $this->sendEmail($sim, $order, "Current minutes is not set");
                continue;
            }

            // ===== Process =====
            if ($sim->cs_currentminutes < 51) {
                $this->logger->info("[processTopUpOrder] Activating TopUp now. Current minutes: $sim->cs_currentminutes");

                if ($voucher->new_parts_count === 2 && is_null($voucher->new_expire_days)) {
                    $this->logger->info("[processTopUpOrder] Skip. Voucher expire days is null");
                    $this->sendEmail($sim, $order, "Voucher expire days is null. Please fix voucher definition. Sku: $targetSku");
                    continue;
                }

                $success = $this->processTopUp($sim, $voucher, $order);
                if (!$success) {
                    $this->logger->err("[processTopUpOrder] Skip. ProcessTopUp failed");
                    continue;
                }

                if ($voucher->new_parts_count === 2) {
                    $this->logger->info("[processTopUpOrder] Updating sim...");

                    $payload = [
                        'new_substatus' => 100000001, // Sub Status = 1/2
                        'new_ast_voucher@odata.bind' => "/new_ast_vouchers($voucher->new_ast_voucherid)",
                        'new_quicknote' => "#{$order->getIncrementId()}",
                        'cs_expirydate' => $this->getNewExpiryDate($voucher->new_expire_days)
                    ];
                    $this->logger->info("[processTopUpOrder] SIM update payload: " . json_encode($payload));

                    $this->dynamicsApi->updateSim($sim->cs_simid, $payload);
                    $this->logger->info("[processTopUpOrder] Sim updated");
                } else {
                    $this->logger->info("[processTopUpOrder] SIM update is not required");
                }

                $this->logger->info("[processTopUpOrder] Creating provisioning...");
                $provGuid = $this->createProvisioning($sim, $orderId, $targetSku);
                $this->logger->info("[processTopUpOrder] Provisioning created. Guid: $provGuid");
            } else {
                /* ===== revert to System Generated Order Process ====== */
                $this->logger->info("[processTopUpOrder] Reverting to SysGenOrderProc. Current minutes: $sim->cs_currentminutes");

                $this->logger->info("[processTopUpOrder] Updating sim...");

                $payload = [
                    'new_substatus' => $voucher->new_parts_count === 2
                        ? 100000017 // Sub Status = 0/2
                        : 100000004, // Sub Status = PAID
                    'new_ast_voucher@odata.bind' => "/new_ast_vouchers($voucher->new_ast_voucherid)",
                    'new_quicknote' => "#{$order->getIncrementId()}"
                ];
                $this->logger->info("[processTopUpOrder] SIM update payload: " . json_encode($payload));

                $this->dynamicsApi->updateSim($sim->cs_simid, $payload);
                $this->logger->info("[processTopUpOrder] Sim updated");
            }
        }

        $this->logger->info("[processTopUpOrder] End");
    }

    private function processTopUp($sim, $voucher, $order): bool {
        $this->logger->info("[processTopUp] Begin");
        $reference = $this->getReference($order);
        $this->logger->info("[processTopUp] Reference: $reference");

        $payloads = [
            // Main
            new IridiumTopupModel($sim->cs_number, $voucher->new_voucher_1_service_type_id,
                $reference, $voucher->new_voucher_1_id, $voucher->new_voucher_1_quantity,
                $voucher->new_voucher_1_validity)
        ];
        if (!is_null($voucher->new_voucher_2_service_type_id)) {
            if ($voucher->new_voucher_2_validity === 1) {
                $voucherCount = !is_null($voucher->new_voucher_2_quantity) && $voucher->new_voucher_2_quantity > 1
                    ? $voucher->new_voucher_2_quantity : 1;
                $voucherQuantity = 0;
            } else {
                $voucherCount = 1;
                $voucherQuantity = $voucher->new_voucher_2_quantity;
            }

            for ($i = 0; $i < $voucherCount; $i++) {
                array_push($payloads,
                    new IridiumTopupModel($sim->cs_number, $voucher->new_voucher_2_service_type_id,
                        $reference, $voucher->new_voucher_2_id, $voucherQuantity,
                        $voucher->new_voucher_2_validity));
            }
        }

        $this->logger->info("[processTopUp] Vouchers to TopUp: " . count($payloads));

        foreach ($payloads as $payload) {
            unset($response);

            for ($i = 0; $i < 3; $i++) {
                try {
                    $this->logger->info("[processTopUp] Requesting TopUp. Payload: " . json_encode($payload));
                    $response = $this->astApi->iridiumTopUp($payload);
                } catch (\Exception $e) {
                    $this->logger->err("[processTopUp] TopUp request error. Try: $i. Message: {$e->getMessage()}");
                    sleep(1);
                    continue;
                }
                break;
            }

            if (!isset($response)) {
                $this->logger->err("[processTopUp] TopUp timed out");
                $this->sendEmail($sim, $order, "AST API error (3/3 tries)");
                return false;
            }

            if ($response->Error_Code !== 0) {
                $this->logger->err("[processTopUp] TopUp API error. Code: $response->Error_Code. Message: $response->Message");
                $this->sendEmail($sim, $order, "AST API message: $response->Message");
                return false;
            }

            $this->logger->info("[processTopUp] AST API response: " . var_export($response, true));
            $this->logger->info("[processTopUp] TopUp OK. Voucher: {$payload->getVoucher()}");
        }

        $this->logger->info("[processTopUp] End");
        return true;
    }

    private function createProvisioning($sim, $orderId, $targetSku) {
        $dynOrder = $this->dynamicsApi->getOrderByIncrementId($orderId);
        if ($dynOrder === false) {
            $this->logger->err("[createProvisioning] Dynamics order not found. Order id: $orderId");
        }

        $dynProductId = $this->dynamicsApi->findProductIdBySku($targetSku);
        if ($dynProductId === false) {
            $this->logger->err("[createProvisioning] Dynamics product not found. Sku: $targetSku");
        }

        $provisioning = [
            "new_sim@odata.bind" => "/cs_sims($sim->cs_simid)",
            "new_date" => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
        ];

        if ($dynOrder !== false) {
            $provisioning['new_account@odata.bind'] = "/accounts($dynOrder->_customerid_value)";
            $provisioning['new_order@odata.bind'] = "/salesorders($dynOrder->salesorderid)";
        }
        if ($dynProductId !== false) {
            $provisioning['new_product@odata.bind'] = "/products($dynProductId)";
        }
        if (!is_null($sim->cs_service)) {
            $provisioning['new_service'] = $sim->cs_service;
        }
        if (!is_null($sim->cs_type)) {
            $provisioning['new_type'] = $sim->cs_type;
        }
        if (!is_null($sim->cs_network)) {
            $provisioning['new_network'] = $sim->cs_network;
        }
        if (!is_null($sim->new_nickname)) {
            $provisioning['new_nickname'] = $sim->new_nickname;
        }

        return $this->dynamicsApi->createProvisioning($provisioning);
    }

    private function getReference(\Magento\Sales\Model\Order $order): string {
        return "{$order->getIncrementId()} - {$order->getCustomerLastname()}";
    }

    private function sendEmail($sim, \Magento\Sales\Model\Order $order, string $message) {
        $to = $this->dynamicsApi->isDevEnv()
            ? 'persianplum@arrivalsib.com' // Temporary email for testing
            : 'sales@canadasatellite.ca';

        $this->logger->info("[sendEmail] Begin. Message: $message");
        try {
            $this->emailSender->sendTopUpEmail($sim, $order, $message, $to);
        } catch (\Exception $e) {
            $this->logger->err("[sendEmail] Exception: {$e->getMessage()}");
        }
        $this->logger->info("[sendEmail] End");
    }

    private function getNewExpiryDate(int $days): string {
        $now = new \DateTime();
        $now->modify("+$days day");
        return $now->format('Y-m-d\TH:i:s\Z');
    }

    public function processAutoRecharge() {
        $this->logger->info("[processAutoRecharge] Begin");

        $sims = $this->dynamicsApi->getAutoRechargeSims();

        $subStatusMapping = [
            100000014 => 100000000, // Auto Recharge - PAID -> Auto Recharge
            100000017 => 100000001, // 0/2 -> 1/2
            100000001 => 100000002, // 1/2 -> 2/2
            100000004 => null, // PAID -> NULL
        ];

        foreach ($sims as $sim) {
            $this->logger->info("[processAutoRecharge] Begin processing SIM $sim->cs_number");

            if (!array_key_exists($sim->new_substatus, $subStatusMapping)) {
                $this->logger->err("[processAutoRecharge] Skip. Sub Status is incorrect. Sub Status: $sim->new_substatus");
                continue;
            }

            if (empty($sim->new_quicknote) || !preg_match('/^#?(\d+)$/', $sim->new_quicknote, $match)) {
                $this->logger->err("[processAutoRecharge] Skip. Quick note does not contain orderId. Quick note: $sim->new_quicknote");
                continue;
            }

            try {
                $order = $this->orderRepo->loadByIncrementId($match[1]);
            } catch (\Exception $e) {
                $this->logger->err("[processAutoRecharge] Skip. Failed to load order. Message: {$e->getMessage()}");
                continue;
            }

            if (!empty($sim->_new_ast_voucher_value)) {
                if ($sim->new_substatus === 100000014) {
                    $this->logger->err("[processAutoRecharge] Skip. SIM has AST Voucher, but Sub Status is AutoRecharge - PAID");
                    continue;
                }

                $this->logger->info("[processAutoRecharge] Get AST Voucher by id in SIM ($sim->_new_ast_voucher_value)");
                $voucher = $this->dynamicsApi->getAstVoucherByGuid($sim->_new_ast_voucher_value);
            } else if (!is_null($sim->cs_plan)) {
                $this->logger->info("[processAutoRecharge] Get AST Voucher by plan in SIM ($sim->cs_plan)");
                $voucher = $this->dynamicsApi->getAstVoucherByPlan($sim->cs_plan);
            } else {
                $this->logger->err("[processAutoRecharge] Skip. AST Voucher and plan not found. SIM: $sim->cs_simid");
                continue;
            }

            if (!$voucher) {
                $this->logger->err("[processAutoRecharge] Skip. Voucher not found. SIM: $sim->cs_simid");
                continue;
            }

            if (is_null($voucher->new_expire_days)) {
                $this->logger->err("[processAutoRecharge] Skip. Voucher expire days is null");
                continue;
            }

            $success = $this->processTopUp($sim, $voucher, $order);
            if (!$success) {
                $this->logger->err("[processAutoRecharge] Skip. Received error from AST API");
                continue;
            }

            $this->logger->info("[processAutoRecharge] Updating SIM...");
            $payload = [
                'new_substatus' => $subStatusMapping[$sim->new_substatus],
                'cs_expirydate' => $this->getNewExpiryDate($voucher->new_expire_days),
                'new_quicknote' => null
            ];
            if ($sim->new_substatus !== 100000017) { // Do not clear AST Voucher if Sub Status was equals 0/2
                $payload['new_ast_voucher'] = null;
            }
            $this->logger->info("[processAutoRecharge] SIM update payload: " . json_encode($payload));
            $this->dynamicsApi->updateSim($sim->cs_simid, $payload);
            $this->logger->info("[processAutoRecharge] SIM updated");

            $this->logger->info("[processAutoRecharge] Creating provisioning...");
            $provId = $this->createProvisioning($sim, $order->getIncrementId(), $voucher->new_name);
            $this->logger->info("[processAutoRecharge] Provisioning created. Guid: $provId");
        }

        $this->logger->info("[processAutoRecharge] End");
    }
}
