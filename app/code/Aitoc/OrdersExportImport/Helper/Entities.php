<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Helper;

use \Aitoc\OrdersExportImport\Model\Profile;
use \Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Entities
 *
 * @package Aitoc\OrdersExportImport\Helper
 */
class Entities
{
    use \Aitoc\OrdersExportImport\Traits\Additional;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $entities;

    /**
     * @var mixed
     */
    private $entity;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

    /**
     * @var mixed
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Entities constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->objectManager = $objectManager;
        $this->scopeConfig   = $scopeConfig;
        $this->entities      = $this->scopeConfig
            ->getValue('oei/export_entities', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @param $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param $element
     */
    public function setOrder($element)
    {
        $this->order = $element;
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return array
     */
    public function scope()
    {
        $scope = [];
        if (!$this->entities[$this->getEntity()]['inner']) {
            foreach ($this->getConfig() as $method) {
                $collection     = call_user_func([$this,
                    $this->toCamelCase("get_" . $this->getEntity() . "_" . $method)
                ]);
                $scope[$method] =
                    [
                        'collection' => $collection,
                        'item' => $this->entities[$this->getEntity()][$method]['item']
                    ];
            }
        } else {
            $collection = call_user_func([$this, $this->toCamelCase('get_' . $this->getEntity())]);
            $inc        = 0;
            $scope      = ['elements' => [], 'item' => $this->entities[$this->getEntity()]['item'], 'inner' => 1];
            foreach ($collection as $item) {
                foreach ($this->getConfig() as $method) {
                    if ($this->getEntity() != $method) {
                        $collection = call_user_func(
                            [$this, $this->toCamelCase('get_' . $this->getEntity() . "_" . $method)],
                            $item->getId()
                        );
                        if (!isset($scope['elements'][$inc])) {
                            $scope['elements'][$inc] = [
                                'element' => $item,
                                'inner' => [
                                    $method => [
                                        'collection' => $collection,
                                        'item' => $this->entities[$this->getEntity()][$method]['item']
                                    ]
                                ]
                            ];
                        } else {
                            $scope['elements'][$inc]['inner'][$method] = ['collection' => $collection,
                                'item' => $this->entities[$this->getEntity()][$method]['item']
                            ];
                        }
                    }
                }
                $inc++;
            }
        }

        return $scope;
    }

    /**
     * @param $collection
     * @return array]
     */
    public function maxElements($collection)
    {
        $scope = [];
        foreach ($this->partCollection($collection) as $element) {
            $this->setOrder($element);
            if (!$this->entities[$this->getEntity()]['inner']) {
                foreach ($this->getConfig() as $method) {
                    $collection = call_user_func([$this,
                        $this->toCamelCase("get_" . $this->getEntity() . "_" . $method)
                    ]);
                    $keys       = $this->scopeCollection($collection);
                    if (!isset($scope[$method]['count'])) {
                        $scope[$method]['count'] = count($collection);
                    } else {
                        $scope[$method]['count'] = ($scope[$method]['count'] < count($collection))
                            ? count($collection)
                            : $scope[$method]['count'];
                    }

                    if (!isset($scope[$method]['keys'])) {
                        $scope[$method]['keys'] = $keys;
                    } else {
                        if (count($scope[$method]['keys']) < count($keys)) {
                            $scope[$method]['keys'] = $keys;
                        }
                    }
                    if (!isset($scope[$method]['item'])) {
                        $scope[$method]['item'] = $this->entities[$this->getEntity()][$method]['item'];
                    }
                }
            } else {
                $collection = call_user_func([$this, $this->toCamelCase('get_' . $this->getEntity())]);
                $inc        = 0;
                if (!count($scope)) {
                    $scope = ['item' => $this->entities[$this->getEntity()]['item'], 'inner' => 1];
                }
                foreach ($collection as $item) {
                    $scope['elements'][$inc] = [
                        'element' => array_keys($item->toArray()),
                        'inner' => []
                    ];
                    foreach ($this->getConfig() as $method) {
                        if ($this->getEntity() != $method) {
                            $collection                       = call_user_func(
                                [$this, $this->toCamelCase('get_' . $this->getEntity() . "_" . $method)],
                                $item->getId()
                            );
                            $scope['elements'][$inc]['inner'] = [$method => []];
                            $keys                             = $this->scopeCollection($collection);
                            if (!isset($scope['elements'][$inc]['inner'][$method]['count'])) {
                                $scope['elements'][$inc]['inner'][$method]['count'] = count($collection);
                            } else {
                                $scope['elements'][$inc]['inner'][$method]['count'] =
                                    ($scope['elements'][$inc]['inner'][$method]['count'] < $collection->count())
                                        ? count($collection) : $scope['elements'][$inc]['inner'][$method]['count'];
                            }
                            if (!isset($scope['elements'][$inc]['inner'][$method]['keys'])) {
                                $scope['elements'][$inc]['inner'][$method]['keys'] = $keys;
                            } else {
                                if (count($scope['elements'][$inc]['inner'][$method]['keys']) < (count($keys))) {
                                    $scope['elements'][$inc]['inner'][$method]['keys'] = $keys;
                                }
                            }
                            if (!isset($scope['elements'][$inc]['inner'][$method]['item'])) {
                                $scope['elements'][$inc]['inner'][$method]['item'] =
                                    $this->entities[$this->getEntity()][$method]['item'];
                            }
                        }
                    }
                    $inc++;
                }
            }
        }

        return $scope;
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderItemInterface[]
     */
    public function getOrdersItems()
    {
        return $this->getOrder()->getItems();
    }

    /**
     * @return \Magento\Sales\Api\Data\OrderAddressInterface[]
     */
    public function getOrdersAddresses()
    {
        return $this->getOrder()->getAddresses();
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Payment\Collection
     */
    public function getOrdersPayments()
    {
        return $this->getOrder()->getPaymentsCollection();
    }

    /**
     * @return mixed
     */
    public function getOrdersPaymentstransaction()
    {
        return $this->objectManager->create('Magento\Sales\Model\Order\Payment\Transaction')
            ->getCollection()
            ->addOrderIdFilter($this->getOrder()->getId());
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Status\History\Collection
     */
    public function getOrdersStatuseshistory()
    {
        return $this->getOrder()->getStatusHistoryCollection();
    }

    /**
     * @return \Magento\Sales\Model\ResourceModel\Order\Invoice\Collection
     */
    public function getInvoices()
    {
        return $this->getOrder()->getInvoiceCollection();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getInvoicesComments($id)
    {
        return $this->objectManager
            ->create('Magento\Sales\Model\Order\Invoice\Comment')
            ->getCollection()
            ->addFieldToFilter('parent_id', ['eq' => $id]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getInvoicesItems($id)
    {
        return $this->objectManager
            ->create('Magento\Sales\Model\Order\Invoice\Item')
            ->getCollection()
            ->addFieldToFilter('parent_id', ['eq' => $id]);
    }

    /**
     * @return false|\Magento\Sales\Model\ResourceModel\Order\Shipment\Collection
     */
    public function getShipments()
    {
        return $this->getOrder()->getShipmentsCollection();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getShipmentsComments($id)
    {
        return $this->objectManager
            ->create('Magento\Sales\Model\Order\Shipment\Comment')
            ->getCollection()
            ->addFieldToFilter('parent_id', ['eq' => $id]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getShipmentsItems($id)
    {
        return $this->objectManager
            ->create('Magento\Sales\Model\Order\Shipment\Item')
            ->getCollection()
            ->addFieldToFilter('parent_id', ['eq' => $id]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getShipmentsTrackingsinformation($id)
    {
        return $this->objectManager
            ->create('Magento\Sales\Model\Order\Shipment\Track')
            ->getCollection()
            ->addFieldToFilter('parent_id', ['eq' => $id]);
    }

    /**
     * @return false|\Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection
     */
    public function getCreditmemos()
    {
        return $this->getOrder()->getCreditmemosCollection();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getCreditmemosComments($id)
    {
        return $this->objectManager
            ->create('Magento\Sales\Model\Order\Creditmemo\Comment')
            ->getCollection()
            ->addFieldToFilter('parent_id', ['eq' => $id]);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getCreditmemosItems($id)
    {
        return $this->objectManager
            ->create('Magento\Sales\Model\Order\Creditmemo\Item')
            ->getCollection()
            ->addFieldToFilter('parent_id', ['eq' => $id]);
    }

    /**
     * @return mixed
     */
    public function getImportEntities()
    {
        return $this->scopeConfig->getValue('oei/import_entities', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @return mixed
     */
    public function getClasses()
    {
        return $this->scopeConfig->getValue('oei/import_classes', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @param $collection
     * @return array
     */
    private function scopeCollection($collection)
    {
        $keys = [];
        if (is_array($collection)) {
            foreach ($collection as $element) {
                $keys = array_merge($keys, array_keys($element->toArray()));
            }
        } else {
            $keys = array_keys($collection->getFirstItem()->toArray());
        }

        return array_unique($keys);
    }
}
