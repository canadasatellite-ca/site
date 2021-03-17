<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model\Import;

use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\Framework\Phrase;

/**
 * Class Validate
 * @package Aitoc\OrdersExportImport\Model\Import
 */
class Validate
{
    use \Aitoc\OrdersExportImport\Traits\Additional;

    const LIMIT_ERRORS_MESSAGE = 100;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * @var Converter
     */
    public $converter;

    /**
     * @var \Aitoc\OrdersExportImport\Model\Converter\Publisher
     */
    public $publish;

    /**
     * @var array
     */
    private $params;

    /**
     * @var BLock
     */
    private $resultBlock;

    /**
     * @var ProcessingErrorAggregatorInterface
     */
    private $errorAggregator;

    /**
     * @var Converter\Xml
     */
    public $convertXML;

    /**
     * @var \Aitoc\OrdersExportImport\Helper\Entities
     */
    public $entities;

    /**
     * @var \Aitoc\OrdersExportImport\Model\Import\Converter\CSV
     */
    public $convertCSV;

    /**
     * @var \Aitoc\OrdersExportImport\Model\Import\Converter\AdvancedCSV
     */
    public $convertAdvancedCSV;

    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var array
     */
    public $notificationsArray;

    /**
     * Validate constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param Converter $converter
     * @param \Aitoc\OrdersExportImport\Model\Converter\Publisher $publish
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param \Aitoc\OrdersExportImport\Helper\Entities $entities
     * @param Converter\Xml $convertXml
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Aitoc\OrdersExportImport\Model\Import\Converter $converter,
        \Aitoc\OrdersExportImport\Model\Converter\Publisher $publish,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Aitoc\OrdersExportImport\Helper\Entities $entities,
        \Aitoc\OrdersExportImport\Model\Import\Converter\XML $convertXml,
        \Aitoc\OrdersExportImport\Model\Import\Converter\CSV $convertCsv,
        \Aitoc\OrdersExportImport\Model\Import\Converter\AdvancedCSV $convertAdvancedCsv,
        \Magento\Framework\Registry $registry
    ) {
        $this->objectManager = $objectManager;
        $this->converter = $converter;
        $this->publish = $publish;
        $this->errorAggregator = $errorAggregator;
        $this->entities = $entities;
        $this->convertXML = $convertXml;
        $this->convertCSV = $convertCsv;
        $this->convertAdvancedCSV = $convertAdvancedCsv;
        $this->registry = $registry;
        $this->notificationsArray = [];
    }

    /**
     * @param $data
     */
    public function validate($data)
    {
        $this->setParams($data);
        $this->isFile($data['filename']);
    }

    /**
     * @param $filename
     */
    public function isFile($filename)
    {
        $params = $this->getParams();
        $list   = explode(";", $filename);

        try {
            foreach ($list as $file) {
                switch ($params['file_type']) {
                    case Converter::FILE_TYPE_XML:
                        if ($this->isXMLContentValid($file)) {
                            $this->validateObjectsXML($file);
                        }
                        break;
                    case Converter::FILE_TYPE_CSV:
                        $this->validateObjectsCSV($file);
                        break;
                    case Converter::FILE_TYPE_ADVANCED_CSV:
                        $this->validateObjectsAdvancedCSV($file);
                        break;
                };
            }
        } catch (\Exception $e) {
            $this->getResultBlock()->addError(__($e->getMessage()));
            $this->addErrorMessages($this->getResultBlock(), $this->getErrorAggregator());
        }
    }

