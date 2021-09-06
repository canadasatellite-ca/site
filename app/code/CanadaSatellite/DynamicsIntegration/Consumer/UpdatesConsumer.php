<?php

namespace CanadaSatellite\DynamicsIntegration\Consumer;

use CanadaSatellite\AstIntegration\LogicProcessors\AstQueueItem;
use CanadaSatellite\AstIntegration\LogicProcessors\OrderCustomOptionsHelper;
use CanadaSatellite\Theme\Model;
use Magento\Framework\Exception\NoSuchEntityException;

class UpdatesConsumer implements \CanadaSatellite\SimpleAmqp\Api\BatchConsumerInterface {
    private $customerFactory;
    private $customerUpdater;
    private $orderFactory;
    private $orderUpdater;
    private $productFactory;
    private $productUpdater;
    private $activationFormFactory;
    private $activationFormUpdater;
    private $orderNoteProcessor;
    private $logger;
    private $productRepository;
    private $orderRepository;
    private $configValuesProvider;
    private $astManager;
    private $state;

    function __construct(
        \CanadaSatellite\DynamicsIntegration\Model\CustomerFactory              $customerFactory,
        \CanadaSatellite\DynamicsIntegration\Updater\CustomerUpdater            $customerUpdater,
        \CanadaSatellite\DynamicsIntegration\Model\OrderFactory                 $orderFactory,
        \CanadaSatellite\DynamicsIntegration\Updater\OrderUpdater               $orderUpdater,
        \CanadaSatellite\DynamicsIntegration\Model\ProductFactory               $productFactory,
        \CanadaSatellite\DynamicsIntegration\Updater\ProductUpdater             $productUpdater,
        \CanadaSatellite\DynamicsIntegration\Model\ActivationFormFactory        $activationFormFactory,
        \CanadaSatellite\DynamicsIntegration\Updater\ActivationFormUpdater      $activationFormUpdater,
        \CanadaSatellite\DynamicsIntegration\LogicProcessors\OrderNoteProcessor $orderNoteProcessor,
        \CanadaSatellite\DynamicsIntegration\Logger\Logger                      $logger,
        \Magento\Catalog\Model\ProductRepository                                $productRepository,
        \Magento\Sales\Model\Order                                              $orderRepository,
        \CanadaSatellite\DynamicsIntegration\Config\ConfigValuesProvider        $configValuesProvider,
        \CanadaSatellite\AstIntegration\AstManagement\AstManager                $astManager,
        \Magento\Framework\App\State                                            $state
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerUpdater = $customerUpdater;
        $this->orderFactory = $orderFactory;
        $this->orderUpdater = $orderUpdater;
        $this->productFactory = $productFactory;
        $this->productUpdater = $productUpdater;
        $this->activationFormFactory = $activationFormFactory;
        $this->activationFormUpdater = $activationFormUpdater;
        $this->orderNoteProcessor = $orderNoteProcessor;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
        $this->configValuesProvider = $configValuesProvider;
        $this->astManager = $astManager;
        $this->state = $state;
    }

    function consume($batch, $client, $astQueue) {
        try {
            $this->state->getAreaCode();
        } catch (\Exception $e) {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        }

        $this->dump("Got message!", $batch);

        $eventsByKind = $this->groupByKind($batch, $client);
        $this->dump("Grouped:", $eventsByKind);

        foreach ($eventsByKind as $kind => $eventsByEntity) {
            foreach ($eventsByEntity as $entityId => $events) {
                // Events are envelopes from CanadaSatellite\SimpleAmqp\Envelope\MessageEnvelope
                try {
                    $envelope = $this->getLast($events);
                    $event = $envelope->getBody();

                    switch ($kind) {
                        case 'CustomerSaved':
                            $customer = $event->data;
                            $this->processCustomer($customer);
                            break;
                        case 'CustomerDeleted':
                            $customerId = $entityId;
                            $email = property_exists($event, 'email') ? $event->email : null;
                            $this->processCustomerDelete($customerId, $email);
                            break;
                        case 'OrderSaved':
                            $order = $event->data;
                            $this->processOrder($order);
                            break;
                        case 'OrderNoteAdded':
                            $orderId = $event->id;
                            $note = $event->data;
                            $this->processOrderNote($orderId, $note);
                            break;
                        case 'ProductSaved':
                            $product = $event->data;
                            $sku = $event->sku;
                            $this->processProduct($product, $sku);
                            break;
                        case 'ProductDeleted':
                            $productId = $entityId;
                            $sku = $event->sku;
                            $this->processProductDelete($productId, $sku);
                            break;
                        case 'ActivationFormSaved':
                            $activationForm = $event->data;
                            $this->processActivationForm($activationForm);
                            break;
                        case 'AstQueuePush':
                            $astQueue->push(new AstQueueItem($event->data->dataId,
                                $event->data->simNumber,
                                $event->data->voucher));
                            break;
                    }
                } catch (\Exception $e) {
                    $this->logger->info("Failed processing: " . $e->getMessage());
                    $this->logger->info("Stack trace: " . $e->getTraceAsString());
                } finally {
                    foreach ($events as $envelope) {
                        $client->ack($envelope->getAmqpMessage());
                    }
                }
            }
        }
    }

