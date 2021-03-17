<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Service;

use Magento\Framework\App\Config\ReinitableConfigInterface;

/**
 * Class Registration
 * @package Mageside\CanadaPostShipping\Model\Service
 * @documentation https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/ecomplatforms/soap/registrationtoken.jsf
 * @documentation https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/ecomplatforms/soap/registrationinfo.jsf
 */
class Registration extends \Mageside\CanadaPostShipping\Model\Service\AbstractService
{
    /**
     * @var \Magento\Config\Model\ConfigFactory
     */
    private $configFactory;

    /**
     * @var array
     */
    private $configsToSave = [
        'customer_number'           => 'customer-number',
        'contract_id'               => 'contract-number',
        'username'                  => 'merchant-username',
        'password'                  => 'merchant-password',
        'has_default_credit_card'   => 'has-default-credit-card'
    ];

    /**
     * Registration constructor.
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
     * @param \Magento\Config\Model\Config $config
     * @param ReinitableConfigInterface $appConfig
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
        \Magento\Config\Model\ConfigFactory $configFactory
    ) {
        $this->configFactory = $configFactory;
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
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getToken()
    {
        $logRecord = ['call' => 'GetMerchantRegistrationToken', 'request' => ' '];
        try {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ]);

            $proxy = new \SoapClient(
                $this->_carrierHelper->getConfigCarrier('registration_token_url'),
                ['stream_context' => $context]
            );
            $response = $proxy->magesideCanadaPostRegistrationGetTokenV1Execute();
            $result = json_decode($response->result, true);

            if ($result['error']) {
                $logRecord['response'] = var_export((array) $result, true);
                $logRecord['status'] = 'warning';
                $this->_carrierHelper->saveRequestLogRecord($logRecord);
            }

            return ['tokenId' => $result['tokenId']];
        } catch (\Exception $e) {
            $logRecord['response'] = trim($e->getMessage());
            $logRecord['status'] = 'exception';
            $this->_carrierHelper->saveRequestLogRecord($logRecord);

            return ['tokenId' => null];
        }
    }

    /**
     * @param $tokenId
     * @return array
     * @throws \Exception
     */
    public function getMerchantInfo($tokenId)
    {
        $logRecord = ['call' => 'GetMerchantRegistrationInfo', 'request' => ' '];
        try {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                ]
            ]);

            $proxy = new \SoapClient(
                $this->_carrierHelper->getConfigCarrier('registration_merchant_info_url'),
                ['stream_context' => $context]
            );
            $response = $proxy->magesideCanadaPostRegistrationGetMerchantInfoV1Execute(['tokenId' => $tokenId]);
            $result = json_decode($response->result, true);

            if ($result['error']) {
                $logRecord['response'] = var_export((array) $result, true);
                $logRecord['status'] = 'warning';
                $this->_carrierHelper->saveRequestLogRecord($logRecord);
            }

            return ['merchant' => $result['merchant'], 'error' => $result['error'], 'messages' => $result['messages']];
        } catch (\Exception $e) {
            $logRecord['response'] = trim($e->getMessage());
            $logRecord['status'] = 'exception';
            $this->_carrierHelper->saveRequestLogRecord($logRecord);

            return ['merchant' => null, 'error' => true, 'messages' => ['messages' => trim($e->getMessage())]];
        }
    }

    /**
     * @param $merchant
     * @param $website
     * @return $this
     * @throws \Exception
     */
    public function saveMerchantInfo($merchant, $website)
    {
        $fields = [];
        foreach ($this->configsToSave as $key => $value) {
            $fields[$key] = ['value' => !empty($merchant[$key]) ? $merchant[$key] : ''];
        }

        $configData = [
            'section'   => 'carriers',
            'website'   => $website,
            'store'     => '',
            'groups'    => [
                'canadapost' => [
                    'fields' => $fields
                ]
            ],
        ];
        /** @var \Magento\Config\Model\Config $configModel  */
        $configModel = $this->configFactory->create(['data' => $configData]);
        $configModel->save();

        return $this;
    }
}
