<?php

namespace CanadaSatellite\AstIntegration\AstManagement;

class AstManager {

    private $restApi;
    private $iriSimFactory;
    private $inmSimFactory;
    private $logger;
    private $iriTopFactory;
    private $inmTopFactory;
    private $simValidator;
    private $providerResolver;
    private $dynamicsManager;
    private $objectManager;

    public function __construct(
        \CanadaSatellite\AstIntegration\Rest\RestApi                                 $restApi,
        \CanadaSatellite\AstIntegration\AstManagement\Factories\IridiumSimFactory    $iriSimFactory,
        \CanadaSatellite\AstIntegration\AstManagement\Factories\InmarsatSimFactory   $inmSimFactory,
        \CanadaSatellite\AstIntegration\Logger\Logger                                $logger,
        \CanadaSatellite\AstIntegration\AstManagement\Factories\IridiumTopupFactory  $iriTopFactory,
        \CanadaSatellite\AstIntegration\AstManagement\Factories\InmarsatTopupFactory $inmTopFactory,
        \CanadaSatellite\DynamicsIntegration\Validators\IccidValidator               $simValidator,
        \CanadaSatellite\DynamicsIntegration\Utils\SatelliteProviderResolver         $providerResolver,
        \Magento\Framework\ObjectManagerInterface                                    $objectManager
    ) {
        $this->restApi = $restApi;
        $this->iriSimFactory = $iriSimFactory;
        $this->inmSimFactory = $inmSimFactory;
        $this->logger = $logger;
        $this->iriTopFactory = $iriTopFactory;
        $this->inmTopFactory = $inmTopFactory;
        $this->simValidator = $simValidator;
        $this->providerResolver = $providerResolver;
        $this->objectManager = $objectManager;
    }

    /**
     * @param $simNumber
     * @param $reference
     * @param $serviceTypeId
     * @param $planId
     * @param $planOptions
     * @param $billProfileId
     * @return object|null
     */
    public function iridiumActivateSim($simNumber, $reference, $serviceTypeId,
                                       $planId, $planOptions, $billProfileId) {

        $sim = $this->iriSimFactory->create(
            $simNumber,
            $serviceTypeId,
            $planId,
            $planOptions,
            $reference,
            $billProfileId
        );

        $resp = $this->restApi->iridiumActivate($sim);

        if ($resp->Error_Code !== 0) {
            $this->logger->info("[AST manager] Iridium SIM #{$sim->getSimNumber()} activation error: {$resp->Message}");
            return null;
        }

        return $resp->Return_Objects[0];
    }

    public function iridiumTopupSim($simNumber, $reference, $serviceTypeId, $voucher, $voucherQuantity) {
        $sim = $this->iriTopFactory->create(
            $simNumber,
            $serviceTypeId,
            $reference,
            $voucher,
            $voucherQuantity,
            0
        );

        $resp = $this->restApi->iridiumTopUp($sim);

        if ($resp->Error_Code !== 0) {
            $this->logger->info("[AST manager] Iridium SIM #{$sim->getSimNumber()} top up error: {$resp->Message}");
            return null;
        }

        return $resp->Return_Objects[0];
    }

    /**
     * @param $simNumber
     * @param $reference
     * @param $serviceTypeId
     * @param $category
     * @param $packageId
     * @param $ratePlanId
     * @param $billProfileId
     * @return object|null
     */
    public function inmarsatActivateSim($simNumber, $reference, $serviceTypeId, $category,
                                        $packageId, $ratePlanId, $billProfileId) {
        $sim = $this->inmSimFactory->create(
            $simNumber,
            $serviceTypeId,
            $category,
            $packageId,
            $ratePlanId,
            $reference,
            $billProfileId
        );

        $resp = $this->restApi->inmarsatActivate($sim);

        if ($resp->Error_Code !== 0) {
            $this->logger->info("[AST manager] Inmarsat SIM #{$sim->getSimNumber()} activation error: {$resp->Message}");
            return null;
        }

        return $resp->Return_Objects[0];
    }