    private function groupByKind($batch, $client) {
        $groups = array();

        foreach ($batch as $envelope) {
            $event = $envelope->getBody();

            if (!property_exists($event, 'kind') || !property_exists($event, 'id')) {
                // Skip, it's not valid event.
                $client->ack($envelope->getAmqpMessage());
                continue;
            }

            if (!array_key_exists($event->kind, $groups)) {
                $groups[$event->kind] = array();
            }

            $kindGroup = &$groups[$event->kind];
            if (!array_key_exists($event->id, $kindGroup)) {
                $kindGroup[$event->id] = array();
            }

            $entityEvents = &$kindGroup[$event->id];
            $entityEvents [] = $envelope;
        }

        return $groups;
    }

    private function getLast($array) {
        if (!is_array($array)) {
            throw new \Exception("Not array.");
        }
        if (count($array) === 0) {
            throw new \Exception("Empty array.");
        }

        return $array[count($array) - 1];
    }

    private function processCustomer($customerEnvelope) {
        $customer = $this->customerFactory->fromEnvelope($customerEnvelope);

        $this->logger->info("[CustomerConsumer] -> Customer message received for customer {$customer->getId()}");

        $this->customerUpdater->createOrUpdate($customer);

        $this->logger->info("[CustomerConsumer] -> Customer message processed");
    }

    private function processCustomerDelete($customerId, $email) {
        $customer = $this->customerFactory->create($customerId, $email, 'Deleted', 'Deleted');

        $this->logger->info("[CustomerDeleteConsumer] -> Customer message received for customer delete $customerId");

        $this->customerUpdater->delete($customer);

        $this->logger->info("[CustomerDeleteConsumer] -> Customer message processed");
    }

    private function processOrder($orderEnvelope) {
        /** @var \CanadaSatellite\DynamicsIntegration\Model\Order $order */
        $order = $this->orderFactory->fromEnvelope($orderEnvelope);

        $this->logger->info("[OrderConsumer] -> Order message received for order {$order->getId()}");

        // Load Magento Order Object
        $mOrder = $this->orderRepository->loadByIncrementId($order->getIncrementId());

        // Check order paid
        // TODO: Check how payments works
        if ($mOrder->getBaseTotalDue() == 0) {
            // Iterate through order items
            foreach ($mOrder->getAllVisibleItems() as $item) {
                /** @type \Magento\Sales\Model\Order\Item $item */

                // Get outer product
                $product = $item->getProduct();

                // Check that outer product it's a topup card
                $itemType = $product->getCustomAttribute('product_ast_type');

                if (is_null($itemType) || $itemType->getValue() != 'topup') continue;

                // Parse item options
                $options = new OrderCustomOptionsHelper($item);
                $simNumber = $options->getFirstExistOptionValue(...$this->configValuesProvider->getTopupPhoneNumber());
                $targetSku = $options->getFirstExistOptionValue(...$this->configValuesProvider->getTopupTargetSku());

                // Check that sim number is exists
                if (is_null($simNumber)) {
                    $this->logger->info("[OrderConsumer] -> Sim number option not found. Full sku: {$item->getSku()}");
                    continue;
                }

                // Replace product object by inner product if option exists
                if (!is_null($targetSku)) {
                    try {
                        $product = $this->productRepository->get($targetSku);
                    } catch (NoSuchEntityException $e) {
                        $this->logger->info("[OrderConsumer] -> Product doesn't exists. Sku: $targetSku. Full sku: {$item->getSku()}");
                        continue;
                    }
                }

                $reference = "{$mOrder->getIncrementId()} - {$mOrder->getCustomerLastname()}";
                try {
                    $this->astManager->processTopupProduct($product, $simNumber, $reference,
                        intval($item->getQtyOrdered()));
                } catch (\Exception $e) {
                    $this->logger->info("[OrderConsumer] -> ProcessTopupProduct error: " . $e->getMessage());
                }
            }
        }

        $this->orderUpdater->createOrUpdate($order);

        $this->logger->info("[OrderConsumer] -> Order message processed");
    }

    private function processOrderNote($orderId, $note) {
        $this->logger->info("[OrderNoteConsumer] -> Order note message received for order $orderId");
        $this->orderUpdater->createOrderNote($orderId, $note);

        $order = $this->orderUpdater->getOrder($orderId);
        if ($order) {
            $this->orderNoteProcessor->processSimInNote($order, $note);
        }

        $this->logger->info("[OrderNoteConsumer] -> Order note message processed");
    }

    private function processProduct($productEnvelope, $sku) {
        $product = $this->productFactory->fromEnvelope($productEnvelope);

        $this->logger->info("[ProductConsumer] -> Product message received for product {$product->getId()}");

        $this->productUpdater->createOrUpdate($product, $sku);

        $this->logger->info("[ProductConsumer] -> Product message processed");
    }

    private function processProductDelete($productId, $sku) {
        $this->logger->info("[ProductDeleteConsumer] -> Product message received for product $productId");

        $this->productUpdater->delete($sku);

        $this->logger->info("[ProductDeleteConsumer] -> Product message processed");
    }

    private function processActivationForm($activationFormEnvelope) {
        $activationForm = $this->activationFormFactory->fromEnvelope($activationFormEnvelope);

        $this->logger->info("[ActivationFormConsumer] -> Activation form message received for activation form {$activationForm->getId()}");

        $this->activationFormUpdater->createOrUpdate($activationForm);

        $this->logger->info("[ActivationFormConsumer] -> Activation form message processed");
    }

    private function dump($message, $body) {
        echo "$message\r\n";
        var_dump($body);

        $this->logger->info($message . var_export($body, true));
    }
}