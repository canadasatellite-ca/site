<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Service;

/**
 * Class Tracking
 * @package Mageside\CanadaPostShipping\Model\Service
 * @documentation https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/tracking/soap/trackingsummary.jsf
 */
class Tracking extends \Mageside\CanadaPostShipping\Model\Service\AbstractService
{
    /**
     * @var
     */
    private $_result;

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Send request for tracking
     *
     * @param $tracking
     * @param $result
     * @return array
     */
    public function getTrackingData($tracking, $result)
    {
        $this->_result = $result;

        $trackRequest = [
            'get-tracking-detail-request' => [
                'pin' => $tracking
            ]
        ];

        $debugData = ['request' => $trackRequest];
        try {
            $client = $this->createSoapClient('track');
            $response = $client->__soapCall('GetTrackingDetail', $trackRequest, null, null);
            $debugData['result'] = $response;
            $debugData['to_cache'] = true;
        } catch (\Exception $e) {
            $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            $this->_logger->critical($e);
        }

        return $debugData;
    }

    /**
     * Parse tracking response
     *
     * @param string $trackingValue
     * @param \stdClass $response
     * @return void
     */
    public function parseTrackingResponse($trackingValue, $response)
    {
        if (!is_object($response)) {
            $this->appendTrackingError($trackingValue, __('Invalid response from carrier'));
            return;
        } elseif (isset($response->{'messages'})) {
            foreach ($response->{'messages'}->{'message'} as $message) {
                $this->appendTrackingError($trackingValue, (string) $message->description);
            }
            return;
        }

        if (isset($response->{'tracking-detail'}->{'significant-events'})) {
            $trackInfo = $response->{'tracking-detail'}->{'significant-events'}->occurrence;
            $result = $this->getResult();
            $carrierTitle = $this->_carrierHelper->getConfigCarrier('title');
            $tracking = $this->_trackStatusFactory->create();
            $tracking->setCarrier(\Mageside\CanadaPostShipping\Model\Carrier::CODE);
            $tracking->setCarrierTitle($carrierTitle);
            $tracking->setTracking($trackingValue);
            $tracking->addData($this->processTrackingDetails($trackInfo));
            $result->append($tracking);
        } else {
            $this->appendTrackingError(
                $trackingValue,
                __('For some reason we can\'t retrieve tracking info right now.')
            );
        }
    }

    /**
     * Append error message to rate result instance
     * @param string $trackingValue
     * @param string $errorMessage
     * @return void
     */
    private function appendTrackingError($trackingValue, $errorMessage)
    {
        $error = $this->_trackErrorFactory->create();
        $error->setCarrier(\Mageside\CanadaPostShipping\Model\Carrier::CODE);
        $error->setCarrierTitle($this->_carrierHelper->getConfigCarrier('title'));
        $error->setTracking($trackingValue);
        $error->setErrorMessage($errorMessage);
        $result = $this->getResult();
        $result->append($error);
    }

    /**
     * Parse track details response from Fedex
     * @param array $trackInfo
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
    +  @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function processTrackingDetails($trackInfo)
    {
        $result = [
            'shippeddate'       => null,
            'weight'            => null,
            'progressdetail'    => [],
            'deliverydate'      => null,
            'deliverytime'      => null,
            'deliverylocation'  => null,
            'activity'          => null,
            'signedby'          => null,
            'status'            => null,
            'service'           => null
        ];

        foreach ($trackInfo as $key => $event) {
            $date = $this->prepareDateTime($event->{'event-date'}, $event->{'event-time'}, $event->{'event-time-zone'});
            if ($event->{'event-description'} == 'Delivered') {
                $result['deliverydate'] = $this->formatDate($date);
                $result['deliverytime'] = $this->formatTime($date);
            } elseif ($key == 0) {
                $result['shipped_date'] = $this->getDeliveryDateTime($date);
                $result['status'] = $event->{'event-description'};
                $result['delivery_location'] = $this->getDeliveryAddress($event);
            }
            $result['progressdetail'][] = [
                'deliverydate'      => $this->formatDate($date),
                'deliverytime'      => $this->formatTime($date),
                'deliverylocation'  => $this->getDeliveryAddress($event),
                'activity'          => $event->{'event-description'},
            ];
        }

        return $result;
    }

    /**
     * Get delivery address details in string representation
     *
     * @param \stdClass $trackInfo
     * @return string
     */
    private function getDeliveryAddress(\stdClass $trackInfo)
    {
        return $trackInfo->{'event-site'} . ', ' . $trackInfo->{'event-province'};
    }

    /**
     * Parse delivery datetime from tracking details
     *
     * @param $date
     * @return string
     */
    private function getDeliveryDateTime($date)
    {
        return $this->formatDate($date) . ' ' . $this->formatTime($date);
    }

    /**
     * @param $date
     * @param $time
     * @param $timezone
     * @return \DateTime
     */
    private function prepareDateTime($date, $time, $timezone)
    {
        return new \DateTime("{$date} {$time}", new \DateTimeZone($timezone));
    }
}
