<?php

namespace CanadaSatellite\DynamicsIntegration\LogicProcessors;

use CanadaSatellite\AstIntegration\LogicProcessors\OrderCustomOptionsHelper;
use Magento\Framework\Exception\NoSuchEntityException;

class OrderNoteProcessor {
    private $simFactory;
    private $simUpdater;
    private $configValuesProvider;
    private $simValidator;
    private $providerResolver;
    private $logger;
    private $orderRepository;
    private $productRepository;


    function __construct(
        \CanadaSatellite\DynamicsIntegration\Model\SimFactory                $simFactory,
        \CanadaSatellite\DynamicsIntegration\Updater\SimUpdater              $simUpdater,
        \CanadaSatellite\DynamicsIntegration\Config\ConfigValuesProvider     $configValuesProvider,
        \CanadaSatellite\DynamicsIntegration\Validators\IccidValidator       $simValidator,
        \CanadaSatellite\DynamicsIntegration\Utils\SatelliteProviderResolver $providerResolver,
        \CanadaSatellite\DynamicsIntegration\Logger\Logger                   $logger,
        \Magento\Sales\Model\Order                                           $orderRepository,
        \Magento\Catalog\Model\ProductRepository                             $productRepository
    ) {
        $this->simFactory = $simFactory;
        $this->simUpdater = $simUpdater;
        $this->configValuesProvider = $configValuesProvider;
        $this->simValidator = $simValidator;
        $this->providerResolver = $providerResolver;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Creates and activates SIM if it is presented in the note text
     * @param object $order Dynamics order object (salesorder)
     * @param string $noteText
     */
    function processSimInNote($order, $noteText) {
        // Load Magento Order Object
        $mOrder = $this->orderRepository->loadByIncrementId($order->name);

        // if note contains sim number and looks like the sim creation request
        $simListOrderNoteRegex = $this->configValuesProvider->getSimListOrderNoteRegex();

        if (preg_match($simListOrderNoteRegex, $noteText, $matches)) {
            $splitSimsListRegex = $this->configValuesProvider->getSplitSimsFromListRegex();

            if (preg_match_all($splitSimsListRegex, $matches[1], $simMatches)) {
                $iridiumItems = [];
                $inmarsatItems = [];
                $iridiumIndex = -1;
                $inmarsatIndex = -1;

                foreach ($mOrder->getAllVisibleItems() as $item) {
                    /** @type \Magento\Sales\Model\Order\Item $item */

                    $product = $item->getProduct();
                    $itemType = $product->getCustomAttribute('product_ast_type');
                    if (is_null($itemType)) continue;

                    $qty = intval($item->getQtyOrdered());
                    switch ($itemType->getValue()) {
                        case 'iridium':
                            for ($i = 0; $i < $qty; $i++) array_push($iridiumItems, $item);
                            break;
                        case 'inmarsat':
                            for ($i = 0; $i < $qty; $i++) array_push($inmarsatItems, $item);
                            break;
                    }
                }

                $plansHelper = new \CanadaSatellite\AstIntegration\LogicProcessors\AstPlansHelper();

                // Iterate through note numbers
                foreach ($simMatches[1] as $simNumber) {
                    if ($this->simValidator->validateIccid($simNumber)) {
                        $accountId = $order->_customerid_value;
                        $sim = null;

                        if ($this->providerResolver->isSimIridium($simNumber)) {
                            $iridiumIndex++;
                            $item = $iridiumIndex < count($iridiumItems) ? $iridiumItems[$iridiumIndex] : null;
                            $providerKey = 'iridium';
                        } else if ($this->providerResolver->isSimInmarsat($simNumber)) {
                            $inmarsatIndex++;
                            $item = $inmarsatIndex < count($inmarsatItems) ? $inmarsatItems[$inmarsatIndex] : null;
                            $providerKey = 'inmarsat';
                        } else {
                            $this->logger->info("[OrderNoteProcessor] -> Unknown provider. SimNumber = $simNumber");
                            continue;
                        }

                        if (is_null($item)) {
                            $sim = $this->simFactory->create($simNumber, $accountId, $order->name, $plansHelper->getMeta('NetworkCodes', $providerKey));
                            $simGuid = $this->simUpdater->createSim($sim);
                            $this->logger->info("[OrderNoteProcessor] -> Order item not found. Sim without plan created. Guid = $simGuid. SimNumber = $simNumber. AccountId = $accountId");
                            continue;
                        }

                        $this->logger->info("[OrderNoteProcessor] -> Provider = $providerKey | SimNumber = $simNumber");

                        $planKey = $item->getCustomAttribute('ast_plan_key');
                        if (is_null($planKey)) {
                            $options = new OrderCustomOptionsHelper($item, false);
                            $planKey = $options->getFirstExistOptionValue(...$this->configValuesProvider->getSimOrderPlan()); // PLAN SELECTION
                        } else {
                            $planKey = $planKey->getValue();
                        }

                        if (empty($planKey)) {
                            $this->logger->info("[OrderNoteProcessor] -> Plan key is empty. Provider = $providerKey. SimNumber = $simNumber");
                            continue;
                        }

                        $planData = $plansHelper->get($providerKey, $planKey);
                        if (is_null($planData)) {
                            $this->logger->info("[OrderNoteProcessor] -> Plan data not found. Provider = $providerKey. Plan key = $planKey. SimNumber = $simNumber");
                            continue;
                        }

                        $sim = $this->simFactory->create($simNumber, $accountId, $order->name,
                            $plansHelper->getMeta('NetworkCodes', $providerKey),
                            $planData['DynamicsService'], $planData['DynamicsType'], $planData['DynamicsPlan']);
                        $sim->setPlanKey($planKey);

                        // Voucher part
                        if (array_key_exists('Voucher', $planData)) {
                            $voucherData = $planData['Voucher'];
                            if (gettype($voucherData) === 'string') {
                                try {
                                    $innerProduct = $this->productRepository->get($voucherData);
                                    $value = $innerProduct->getCustomAttribute('dynamics_voucher_id');
                                    if (!is_null($value)) {
                                        $sim->setVoucher($value->getValue());
                                    }
                                } catch (NoSuchEntityException $e) {
                                    $this->logger->info("[OrderNoteProcessor] -> Inner topup product not found. SimNumber = $simNumber. AccountId = $accountId");
                                }
                            } else if (gettype($voucherData) === 'array') {
                                if (!empty($voucherData['dynamics_voucher_id'])) {
                                    $sim->setVoucher($voucherData['dynamics_voucher_id']);
                                }
                            }
                        }

                        $simGuid = $this->simUpdater->createSim($sim);
                        $this->logger->info("[OrderNoteProcessor] -> Sim created. Guid = $simGuid. SimNumber = $simNumber. AccountId = $accountId");
                    } else {
                        $this->logger->info("[OrderNoteProcessor] -> Sim ICCID is not valid. SimNumber = $simNumber");
                    }
                }
            }
        }
    }
}