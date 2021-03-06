<?php

namespace CanadaSatellite\Theme\Block\Customer\Card;

class AddCard extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * Card collection
     *
     * @var \CanadaSatellite\Theme\Model\ResourceModel\Card\Collection
     */
    protected $_collection;

    /**
     * Card resource model
     *
     * @var \CanadaSatellite\Theme\Model\ResourceModel\Card\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Current Customer
     *
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param \CanadaSatellite\Theme\Model\ResourceModel\Sim\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \CanadaSatellite\DynamicsIntegration\Rest\RestApi $restApi
     * @param array $data
     */
    function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \CanadaSatellite\Theme\Model\ResourceModel\Card\CollectionFactory $collectionFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_currentCustomer = $currentCustomer;

        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
    }

    /**
     * @return return available cc type
     */
    function getCcAvailableTypes()
    {
        return array(
            '100000000' => 'Visa',
            '100000001' => 'Master Card',
            '100000002' => 'American Express',
            '100000003' => 'JCB',
            '100000004' => 'Diners Club',
        );
    }

    /**
     * @return cc months
     */
    function getCcMonths()
    {
        return array(
            '100000000' => '01 - January',
            '100000001' => '02 - February',
            '100000002' => '03 - March',
            '100000003' => '04 - April',
            '100000004' => '05 - May',
            '100000005' => '06 - June',
            '100000006' => '07 - July',
            '100000007' => '08 - August',
            '100000008' => '09 - September',
            '100000009' => '10 - October',
            '100000010' => '11 - November',
            '100000011' => '12 - December',
        );
    }

    /**
     * @return cc Years
     */
    function getCcYears()
    {
        return array(
            //'100000000' => '2014',
            //'100000001' => '2015',
            //'100000002' => '2016',
            //'100000003' => '2017',
            //'100000004' => '2018',
            '100000005' => '2019',
            '100000006' => '2020',
            '100000007' => '2021',
            '100000008' => '2022',
            '100000009' => '2023',
            '100000010' => '2024',
            '100000011' => '2025',
        );
    }

    function getBackUrl()
    {
        return $this->getUrl('casat/customer/card_listing');
    }
    
    function getSaveUrl()
    {
        return $this->getUrl('casat/customer/card_save');
    }

    function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}
