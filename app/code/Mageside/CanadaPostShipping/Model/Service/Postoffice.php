<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Model\Service;

/**
 * Deliver to Post Office option is available only with Xpresspost and Expedited Parcel Services.
 */
class Postoffice extends \Mageside\CanadaPostShipping\Model\Service\AbstractService
{
    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $_regCollectionFactory;

    /**
     * @var null|array
     */
    protected $_regions = null;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * Postoffice constructor.
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
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regCollectionFactory,
        \Magento\Framework\App\Cache\Type\Config $configCacheType
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
        $this->_configCacheType = $configCacheType;
        $this->_regCollectionFactory = $regCollectionFactory;
    }

    public function getNearestPostOffice($postcode)
    {
        $offices = [];
        $error = false;
        $messages = [];
        $regions = $this->getRegions();
        try {
            // Execute Request
            $client = $this->createSoapClient('postoffice');
            $result = $client->__soapCall('GetNearestPostOffice', array(
                'get-nearest-post-office-request' => array(
                    'maximum'	=> '6',
                    'search-data' => [
                        'postal-code' => $postcode
                    ]
                )
            ), NULL, NULL);

            // Parse Response
            if (isset($result->{'post-office-list'}) ) {
                foreach ($result->{'post-office-list'}->{'post-office'} as $postOffice) {
                    $region = $postOffice->{'address'}->{'province'};
                    $offices[] = [
                        'id'            => $postOffice->{'office-id'},
                        'name'          => $postOffice->{'name'},
                        'location'      => $postOffice->{'location'},
                        'position'      => [
                            'lat' => $postOffice->{'address'}->{'latitude'},
                            'lng' => $postOffice->{'address'}->{'longitude'},
                        ],
                        'city'          => $postOffice->{'address'}->{'city'},
                        'region'        => $region,
                        'region_id'     => isset($regions[$region]) ? $regions[$region] : '',
                        'postcode'      => $postOffice->{'address'}->{'postal-code'},
                        'address'       => $postOffice->{'address'}->{'office-address'},
                    ];
                }
            } else {
                $error = true;
                foreach ($result->{'messages'}->{'message'} as $message) {
                    $messages[] = [
                        'code'      => $message->code,
                        'message'   => $message->description
                    ];
                }
            }
        } catch (\SoapFault $exception) {
            $error = true;
            $messages[] = [
                'code'      => trim($exception->faultcode),
                'message'   => trim($exception->getMessage())
            ];
        }

        return ['offices' => $offices, 'error' => $error, 'messages' => $messages];
    }

    public function getPostOfficeDetail($officeId)
    {
        $cacheKey = 'MAGESIDE_CANADA_OFFICE_' . $officeId;
        $cache = $this->_configCacheType->load($cacheKey);
        if ($cache) {
            $result = unserialize($cache);
        } else {
            $days = [
                '1' => __('Sun'),
                '2' => __('Mon'),
                '3' => __('Tue'),
                '4' => __('Wed'),
                '5' => __('Thu'),
                '6' => __('Fri'),
                '7' => __('Sat'),
            ];

            $regions = $this->getRegions();

            $office = [];
            $error = false;
            $messages = [];
            try {
                // Execute Request
                $client = $this->createSoapClient('postoffice');
                $result = $client->__soapCall('GetPostOfficeDetail', array(
                    'get-post-office-detail-request' => array(
                        'office-id'	=> $officeId
                    )
                ), NULL, NULL);

                // Parse Response
                if (isset($result->{'post-office-detail'})) {
                    $region = $result->{'post-office-detail'}->{'address'}->{'province'};
                    $office = [
                        'id'            => $result->{'post-office-detail'}->{'office-id'},
                        'name'          => $result->{'post-office-detail'}->{'name'},
                        'location'      => $result->{'post-office-detail'}->{'location'},
                        'position'      => [
                            'lat' => $result->{'post-office-detail'}->{'address'}->{'latitude'},
                            'lng' => $result->{'post-office-detail'}->{'address'}->{'longitude'},
                        ],
                        'city'          => $result->{'post-office-detail'}->{'address'}->{'city'},
                        'region'        => $region,
                        'region_id'     => isset($regions[$region]) ? $regions[$region] : '',
                        'postcode'      => $result->{'post-office-detail'}->{'address'}->{'postal-code'},
                        'address'       => $result->{'post-office-detail'}->{'address'}->{'office-address'},
                        'operation'     => [],
                    ];
                    foreach ($result->{'post-office-detail'}->{'hours-list'} as $hoursListItem) {
                        if ($period = $hoursListItem->{'time'}) {
                            $time = [
                                'from' => isset($period[0]) ? $period[0] : '',
                                'to' => isset($period[1]) ? $period[1] : '',
                            ];
                            $office['operation'][] = ['day' => $days[$hoursListItem->{'day'}], 'time' => $time];
                        }
                    }
                    $office['region_id'] = isset($regions[$office['region']]) ? $regions[$office['region']] : '';
                } else {
                    $error = true;
                    foreach ($result->{'messages'}->{'message'} as $message) {
                        $messages[] = [
                            'code'      => $message->code,
                            'message'   => $message->description
                        ];
                    }
                }
            } catch (\SoapFault $exception) {
                $error = true;
                $messages[] = [
                    'code'      => trim($exception->faultcode),
                    'message'   => trim($exception->getMessage())
                ];
            }

            $result = ['office' => $office, 'error' => $error, 'messages' => $messages];
            $this->_configCacheType->save(serialize($result), $cacheKey);
        }

        return $result;
    }

    public function getRegions()
    {
        $regions = [];
        if ($this->_regions == null) {
            $collection = $this->_regCollectionFactory->create();
            $collection->addCountryFilter('CA')->load();
            foreach ($collection as $region) {
                $regions[$region->getCode()] = $region->getRegionId();
            }
            $this->_regions = $regions;
        }

        return $this->_regions;
    }
}