    /**
     * @param $data
     */
    public function setParams($data)
    {
        $this->params = unserialize($data['serialized_config']);
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $block
     */
    public function setResultBlock($block)
    {
        $this->resultBlock = $block;
    }

    /**
     * @return BLock
     */
    public function getResultBlock()
    {
        return $this->resultBlock;
    }

    /**
     * @return ProcessingErrorAggregatorInterface
     */
    public function getErrorAggregator()
    {
        return $this->errorAggregator;
    }

    /**
     * @param $file
     * @param string $version
     * @param string $encoding
     */
    public function isXMLContentValid($file, $version = '1.0', $encoding = 'utf-8')
    {
        $params = $this->getParams();
        if ($params['file_name'][0]['type'] != 'text/xml') {
            $this->getResultBlock()->addError(__('This is not format xml'));
            
            return 0;
        }
        try {
            $xml        = simplexml_load_file($file);
            $xmlContent = $xml->asXml();
            if (trim($xmlContent) == '') {
                $this->getResultBlock()->addError(__('This file is empty. Please try another one.'));
            }
            libxml_use_internal_errors(true);
            $doc = new \DOMDocument($version, $encoding);
            $doc->loadXML($xmlContent);
            $errors = libxml_get_errors();
            if (count($errors)) {
                $this->getResultBlock()->addError(__('Data validation failed. Please fix the following errors and upload the file again.'));
                $this->addErrorMessages($this->getResultBlock(), $this->getErrorAggregator());
                libxml_clear_errors();
            }
        } catch (\Exception $e) {
            $this->getResultBlock()->addError(__($e->getMessage()));
            $this->addErrorMessages($this->getResultBlock(), $this->getErrorAggregator());
        }

        return 1;
    }

    /**
     * @param $file
     */
    public function validateObjectsXML($file)
    {

        $xml = $this->publish->readXml($file);
        $this->convertXML->setParams($this->getParams());
        $modelArray = [];
        if (count($xml)) {
            $modelArray = $this->convertXML->getNodes($xml, $this->entities->getImportEntities(), 0);
        }
        foreach ($this->partCollection($modelArray) as $val) {
            $this->setModeles($val);
        }

        if (array_key_exists('error', $this->notificationsArray)) {
            foreach ($this->notificationsArray['error'] as $orderId => $orderErrorsArray) {
                $text = "<b>Order #" . $orderId . " contains following errors:</b><br/>";
                foreach ($orderErrorsArray as $errorItem) {
                    $text .= "&emsp; - " . $errorItem . "<br/>";
                }
                $this->getResultBlock()->addError($text);
            }
        }
        if (array_key_exists('notice', $this->notificationsArray)) {
            foreach ($this->notificationsArray['notice'] as $orderId => $orderNoticeArray) {
                $text = "<b>Order #" . $orderId . " contains following warnings:</b><br/>";
                foreach ($orderNoticeArray as $noticeItem) {
                    $text .=  "&emsp; - " . $noticeItem . "<br/>";
                }
                $this->getResultBlock()->addNotice($text);
            }
        }

    }

    /**
     * @param $file
     * @return int
     */
    public function validateObjectsCSV($file)
    {
        $params = $this->getParams();
        if (!in_array($params['file_name'][0]['type'], ['text/csv', 'text/comma-separated-values', 'application/vnd.ms-excel'])) {
            $this->getResultBlock()->addError(__('This is not format csv'));

            return 0;
        }
        $this->publish->initCSV($file, "r");
        $this->convertCSV->setParams($this->getParams());
        $line                      = 0;
        $this->convertCSV->headers = [];
        $count = 0;
        while (($row = $this->publish->readRow()) !== false) {
            if (!$line) {
                if (array_search('entity_type', $row) !== false) {
                    $this->getResultBlock()->addError(__('Not corrected format csv'));
                }
                $this->convertCSV->headers = $row;
                $count = count($row);
            } else {
                $newArray = [];
                foreach ($row as $key => $value) {
                    if ($value && $key < count($this->convertCSV->headers)) {
                        $newArray[$this->convertCSV->headers[$key]] = $this->convertCSV->onSerialize($this->convertCSV->isDate($value));
                    }
                }
                $this->convertCSV->entHeaders = $this->convertCSV->scopeHeaders(array_keys($newArray));
                if (count($newArray)) {
                    $modelArray = $this->convertCSV->getNodes($newArray);
                    $this->setModeles($modelArray);
                }
            }
            $line++;
        }

        if (array_key_exists('error', $this->notificationsArray)) {
            foreach ($this->notificationsArray['error'] as $orderId => $orderErrorsArray) {
                $text = "<b>Order #" . $orderId . " contains following errors:</b><br/>";
                foreach ($orderErrorsArray as $errorItem) {
                    $text .= "&emsp; - " . $errorItem . "<br/>";
                }
                $this->getResultBlock()->addError($text);
            }
        }
        if (array_key_exists('notice', $this->notificationsArray)) {
            foreach ($this->notificationsArray['notice'] as $orderId => $orderNoticeArray) {
                $text = "<b>Order #" . $orderId . " contains following warnings:</b><br/>";
                foreach ($orderNoticeArray as $noticeItem) {
                    $text .= "&emsp; - " . $noticeItem . "<br/>";
                }
                $this->getResultBlock()->addNotice($text);
            }
        }

        if ($line < 2) {
            $this->getResultBlock()->addError(
                __(
                    "File is invalid. Required for import fields are missing. Please add these fields with values for them into your file: increment_id, address1:postcode, address1:lastname, address1:street, address1:city, address1:email, address1:telephone, address1:country_id, address1:firstname, address1:address_type, address2:postcode,address2:lastname, address2:street, address2:city, address2:email, address2:telephone, address2:country_id, address2:firstname, address2:address_type, payment1:shipping_captured, payment1:base_amount_paid, payment1:base_shipping_amount, payment1:shipping_amount, payment1:amount_paid, payment1:base_amount_ordered, payment1:amount_ordered, payment1:method"
                )
            );
            $this->getResultBlock()->addAction(
                'exception',
                'show',
                1
            );
        }
    }

    /**
     * @param $file
     * @return int
     */
    public function validateObjectsAdvancedCSV($file)
    {
        $params = $this->getParams();
        if (!in_array($params['file_name'][0]['type'], ['text/csv', 'text/comma-separated-values', 'application/vnd.ms-excel'])) {
            $this->getResultBlock()->addError(__('This is not format csv'));

            return 0;
        }
        $this->publish->initCSV($file, "r");
        $scopeArray = [];
        $this->convertAdvancedCSV->setParams($this->getParams());
        $line                              = 0;
        $this->convertAdvancedCSV->headers = [];
        while (($row = $this->publish->readRow()) !== false) {
            if (!$line) {
                if (array_search('entity_type', $row) === false) {
                    $this->getResultBlock()->addError(__('Not corrected format csv'));
                }
                $this->convertAdvancedCSV->headers = $row;
            } else {
                if ($row[0] == 'order' && $line > 1) {
                    if (count($scopeArray) > 0) {
                        if (count($scopeArray)) {
                            $modelArray = $this->convertAdvancedCSV->getNodes($scopeArray);
                            $this->setModeles($modelArray);
                        }
                        $scopeArray   = [];
                        $scopeArray[] = $row;
                    }
                } else {
                    $scopeArray[] = $row;
                }
            }
            $line++;
        }
        if (count($scopeArray) > 0) {
            if (count($scopeArray)) {
                $modelArray = $this->convertAdvancedCSV->getNodes($scopeArray);
                $this->setModeles($modelArray);
            }
        }
        if (!$line) {
            $this->getResultBlock()->addError(
                __(
                    "File is invalid. Required for import fields are missing. Please add these fields with values for them into your file: increment_id, address1:postcode, address1:lastname, address1:street, address1:city, address1:email, address1:telephone, address1:country_id, address1:firstname, address1:address_type, address2:postcode,address2:lastname, address2:street, address2:city, address2:email, address2:telephone, address2:country_id, address2:firstname, address2:address_type, payment1:shipping_captured, payment1:base_amount_paid, payment1:base_shipping_amount, payment1:shipping_amount, payment1:amount_paid, payment1:base_amount_ordered, payment1:amount_ordered, payment1:method"
                )
            );
            $this->getResultBlock()->addAction(
                'exception',
                'show',
                1
            );
        }
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
        if (isset($dataOrder['order'])) {
            if (!isset($dataOrder['order']['fields'])
                || !isset($dataOrder['order']['addresses'])
                || !isset($dataOrder['order']['payments'])
            ) {
                $beginText = '';
                if (isset($dataOrder['order']['fields']['increment_id'])) {
                    $beginText = 'Order ' . $dataOrder['order']['fields']['increment_id'] . ' ';
                    $this->notificationsArray['error'][$dataOrder['order']['fields']['increment_id']][] = __(
                        "The order can not be imported because required for import fields are missing. Please make sure that the order contains following data: increment_id, address1:postcode, address1:lastname, address1:street, address1:city, address1:email, address1:telephone, address1:country_id, address1:firstname, address1:address_type, address2:postcode, address2:lastname, address2:street, address2:city, address2:email, address2:telephone, address2:country_id, address2:firstname, address2:address_type, payment1:shipping_captured, payment1:base_amount_paid, payment1:base_shipping_amount, payment1:shipping_amount, payment1:amount_paid, payment1:base_amount_ordered, payment1:amount_ordered, payment1:method"
                    );
                } else {
                    $this->getResultBlock()->addError(
                        __(
                            "The order can not be imported because required for import fields are missing. Please make sure that the order contains following data: increment_id, address1:postcode, address1:lastname, address1:street, address1:city, address1:email, address1:telephone, address1:country_id, address1:firstname, address1:address_type, address2:postcode, address2:lastname, address2:street, address2:city, address2:email, address2:telephone, address2:country_id, address2:firstname, address2:address_type, payment1:shipping_captured, payment1:base_amount_paid, payment1:base_shipping_amount, payment1:shipping_amount, payment1:amount_paid, payment1:base_amount_ordered, payment1:amount_ordered, payment1:method"
                        )
                    );
                }

                return 0;
            }
            foreach ($dataOrder['order'] as $keyOrd => $value) {
                if ($keyOrd == 'fields') {
                    $value = $this->isStore($value);
                    if (!isset($value['increment_id'])|| !$value['increment_id']) {
                        if (!$this->registry->registry('aitoc_order_export_import_increment_error')) {
                            $this->getResultBlock()->addError(
                                __(
                                    "Some orders will not be imported because they are missing the increment_id value (order number)."
                                )
                            );
                            $this->registry->register('aitoc_order_export_import_increment_error', true);
                        }
                        return 0;
                    }

                    $order = $this->setModel($class['orders']['model'], $value, 0, 1);
                    if (!$order) {
                        $object = $this->objectManager->create($class['orders']['model']);
                        $object->load($value['increment_id'], 'increment_id');
                        $this->getResultBlock()->addNotice(
                            __(
                                '<b>Order #%1 already exists</b>',
                                $object->getIncrementId()
                            )
                        );

                        return 0;
                    }
                }
                $data[$keyOrd] = $this->scopeData($keyOrd, $value, $order, $class);
                $this->isPaymentTransaction($keyOrd, $value, $class, $scope, $order);
                if (in_array($keyOrd, ['invoices', 'shipments', 'creditmemos'])) {
                    $secondData[$keyOrd] = $value;
                }
            }

            if (!isset($data['payments'])) {
                $this->notificationsArray['error'][$order->getIncrementId()][] = __(
                    'Order haven\'t payment. An order with a missed payment information can not be imported in Magento.  Please add these fields with values for them into your import file: shipping_captured, base_amount_paid, base_shipping_amount, shipping_amount, amount_paid, base_amount_ordered, amount_ordered, method'
                );

                return 0;
            }
            if (!isset($data['addresses'])) {
                $this->notificationsArray['notice'][$order->getIncrementId()][] = __(
                    'Order #%1 haven\'t adresses. An order with a missed address information can not be imported in Magento. Please add these fields with values for them into your import file: postcode, lastname, street, city, email, telephone, country_id, firstname, address_type',
                    $order->getIncrementId()
                );

                return 0;
            }
            $this->checkDataByModels($order, $data, $class, $secondData);
        } else {
            $this->getResultBlock()->addError(__('Empty Objects'));
        }
        if (!count($dataOrder)) {
            $this->getResultBlock()->addError(__('Empty Objects'));
        }
    }

    private function checkDataByModels($order, $data, $class, $secondData)
    {
        if (!is_null($order)) {
            foreach ($data as $key => $objects) {
                foreach ($objects as $element) {
                    $this->walk($order, $class[$key]['action'], $element);
                }
            }
            $this->validateModel($order, $class['orders']['validate'], 'order');
            foreach ($secondData as $keyOrd => $list) {
                foreach ($list as $value) {
                    foreach ($value as $keyDown => &$element) {
                        if (in_array($keyDown, ['fields', 'items', 'comments', 'trackingsinformation'])) {
                            $element = $this->isStore($element);
                            if (count($element)) {
                                if ($this->isItems($keyDown)) {
                                    foreach ($element as $el) {
                                        $this->loadElements(
                                            $el,
                                            $keyOrd,
                                            $keyDown,
                                            $class,
                                            $order
                                        );
                                    }
                                } else {
                                    $scope[$keyOrd . "Id"] = $this->loadElements(
                                        $element,
                                        $keyOrd,
                                        $keyDown,
                                        $class,
                                        $order
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $element
     * @param $keyOrd
     * @param $keyDown
     * @param $class
     * @param $order
     * @return int
     */
    public function loadElements($element, $keyOrd, $keyDown, $class, $order)
    {
        $errors  = [];
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
//                $text = 'Order #' . $order->getIncrementId() . ' ';
                $text = ucfirst($keyOrd);
                $text .= ": ";
                foreach ($errors as $error) {
                    $this->notificationsArray['notice'][$order->getIncrementId()][] = __(
                        $text . $error
                    );
                }

                return 0;
            }
        }

        $object = $this->setModel($class[$keyOrd . '_' . $keyDown]['model'], $element, 0);
        if (isset($class[$keyOrd . '_' . $keyDown]['validate'])) {
            $this->validateModel($object, $class[$keyOrd . '_' . $keyDown]['validate'], $keyOrd . '_' . $keyDown, $order);
        }
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
                if ($keyOrd == 'payments') {
                    $element = $this->addAdditionalInfo($element);
                }
                $newModel = $this->setModel($class[$keyOrd]['model'], $element, 0);
                
                if (isset($class[$keyOrd]['validate'])) {
                    $this->validateModel($newModel, $class[$keyOrd]['validate'], $keyOrd, $order, 1);
                }
                $data[] = $newModel;
            }
            if (!count($value)) {
                $data[] = $this->setModel($class[$keyOrd]['model'], [], 0);
            }
        }

        return $data;
    }

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock $resultBlock
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @return $this
     */
    protected function addErrorMessages(
        \Magento\Framework\View\Element\AbstractBlock $resultBlock,
        ProcessingErrorAggregatorInterface $errorAggregator
    ) {
        if ($errorAggregator->getErrorsCount()) {
            $message = '';
            $counter = 0;
            foreach ($this->getErrorMessages($errorAggregator) as $error) {
                $count = ++$counter;
                $message .= $count . '. ' . $error . '<br>';
                if ($counter >= self::LIMIT_ERRORS_MESSAGE) {
                    break;
                }
            }
            if ($errorAggregator->hasFatalExceptions()) {
                foreach ($this->getSystemExceptions($errorAggregator) as $error) {
                    $message .= $error->getErrorMessage()
                        . ' <a href="#" onclick="$(this).next().show();$(this).hide();return false;">'
                        . __('Show more') . '</a><div style="display:none;">' . __('Additional data') . ': '
                        . $error->getErrorDescription() . '</div>';
                }
            }
            try {
                $resultBlock->addNotice(
                    '<strong>' . __('Following Error(s) has been occurred during importing process:') . '</strong><br>'
                    . '<div class="import-error-wrapper">' . __('Only the first 100 errors are shown. ')
                    . '<div class="import-error-list">' . $message . '</div></div>'
                );
            } catch (\Exception $e) {
                foreach ($this->getErrorMessages($errorAggregator) as $errorMessage) {
                    $resultBlock->addError($errorMessage);
                }
            }
        }

        return $this;
    }

    /**
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @return array
     */
    protected function getErrorMessages(ProcessingErrorAggregatorInterface $errorAggregator)
    {
        $messages    = [];
        $rowMessages = $errorAggregator->getRowsGroupedByErrorCode([], [AbstractEntity::ERROR_CODE_SYSTEM_EXCEPTION]);
        foreach ($rowMessages as $errorCode => $rows) {
            $messages[] = $errorCode . ' ' . __('in row(s):') . ' ' . implode(', ', $rows);
        }

        return $messages;
    }

    /**
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @return array
     */
    protected function getSystemExceptions(ProcessingErrorAggregatorInterface $errorAggregator)
    {
        return $errorAggregator->getErrorsByCode([AbstractEntity::ERROR_CODE_SYSTEM_EXCEPTION]);
    }

    /**
     * @param $model
     */
    public function validateModel($model, $class, $entity, $order = null, $notEmpty = 0)
    {
        $validator = $this->objectManager->create($class);
        if ($validator) {
            if (!$notEmpty) {
                $errors = $validator->validate($model, []);
            } else {
                $errors = $validator->validate($model);
            }

            if (count($errors)) {
                $text = '';
                if (!$order) {
                    $incrementId = $model->getIncrementId();
//                    $text .= 'Order #' . $model->getIncrementId() . ' : ';
                } else {
                    $array = explode("_", $entity);
                    $incrementId = $order->getIncrementId();
//                    $text  = 'Order #' . $order->getIncrementId() . ' ';
                    if (count($array) > 1) {
                        foreach ($array as $item) {
                            $text .= ucfirst($item);
                        }
                    } else {
                        $text .= ucfirst($entity) . ' ';
                    }
                    $text .= ": ";
                }
                foreach ($errors as $errorMessage) {
                    if (strpos("Parent Order Id is a required field", $errorMessage) === false) {
                        $this->notificationsArray['error'][$incrementId][] = __($text . $errorMessage);
//                        $this->getResultBlock()->addError($text . $errorMessage);
                        $this->addErrorMessages($this->getResultBlock(), $this->getErrorAggregator());
                    }
                }
            }
        }
    }
}
