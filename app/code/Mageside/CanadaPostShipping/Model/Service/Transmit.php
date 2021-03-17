<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Service;

/**
 * Class Transmit
 * @package Mageside\CanadaPostShipping\Model\Service
 * @documentation https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/shippingmanifest/soap/transmitshipments.jsf
 */
class Transmit extends \Mageside\CanadaPostShipping\Model\Service\AbstractService
{
    const STATUS_TRANSMITTED = 'transmitted';
    const STATUS_TRANSMITTED_OFFLINE = 'offline';
    const STATUS_PENDING = 'pending';

    /**
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection
     * @return array
     */
    public function transmitShipments(\Magento\Framework\Data\Collection\AbstractDb $collection)
    {
        $successIds = [];
        $errors = [];
        foreach ($collection as $manifest) {
            $response = null;
            if ($manifest->getStatus() == self::STATUS_PENDING) {
                $response = $this->getTransmitResponse($manifest);
            }
            if (isset($response['success'])) {
                $successIds[] = $manifest->getId();
            }
            if (isset($response['errors'])) {
                $errors = array_merge($errors, $response['errors']);
            }
        }

        return [
            'success'   => $successIds,
            'error'     => $errors
        ];
    }

    /**
     * @param $manifest
     * @return array|string
     */
    private function getTransmitResponse($manifest)
    {
        $errors = [];
        $client = $this->createSoapClient('transmit');
        $result = $client->__soapCall(
            'TransmitShipments',
            $this->prepareRequest($manifest),
            null,
            null
        );

        // Parse Response
        if (isset($result->{'manifests'})) {
            $manifestIds = [];
            foreach ($result->{'manifests'}->{'manifest-id'} as $manifestId) {
                $manifestIds[] = $manifestId;
            }

            /** @var \Mageside\CanadaPostShipping\Model\Manifest $manifest */
            $manifest->addData([
                'cp_manifest_id'    => implode(',', $manifestIds),
                'status'            => self::STATUS_TRANSMITTED,
                'updated_at'        => date('Y-m-d H:i:s')
            ])->save();

            return ['success' => 'ok'];
        } else {
            foreach ($result->{'messages'}->{'message'} as $message) {
                $errors[] = ['id' => $manifest->getId(), 'message' => $message->description];
            }

            return ['errors' => $errors];
        }
    }

    /**
     * @param $manifest
     * @return array
     */
    private function prepareRequest($manifest)
    {
        $storeInfo = $this->_carrierHelper->getStoreConfig($manifest->getStoreId());

        $request = [
            'transmit-shipments-request' => [
                'mailed-by' => $this->_carrierHelper->getMailedBy(),
                'transmit-set' => [
                    'group-ids' => [
                        'group-id' => $manifest->getGroupId(),
                    ],
                    'requested-shipping-point' => $this->formatPostCode($storeInfo->getShipperAddressPostalCode()),
                    'detailed-manifests' => true,
                    'method-of-payment' => $this->_carrierHelper->getConfigCarrier('has_default_credit_card') ? 'CreditCard' : 'Account',
                    'manifest-address' => [
                        'manifest-company' => $storeInfo->getShipperContactCompanyName(),
                        'phone-number' => $storeInfo->getShipperContactPhoneNumber(),
                        'address-details' => [
                            'address-line-1' => $storeInfo->getShipperAddressStreet(),
                            'address-line-2' => $storeInfo->getShipperAddressStreet2() ?
                                $storeInfo->getShipperAddressStreet2() :
                                '',
                            'city' => $storeInfo->getShipperAddressCity(),
                            'prov-state' => $storeInfo->getShipperAddressStateOrProvinceCode(),
                            'postal-zip-code' => $this->formatPostCode($storeInfo->getShipperAddressPostalCode()),
                        ],
                    ],
                ]
            ]
        ];

        return $request;
    }

    /**
     * @param \Magento\Framework\Data\Collection\AbstractDb $collection
     * @return array
     */
    public function transmitShipmentsOffline(\Magento\Framework\Data\Collection\AbstractDb $collection)
    {
        $successIds = [];
        $errors = [];
        foreach ($collection as $manifest) {
            if ($manifest->getStatus() != self::STATUS_TRANSMITTED) {
                /** @var \Mageside\CanadaPostShipping\Model\Manifest $manifest */
                $manifest->addData([
                    'status'            => self::STATUS_TRANSMITTED_OFFLINE,
                    'updated_at'        => date('Y-m-d H:i:s')
                ])->save();

                $successIds[] = $manifest->getId();
            }
        }

        return [
            'success'   => $successIds,
            'error'     => $errors
        ];
    }
}
