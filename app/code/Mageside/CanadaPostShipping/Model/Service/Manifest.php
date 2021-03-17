<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Service;

/**
 * Class Manifest
 * @package Mageside\CanadaPostShipping\Model\Service
 * @documentation https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/shippingmanifest/soap/manifest.jsf
 */
class Manifest extends \Mageside\CanadaPostShipping\Model\Service\AbstractService
{
    /**
     * @var \Mageside\CanadaPostShipping\Model\ResourceModel\Shipment
     */
    private $shipmentResourceModel;

    /**
     * Manifest constructor.
     * @param \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeData
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param ArtifactFactory $artifact
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory
     * @param \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory
     * @param RatingFactory $ratingClientFactory
     * @param \Mageside\CanadaPostShipping\Model\Currency\CurrencyFactory $currencyFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Mageside\CanadaPostShipping\Model\ResourceModel\Shipment $shipmentResourceModel
     */
    public function __construct(
        \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeData,
        \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Mageside\CanadaPostShipping\Model\Service\ArtifactFactory $artifact,
        \Magento\Framework\Registry $registry,
        \Magento\Shipping\Model\Tracking\Result\ErrorFactory $trackErrorFactory,
        \Magento\Shipping\Model\Tracking\Result\StatusFactory $trackStatusFactory,
        \Mageside\CanadaPostShipping\Model\Service\RatingFactory $ratingClientFactory,
        \Mageside\CanadaPostShipping\Model\Currency\CurrencyFactory $currencyFactory,
        \Psr\Log\LoggerInterface $logger,
        \Mageside\CanadaPostShipping\Model\ResourceModel\Shipment $shipmentResourceModel
    ) {
        parent::__construct(
            $carrierHelper,
            $scopeConfig,
            $localeData,
            $dateTimeFormatter,
            $productCollectionFactory,
            $artifact,
            $registry,
            $trackErrorFactory,
            $trackStatusFactory,
            $ratingClientFactory,
            $currencyFactory,
            $logger
        );
        $this->shipmentResourceModel = $shipmentResourceModel;
    }

    /**
     * @param $manifest
     * @return array
     */
    public function getManifestPrint($manifest)
    {
        $messages = '';
        $artifacts = [];
        $response = [];
        if ($manifests = $manifest->getCpManifestId()) {
            foreach (explode(',', $manifests) as $manifestId) {
                $client = $this->createSoapClient('manifest');
                $result = $client->__soapCall(
                    'GetManifestArtifactId',
                    [
                        'get-manifest-artifact-id-request' => [
                            'mailed-by'     => $this->_carrierHelper->getMailedBy(),
                            'manifest-id'   => $manifestId
                        ]
                    ],
                    null,
                    null
                );

                // Parse Response
                if (isset($result->{'manifest'})) {
                    $artifacts[] = [
                        'artifact_id' => $result->{'manifest'}->{'artifact-id'},
                        'page_index' => 0
                    ];
                } else {
                    foreach ($result->{'messages'}->{'message'} as $message) {
                        $messages .= $message->description . '; ';
                    }
                }
            }
        }

        if (!empty($artifacts)) {
            $response['manifest'] = $this->_artifactService->create()->getArtifacts($artifacts);
        }

        if ($messages) {
            $response['messages'] = $messages;
        }

        return $response;
    }

    /**
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection
     * @return array
     */
    public function voidShipments(\Magento\Framework\Data\Collection\AbstractDb $collection)
    {
        $manifestId = null;
        $successIds = [];
        $orderIds = [];
        $errors = [];
        foreach ($collection as $shipment) {
            $manifestId = $shipment->getManifestId();
            $response = null;
            if ($shipment->getStatus() != Transmit::STATUS_TRANSMITTED) {
                $response = $this->cancelShipment($shipment);
            }
            if (isset($response['success'])) {
                $successIds[] = $shipment->getId();
                $orderIds[] = $shipment->getSalesOrderId();
            }
            if (isset($response['errors'])) {
                $errors = array_merge($errors, $response['errors']);
            }
        }

        if (!empty($successIds)) {
            $this->shipmentResourceModel->removeShipments($successIds);
        }

        if (!empty($orderIds)) {
            $this->shipmentResourceModel->removeTracking($orderIds);
        }

        return [
            'manifestId'    => $manifestId,
            'success'       => $successIds,
            'error'         => $errors
        ];
    }

    /**
     * @param $shipment
     * @return array
     */
    private function cancelShipment($shipment)
    {
        $errors = [];
        $client = $this->createSoapClient('shipment');
        $result = $client->__soapCall(
            'VoidShipment',
            [
                'void-shipment-request' => [
                    'mailed-by'     => $this->_carrierHelper->getMailedBy(),
                    'shipment-id'   => $shipment->getShipmentId()
                ]
            ],
            null,
            null
        );

        // Parse Response
        if (isset($result->{'void-shipment-success'}) && $result->{'void-shipment-success'} == 'true') {
            return ['success' => 'ok'];
        } else {
            foreach ($result->{'messages'}->{'message'} as $message) {
                if ($message->code == '404' && empty($message->description)) {
                    $description = __('The resource was not found.');
                } else {
                    $description = $message->description;
                }
                $errors[] = ['id' => $shipment->getId(), 'message' => $description];
            }

            return ['errors' => $errors];
        }
    }
}
