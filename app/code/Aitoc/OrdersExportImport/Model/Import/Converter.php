<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Import;

/**
 * Class Converter
 * @package Aitoc\OrdersExportImport\Model\Import
 */
class Converter
{
    use \Aitoc\OrdersExportImport\Traits\Additional;

    const LENGTH = 25;

    const FILE_TYPE_XML = 0;

    const FILE_TYPE_CSV = 1;

    const FILE_TYPE_ADVANCED_CSV = 2;

    const XML = 'XML';

    const CSV = 'CSV';

    const ADVANCED_CSV = 'AdvancedCSV';

    const ITEM_CONFIGURABLE = 'configurable';

    const ITEM_SIMPLE = 'simple';
    
    const ITEM_BUNDLE = 'bundle';
    
    const ITEM_VIRTUAL = 'virtual';

    /**
     * @var array
     */
    private $params;

    /**
     * @var Publisher
     */
    public $publish;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * @var \Aitoc\OrdersExportImport\Helper\Entities
     */
    public $entities;

    public $ent;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    public $messageManager;

    /**
     * @var \Magento\SalesSequence\Model\Manager
     */
    private $sequenceManager;

    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    public $stated;
    /**
     * @var Frame/Result
     */
    public $frameResult;

    /**
     * Converter constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Aitoc\OrdersExportImport\Model\Converter\Publisher $publish
     * @param \Aitoc\OrdersExportImport\Helper\Entities $entities
     * @param \Magento\Framework\Message\ManagerInterface $message
     * @param \Magento\SalesSequence\Model\Manager $sequenceManager
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Aitoc\OrdersExportImport\Model\Converter\Publisher $publish,
        \Aitoc\OrdersExportImport\Helper\Entities $entities,
        \Magento\Framework\Message\ManagerInterface $message,
        \Magento\SalesSequence\Model\Manager $sequenceManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->objectManager   = $objectManager;
        $this->publish         = $publish;
        $this->entities        = $entities;
        $this->messageManager  = $message;
        $this->sequenceManager = $sequenceManager;
        $this->registry        = $registry;

    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    public function getBlockResult()
    {
        return $this->frameResult;
    }

    public function setBlockResult($block)
    {
        $this->frameResult = $block;
    }

    /**
     * @param $filename
     */
    public function convert($filename)
    {
        $params = $this->getParams();
        $object = '';
        switch ($params['file_type']) {
            case self::FILE_TYPE_XML:
                $object = self::XML;
                break;
            case self::FILE_TYPE_CSV:
                $object = self::CSV;
                break;
            case self::FILE_TYPE_ADVANCED_CSV:
                $object = self::ADVANCED_CSV;
                break;
        };

        $output = $this->objectManager->create(__CLASS__ . "\\" . $object);
        $output->setBlockResult($this->getBlockResult());
        $output->setParams($this->getParams());
        $output->toDB($filename);
    }

