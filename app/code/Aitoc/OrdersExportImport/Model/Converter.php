<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Model;

/**
 * Class Converter
 * @package Aitoc\OrdersExportImport\Model
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

    const FTP = 1;

    const EMAIL = 2;

    const ADVANCED_CSV = 'AdvancedCSV';

    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private $config;

    /**
     * @var \Magento\Sales\Model\Order
     */
    private $order;

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
     * @var \Magento\Framework\Filesystem\Io\Ftp
     */
    public $ftpConnect;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    public $messageManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    public $transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    public $store;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scope;

    /**
     * @var \Aitoc\OrdersExportImport\Model\Stack
     */
    public $stack;

    /**
     * Converter constructor.
     * @param \Magento\Sales\Model\Order $order
     * @param Converter\Publisher $publish
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Aitoc\OrdersExportImport\Helper\Entities $entities
     */
    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Aitoc\OrdersExportImport\Model\Converter\Publisher $publish,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Aitoc\OrdersExportImport\Helper\Entities $entities,
        \Magento\Framework\Filesystem\Io\Ftp $ftp,
        \Magento\Framework\Message\ManagerInterface $message,
        \Aitoc\OrdersExportImport\Model\Profile\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManager $store,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope
    ) {
        $this->order            = $order;
        $this->publish          = $publish;
        $this->objectManager    = $objectManager;
        $this->entities         = $entities;
        $this->ftpConnect       = $ftp;
        $this->ent              = ['orders', 'invoices', 'shipments', 'creditmemos'];
        $this->messageManager   = $message;
        $this->transportBuilder = $transportBuilder;
        $this->store            = $store;
        $this->scope            = $scope;
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

    /**
     * @param $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param $filename
     */
    public function convert($filename)
    {
        $config = $this->getConfig();
        $object = '';
        switch ($config['file_type']) {
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
        $output->setConfig($config);
        $output->setParams($this->getParams());
        $output->setStack($this->getStack()->getId());
        list($path, $filename) = $output->toFile($filename);
        if ($config['type'] == self::FTP) {
            $output->ftpSent($path, $filename);
        }
        if ($config['type'] == self::EMAIL) {
            $output->emailSend($path, $filename);
        }

    }

    /**
     * @return mixed
     */
    public function filter()
    {
        $collection = $this->getOrder()->getCollection();
        $params     = $this->getParams();
        if (isset($params['selected']) && $params['selected']) {
            $collection->addFieldToFilter('entity_id', ['in' => $params['selected']]);
        }

        return $this->setFields($collection);
    }

    /**
     * @param $collection
     * @return mixed
     */
    public function setFields($collection)
    {
        $config = $this->getConfig();
        if (!isset($config['sel_unselfields']) || (isset($config['sel_unselfields']) && !$config['sel_unselfields'])) {
            if (isset($config['fields'])) {
                $fields = $config['fields'];
                $collection->getSelect()->reset(\Magento\Framework\DB\Select::COLUMNS);
                $collection->getSelect()->columns($fields);
            }
        }

        return $collection;
    }

    /**
     * @param $element
     * @return array
     */
    public function getEntities($element, $headers = 0)
    {
        $entities = [];
        $config   = $this->getConfig();
        $ents     = $this->ent;
        if (!$headers) {
            $this->entities->setOrder($element);
        }
        foreach ($ents as $ent) {
            if (isset($config[$ent]) && $config[$ent]) {
                $this->entities->setEntity($ent);
                $this->entities->setConfig($config[$ent]);
                if (!$headers) {
                    $entities[$ent] = $this->entities->scope();
                } else {
                    $entities[$ent] = $this->entities->maxElements($element);
                }
            }
        }
        return $entities;
    }

    /**
     * @param $path
     * @param $file
     */
    public function ftpSent($path, $file)
    {
        $config = $this->getConfig();
        try {
            $result = $this->ftpConnect->open($this->getConnected());
            if (trim($config['path_ftp'])) {
                $result = $this->ftpConnect->cd('/' . trim($config['path_ftp'], ' /') . '/');
            }
            $result = $this->ftpConnect->write($file, $path . "/" . $file);
            $this->ftpConnect->close();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('FTP error: %s', $e->getMessage()));
        }
    }

    /**
     * @return array
     */
    public function getConnected()
    {
        $config          = $this->getConfig();
        $connectedParams =
            [
                'host' => trim($config['ftp']),
                'user' => trim($config['user_ftp']),
                'password' => trim($config['pass_ftp']),
                'passive' => trim($config['passmode_ftp']),
            ];
        if (strpos($connectedParams['host'], ':') !== false) {
            list($connectedParams['host'], $connectedParams['port']) = explode(':', $connectedParams['host']);
        }

        return $connectedParams;
    }

    /**
     * @param $path
     * @param $file
     */
    public function emailSend($path, $file)
    {
        $config     = $this->getConfig();
        $templateId = $this->scope->getValue('oei/email/template');
        if ($config['template_email'] != 'oei/email/template') {
            $templateId = $config['template_email'];
        }
        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => 'frontend', 'store' => $this->store->getStore()->getId()])
                ->setTemplateVars([])
                ->setFrom($config['sender_email'])
                ->addTo($config['send_email'], 'Export')
                ->createAttachment($path . "/" . $file, $file)
                ->getTransport();
            $transport->sendMessage();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Email error: %s', $e->getMessage()));
        }
    }

    /**
     * @param $id
     */
    public function setStack($id)
    {
        $this->stack = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Aitoc\OrdersExportImport\Model\Stack')->load($id);
    }

    /**
     * @return Stack
     */
    public function getStack()
    {
        return $this->stack;
    }
}
