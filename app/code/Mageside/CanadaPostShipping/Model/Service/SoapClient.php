<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Service;

use Magento\Framework\Module\Dir;

class SoapClient extends \SoapClient
{
    /**
     * @var \Mageside\CanadaPostShipping\Helper\Carrier
     */
    private $carrierHelper;

    /**
     * Paths array to wsdl files
     *
     * @var array
     */
    private $serviceWsdl = [
        'shipment'      => '/wsdl/shipment.wsdl',
        'ncshipment'    => '/wsdl/ncshipment.wsdl',
        'rating'        => '/wsdl/rating.wsdl',
        'artifact'      => '/wsdl/artifact.wsdl',
        'track'         => '/wsdl/track.wsdl',
        'transmit'      => '/wsdl/manifest.wsdl',
        'manifest'      => '/wsdl/manifest.wsdl',
        'registration'  => '/wsdl/merchantregistration.wsdl',
        'postoffice'    => '/wsdl/postoffice.wsdl',
    ];

    /**
     * @var array
     */
    private $sensitiveData = ['customer-number', 'merchant-username', 'merchant-password'];

    /**
     * Path to certificate file
     *
     * @var string
     */
    private $certificate = '/cert/cacert.pem';

    /**
     * SoapClient constructor.
     *
     * @param \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper
     * @param string $service
     */
    public function __construct(
        \Mageside\CanadaPostShipping\Helper\Carrier $carrierHelper,
        $service
    ) {
        $this->carrierHelper = $carrierHelper;
        if (!$this->carrierHelper->isContractShipment() && $service == 'shipment') {
            $service = 'ncshipment';
        }
        parent::__construct($this->getWsdl($service), $this->getOptions($service));
        $this->setHeader($service);
    }

    /**
     * Need to override SoapClient because the abstract element 'groupIdOrTransmitShipment'
     * is expected to be in the request in order for validation to pass.
     * So, we give it what it expects, but in __doRequest we modify the request by removing the abstract element
     * and add the correct element.
     *
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int $version
     * @param null $one_way
     * @return string
     */
    public function __doRequest($request, $location, $action, $version, $one_way = null) {
        $dom = new \DOMDocument('1.0');
        $dom->loadXML($request);

        //get element name and values of group-id or transmit-shipment.
        $groupIdOrTransmitShipment =  $dom->getElementsByTagName("groupIdOrTransmitShipment")->item(0);
        if ($groupIdOrTransmitShipment) {
            $element = $groupIdOrTransmitShipment->firstChild->firstChild->nodeValue;
            $value = $groupIdOrTransmitShipment->firstChild->firstChild->nextSibling->firstChild->nodeValue;

            //remove bad element
            $newDom = $groupIdOrTransmitShipment->parentNode->removeChild($groupIdOrTransmitShipment);

            //append correct element with namespace
            $body =  $dom->getElementsByTagName("shipment")->item(0);
            $newElement = $dom->createElement($element, $value);
            $body->appendChild($newElement);

            //save $dom to string
            $request = $dom->saveXML();
        }

        //doRequest
        return parent::__doRequest($request, $location, $action, $version);
    }