    /**
     * @param $dataOrder
     * @return int
     */
    public function setModeles($dataOrder)
    {
        $class      = $this->entities->getClasses();
        $scope      = [];
        $data       = [];
        $secondData = [];
        $order      = null;
        $params     = $this->getParams();
        $behavior   = $params['import_behavior'];
        $this->registry->unregister('isSecureArea');
        $this->registry->register('isSecureArea', 1);
        $status = 'pending';
        if (isset($dataOrder['order'])) {
            $this->staded = 0;
            foreach ($dataOrder['order'] as $keyOrd => $value) {
                if (!isset($dataOrder['order']['fields'])
                    || !isset($dataOrder['order']['addresses'])
                    || !isset($dataOrder['order']['payments'])
                ) {
                    $beginText = '';
                    if (isset($dataOrder['order']['fields']['increment_id'])) {
                        $beginText = 'Order ' . $dataOrder['order']['fields']['increment_id'] . ' ';
                    } else {
                        $beginText = 'The order ';
                    }
                    $this->getBlockResult()->addError(
                        __(
                            $beginText . "can not be imported because required for import fields are missing. Please make sure that the order contains following data: increment_id, address1:postcode, address1:lastname, address1:street, address1:city, address1:email, address1:telephone, address1:country_id, address1:firstname, address1:address_type, address2:postcode, address2:lastname, address2:street, address2:city, address2:email, address2:telephone, address2:country_id, address2:firstname, address2:address_type, payment1:shipping_captured, payment1:base_amount_paid, payment1:base_shipping_amount, payment1:shipping_amount, payment1:amount_paid, payment1:base_amount_ordered, payment1:amount_ordered, payment1:method"
                        )
                    );

                    return 0;
                }
                if ($keyOrd == 'fields') {
                    $value = $this->deleteNode($value, 'entity_id');
                    $value = $this->isStore($value);
                    if (!isset($value['increment_id'])) {
                        $this->getBlockResult()->addError(
                            __(
                                "Some orders will not be imported because they are missing the increment_id value (order number)."
                            )
                        );

                        return 0;
                    }
                    $order = $this->setModel($class['orders']['model'], $value, $behavior, 1);
                    if (!$order) {
                        $object = $this->objectManager->create($class['orders']['model']);
                        $object->load($value['increment_id'], 'increment_id');
                        $this->getBlockResult()->addNotice(
                            __(
                                'Order #%1 already exists',
                                $object->getIncrementId()
                            )
                        );

                        return 0;
                    }
                    if (isset($value['status'])) {
                        $status = $value['status'];
                    }
                }
                if ($behavior > 0) {
                    $this->deleteEntities($order);
                }
                if ($behavior == 2) {
                    $order->delete();
                    $text = 'Order #' . $order->getIncrementId() . ' has been deleted';
                    $this->getBlockResult()->addDeleted(__($text));

                    return 0;
                }

                try {
                    $data[$keyOrd] = $this->scopeData($keyOrd, $value, $order, $class);
                    $this->isPaymentTransaction($keyOrd, $value, $class, $scope, $order);
                } catch (\Exception $e) {
                    $text = 'Order #' . $order->getIncrementId() . '. ';
                    $this->getBlockResult()->addError(__($text . $e->getMessage()));
                }

                if (in_array($keyOrd, ['invoices', 'shipments', 'creditmemos'])) {
                    $secondData[$keyOrd] = $value;
                }
            }
            if (!isset($data['payments'])) {
                $this->getBlockResult()->addError(
                    __(
                        'Order #%1 haven\'t payment. An order with a missed payment information can not be imported in Magento. Please add these fields with values for them into your import file: shipping_captured, base_amount_paid, base_shipping_amount, shipping_amount, amount_paid, base_amount_ordered, amount_ordered, method',
                        $order->getIncrementId()
                    )
                );

                return 0;
            }
            if (!isset($data['addresses'])) {
                $this->getBlockResult()->addNotice(
                    __(
                        'Order #%1 haven\'t adresses. An order with a missed address information can not be imported in Magento. Please add these fields with values for them into your import file: postcode, lastname, street, city, email, telephone, country_id, firstname, address_type',
                        $order->getIncrementId()
                    )
                );

                return 0;
            }
            $this->validateDataByModels($order, $data, $class, $status, $secondData);
        }
    }

    private function validateDataByModels($order, $data, $class, $status, $secondData)
    {
        if (!is_null($order)) {
            foreach ($data as $key => $objects) {
                foreach ($objects as $element) {
                    $this->walk($order, $class[$key]['action'], $element);
                }
            }
            try {
                $order->setStatus($status);
                $order->save();
                $this->changeItems($order);
                $this->stated++;
            } catch (\Exception $e) {
                $text = 'Order #' . $order->getIncrementId() . '. ';
                $this->getBlockResult()->addError(__($text . $e->getMessage()));
            }
            $scope['ordersId'] = $order->getId();
            foreach ($secondData as $keyOrd => $list) {
                foreach ($list as $value) {
                    foreach ($value as $keyDown => &$element) {
                        try {
                            if (in_array($keyDown, ['fields', 'items', 'comments', 'trackingsinformation'])) {
                                if (count($element)) {
                                    if ($this->isItems($keyDown)) {
                                        foreach ($element as $el) {
                                            $el = $this->isStore($el);
                                            $this->loadElements(
                                                $el,
                                                $keyOrd,
                                                $keyDown,
                                                $scope,
                                                $class,
                                                $order,
                                                $value
                                            );
                                        }
                                    } else {
                                        $element               = $this->isStore($element);
                                        $scope[$keyOrd . "Id"] = $this->loadElements(
                                            $element,
                                            $keyOrd,
                                            $keyDown,
                                            $scope,
                                            $class,
                                            $order,
                                            $value
                                        );
                                    }
                                }
                            }
                        } catch (\Exception $e) {
                            $text = 'Order #' . $order->getIncrementId() . '. ';
                            $this->getBlockResult()->addError(__($text . $e->getMessage()));
                        }
                    }
                }
            }
            if ($this->stated) {
                $text = 'Order #' . $order->getIncrementId() . ' Imported';
                $this->getBlockResult()->addSuccess(__($text));
            }
        }
    }