    /**
     * @param $simNumber
     * @param $reference
     * @param $serviceTypeId
     * @param $voucher
     * @param $voucherQuantity
     * @return object|null
     */
    public function inmarsatTopupSim($simNumber, $reference, $serviceTypeId, $voucher, $voucherQuantity) {
        $sim = $this->inmTopFactory->create(
            $simNumber,
            $serviceTypeId,
            $reference
        );
        $sim->addVoucher($voucher, $voucherQuantity);

        $resp = $this->restApi->inmarsatTopup($sim);

        if ($resp->Error_Code !== 0) {
            $this->logger->info("[AST manager] Inmarsat SIM #{$sim->getSimNumber()} top up error: {$resp->Message}");
            return null;
        }

        return $resp->Return_Objects[0];
    }

    /**
     * @param integer $dataId
     * @return object
     */
    public function getActionStatus($dataId) {
        return $this->restApi->getActionStatus($dataId);
    }

    public function getProductTopupAttributes($product) {
        $voucherId = $product->getCustomAttribute('ast_voucher_id');
        $voucherQuantity = $product->getCustomAttribute('ast_voucher_quantity');
        $serviceTypeId = $product->getCustomAttribute('ast_service_type_id');
        if (is_null($voucherId) || is_null($voucherQuantity) || is_null($serviceTypeId)) {
            throw new \Exception('One of product custom attributes not exists');
        }

        return [
            'Voucher' => $voucherId->getValue(),
            'Quantity' => intval($voucherQuantity->getValue()),
            'ServiceTypeId' => intval($serviceTypeId->getValue())
        ];
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string $simNumber
     * @param string $reference
     * @param integer $quantity
     * @return boolean
     * @throws \Exception
     */
    public function processTopupProduct($product, $simNumber, $reference, $quantity = 1) {
        $attributes = $this->getProductTopupAttributes($product);

        return $this->processTopup($simNumber,
            $attributes['ServiceTypeId'],
            $attributes['Voucher'],
            $attributes['Quantity'] * $quantity,
            $reference);
    }

    /**
     * @param string $simNumber
     * @param integer $serviceTypeId
     * @param string $voucher
     * @param integer $quantity
     * @param string $reference
     * @return boolean True if topup operation completed successfully, else false
     */
    public function processTopup($simNumber, $serviceTypeId, $voucher, $quantity, $reference) {
        if (!$this->providerResolver->isIccid($simNumber)) {
            $dynamicsManager = $this->objectManager->get('CanadaSatellite\DynamicsIntegration\DynamicsCrm\DynamicsCrm');
            $simNumber = $dynamicsManager->getSimNumberBySatelliteNumber($simNumber);
        }

        if (!$simNumber || !$this->simValidator->validateIccid($simNumber)) {
            return false;
        }

        $this->logger->info("[processTopup] -> Topup begin. SimNumber = $simNumber. ServiceTypeId = $serviceTypeId. Voucher = $voucher. Quantity = $quantity");

        if ($this->providerResolver->isSimIridium($simNumber)) {
            $this->iridiumTopupSim($simNumber, $reference, $serviceTypeId, $voucher, $quantity);
            $this->logger->info("[processTopup] -> Iridium topup end. SimNumber = $simNumber. Voucher = $voucher. Quantity = $quantity");
            return true;
        } else if ($this->providerResolver->isSimInmarsat($simNumber)) {
            $this->inmarsatTopupSim($simNumber, $reference, $serviceTypeId, $voucher, $quantity);
            $this->logger->info("[processTopup] -> Inmarsat topup end. SimNumber = $simNumber. Voucher = $voucher. Quantity = $quantity");
            return true;
        } else {
            $this->logger->info("[processTopup] -> Unknown sim provider. SimNumber = $simNumber. Voucher = $voucher. Quantity = $quantity");
            return false;
        }
    }
}