    /**
     * @inheritdoc
     */
    public function __soapCall(
        $function_name,
        $arguments,
        $options = NULL,
        $input_headers = NULL,
        &$output_headers = NULL
    ) {
        $data = [];
        try {
            $serviceKey = array_keys($arguments);
            if (!empty($serviceKey)){
                $serviceKey = array_shift($serviceKey);
                if (!$this->carrierHelper->getConfigCarrier('sandbox_mode')) {
                    $arguments[$serviceKey] = array_merge([
                        'locale'        => $this->carrierHelper->getConfigCarrier('locale'),
                        'platform-id'   => $this->carrierHelper->getConfigCarrier('platform_id')
                    ], $arguments[$serviceKey]);
                } else {
                    $arguments[$serviceKey] = array_merge([
                        'locale'        => $this->carrierHelper->getConfigCarrier('locale'),
                    ], $arguments[$serviceKey]);
                }
            }

            $data['call'] = $function_name;
            $data['request'] = var_export($arguments, true);

            $result = parent::__soapCall(
                $function_name,
                $arguments,
                $options,
                $input_headers,
                $output_headers
            );

            if (is_object($result)) {
                $responseArray = json_decode(json_encode($result),true);

                /**
                 * hiding sensitive data
                 */
                $responseKey = array_keys($responseArray);
                if (!empty($responseKey)){
                    $responseKey = array_shift($responseKey);
                    foreach ($responseArray[$responseKey] as $key => $value) {
                        if (in_array($key, $this->sensitiveData)) {
                            $responseArray[$responseKey][$key] = '***';
                        }
                    }
                }

                $data['response'] = var_export($responseArray, true);
            } else {
                $data['response'] = var_export((array) $result, true);
            }
            $data['status'] = isset($result->{'messages'}->{'message'}) ? 'warning' : 'success';
            $this->carrierHelper->saveRequestLogRecord($data);

            return $result;
        } catch (\SoapFault $e) {
            $data['response'] = trim($e->faultcode) . ': ' . trim($e->getMessage());
            $data['status'] = 'exception';
            $this->carrierHelper->saveRequestLogRecord($data);

            throw $e;
        }
    }

    /**
     * Get path to wsdl file
     *
     * @param string $service
     * @return string
     */
    private function getWsdl($service)
    {
        return $this->carrierHelper->getModuleDir(Dir::MODULE_ETC_DIR) . $this->serviceWsdl[$service];
    }

    /**
     * Get path to certificate file
     *
     * @return string
     */
    private function getCertificate()
    {
        $certificate = trim($this->carrierHelper->getConfigCarrier('certificate_path'));
        if (!$certificate) {
            $certificate = $this->carrierHelper->getModuleDir(Dir::MODULE_ETC_DIR) . $this->certificate;
        }

        return $certificate;
    }

    /**
     * Get SoapClient options
     *
     * @param string $service
     * @return array
     */
    private function getOptions($service)
    {
        // Notice: for tracking always using production environment
        $location = ($this->carrierHelper->getConfigCarrier('sandbox_mode') && $service != 'track') ?
            $this->carrierHelper->getConfigCarrier($service . '_development_endpoint_url') :
            $this->carrierHelper->getConfigCarrier($service . '_production_endpoint_url');
        $hostName = explode('/', $location)[2];

        $opts = [
            'ssl' => [
                'verify_peer'   => true,
                'cafile'        => $this->getCertificate(),
                'peer_name'     => $hostName
            ],
            'http' => ['protocol_version' => 1.0]
        ];

        $ctx = stream_context_create($opts);

        return [
            'location'          => $location,
            'features'          => SOAP_SINGLE_ELEMENT_ARRAYS,
            'stream_context'    => $ctx,
            'trace'             => true
        ];
    }

    /**
     * Create soap client with selected wsdl
     *
     * @param $service
     * @return $this
     */
    private function setHeader($service)
    {
        // Notice: for tracking always using production environment
        if ($service == 'registration') {
            $username = $this->carrierHelper->getConfigCarrier('platform_username');
            $password = $this->carrierHelper->getConfigCarrier('platform_password');
        } elseif ($this->carrierHelper->getConfigCarrier('sandbox_mode') && $service != 'track') {
            $username = $this->carrierHelper->getConfigCarrier('username_development');
            $password = $this->carrierHelper->getConfigCarrier('password_development');
        } else {
            $username = $this->carrierHelper->getConfigCarrier('username');
            $password = $this->carrierHelper->getConfigCarrier('password');
        }

        $WSSENS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $usernameToken = new \stdClass();
        $usernameToken->Username = new \SoapVar(
            $username,
            XSD_STRING,
            null,
            null,
            null,
            $WSSENS
        );
        $usernameToken->Password = new \SoapVar(
            $password,
            XSD_STRING,
            null,
            null,
            null,
            $WSSENS
        );
        $content = new \stdClass();
        $content->UsernameToken = new \SoapVar(
            $usernameToken,
            SOAP_ENC_OBJECT,
            null,
            null,
            null,
            $WSSENS
        );
        $header = new \SOAPHeader($WSSENS, 'Security', $content);
        $this->__setSoapHeaders($header);

        return $this;
    }
}