    /**
     * @param $element
     * @param $keyOrd
     * @param $keyDown
     * @param $scope
     * @param $class
     * @param $order
     * @param $value
     * @return mixed
     */
    public function loadElements($element, $keyOrd, $keyDown, $scope, $class, $order, $value)
    {
        $errors = [];
        foreach ($class[$keyOrd . '_' . $keyDown]['params'] as $record) {
            $element[$record['field']] = $scope[$record['id']];
        }

        $element = $this->deleteNode($element, $class[$keyOrd . '_' . $keyDown]['entity']);
        if ($keyDown == 'fields') {
            if (!isset($element['increment_id'])) {
                $errors[] = 'Have not Increment Id';
            } else {
                $object = $this->objectManager->create($class[$keyOrd . '_' . $keyDown]['model']);
                $object->load($element['increment_id'], 'increment_id');
                if ($object->getId()) {
                    $errors[] = '#' . $element['increment_id'] . ' already exists';
                }
            }
            if (count($errors) > 0) {
                $text = 'Order #' . $order->getIncrementId() . '. ';
                $text .= ucfirst($keyOrd);
                $text .= ": ";
                foreach ($errors as $error) {
                    $this->getBlockResult()->addError(
                        __(
                            $text . $error
                        )
                    );
                }

                return 0;
            }
        }
        if ($keyOrd == 'shipments' && $keyDown == 'items') {
            return 0;
        }
        $element = $this->setOrderItem($keyDown, $element, $order);
        $object  = $this->setModel($class[$keyOrd . '_' . $keyDown]['model'], $element, 0);
        if (isset($class[$keyOrd . '_' . $keyDown]['parent'])) {
            $newModel = $this->objectManager->create(
                $class[$class[$keyOrd . '_' . $keyDown]['parent']]['model']
            );
            $newModel->load($scope[$keyOrd . "Id"]);
            $object->setParentId($newModel->getId());
            $this->walk($object, $class[$keyOrd . '_' . $keyDown]['action'], $newModel);
        }
        if (isset($class[$keyOrd . '_' . $keyDown]['child'])) {
            if (!isset($value['items'])) {
                $text = 'Order #' . $order->getIncrementId() . '. Not have Shipment Items';
                $this->getBlockResult()->addError(__($text));
            } else {
                foreach ($value['items'] as $item) {
                    $item     = $this->deleteNode($item, $class[$keyOrd . '_' . $keyDown]['entity']);
                    $item     = $this->deleteNode($item, 'parent_id');
                    $newModel = $this->objectManager->create(
                        $class[$class[$keyOrd . '_' . $keyDown]['child']]['model']
                    );
                    $item     = $this->setOrderItem('items', $item, $order);
                    $newModel->setData($item);
                    $this->walk($object, $class[$keyOrd . '_' . $keyDown]['action'], $newModel);
                }
            }
        }
        $object->save();

        return $object->getId();
    }


    /**
     * @param $entity
     * @return mixed
     */
    public function pregMatch($entity)
    {
        preg_match('/(\w+[^\d])+(\d)/im', $entity, $matches);

        return $matches;
    }

    /**
     * @param $val
     * @return mixed
     */
    public function isDate($val)
    {
        preg_match("/^'[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])/im", $val, $matches);
        if (is_array($matches)) {
            $val = str_replace("'", "", $val);
        }

        return $val;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function onSerialize($value)
    {
        $params = $this->getParams();
        if (strpos($value, "ser;") !== false) {
            $string = str_replace("%%", $params['delimeter'], str_replace("ser;", "", $value));
            $value  = json_decode($string, true);
        }

        return $value;
    }

    /**
     * @param $keyOrd
     * @param $value
     * @param $order
     * @param $class
     * @return array
     */
    public function scopeData($keyOrd, $value, $order, $class)
    {
        $data = [];
        if (in_array($keyOrd, ['items', 'addresses', 'payments', 'statuseshistory'])) {
            foreach ($value as $element) {
                $element = $this->isStore($element);
                foreach ($class[$keyOrd]['params'] as $record) {
                    $element = $this->deleteNode($element, $record['field']);
                    $element = $this->deleteNode($element, $class[$keyOrd]['entity']);
                }
                if ($keyOrd == 'addresses') {
                    $element = $this->isStore($element);
                    $element = $this->addCustomer($element, $order);
                    $order->setCustomerId($element['customer_id']);
                }
                if ($keyOrd == 'payments') {
                    $element = $this->addAdditionalInfo($element);
                }
                if ($keyOrd == 'items') {
                    $element = $this->changeItem($element);
                }
                $model  = $this->setModel($class[$keyOrd]['model'], $element, 0);
                $data[] = $model;
            }
            if (!count($value)) {
                $data[] = $this->setModel($class[$keyOrd]['model'], [], 0);
            }
        }

        return $data;
    }


    /**
     * @param $order
     */
    public function deleteEntities($order)
    {
        $this->deleteCollection($order->getItemsCollection());
        $this->deleteCollection($order->getAddresses());
        if (is_object($order->getPayment())) {
            $order->getPayment()->delete();
        }
        $this->deleteCollection($order->getStatusHistories());
        $this->deleteCollection($order->getInvoiceCollection());
        $this->deleteCollection($order->getShipmentsCollection());
        $this->deleteCollection($order->getCreditmemosCollection());
    }

    /**
     * @param $collection
     */
    public function deleteCollection($collection)
    {
        if (is_object($collection)) {
            if ($collection->getSize()) {
                foreach ($collection as $model) {
                    $model->delete();
                }
            }
        }
    }

    /**
     * @param $key
     * @param $element
     * @param $order
     * @return mixed
     */
    public function setOrderItem($key, $element, $order)
    {
        if ($key == 'items') {
            $element['order_item_id'] = null;
            $items                    = $order->getItems();
            if (count($items)) {
                foreach ($items as $item) {
                    if ($item->getName() == $element['name']) {
                        $element['order_item_id'] = $item->getId();
                    }
                }
            }
        }

        return $element;
    }

    public function changeItems($order)
    {
        $configurable = [];
        $record       = null;
        $items        = $order->getItems();
        $bundle = [];
        $ids = [];
        $isBundle = 0;
        $bundleId = [];
        if (count($items)) {
            foreach ($items as $item) {
                if ($item->getProductType() == self::ITEM_CONFIGURABLE) {
                    $configurable[] = ['id' => $item->getId(), 'sku' => $item->getSku()];
                } elseif ($item->getProductType() == self::ITEM_BUNDLE) {
                    $isBundle++;
                    $bundleId[$isBundle] = $item->getId();
                } elseif (in_array($item->getProductType(), [self::ITEM_SIMPLE, self::ITEM_VIRTUAL])) {
                    if (!$this->searchInConfigurable($item->getSku(), $configurable)) {
                        if (array_key_exists($isBundle, $bundleId)) {
                            if (!array_key_exists($bundleId[$isBundle], $bundle) || !isset($bundle[$bundleId[$isBundle]])) {
                                $bundle[$bundleId[$isBundle]] = [];
                            }
                            $bundle[$bundleId[$isBundle]][] =  $item->getId();
                        }
                    }
                }
            }
            foreach ($items as $item) {
                if ($item->getProductType() == self::ITEM_SIMPLE) {
                    if (count($configurable)) {
                        $record = $this->inSearch($item, $configurable);
                    }
                    if ($record) {
                        if (isset($ids[$item->getParentItemId()])) {
                            unset($ids[$item->getParentItemId()]);
                        }
                        $item->setParentItemId($record['id']);
                        $item->save();
                    }
                }
            }
            
            if ($isBundle > 0) {
                foreach ($items as $item) {
                    foreach ($bundle as $id => $record) {
                        if (in_array($item->getId(), $record)) {
                            $item->setParentItemId($id);
                            $item->save();
                        }
                    }
                }
            }
        }
    }

    public function inSearch($simple, $array)
    {
        foreach ($array as $item) {
            if ($simple->getSku() == $item['sku']) {
                return $item;
            }
        }

        return [];
    }

    public function searchInConfigurable($sku, $array)
    {
        foreach ($array as $record) {
            if ($sku == $record['sku']) {
                return true;
            }
        }

        return false;
    }
}
