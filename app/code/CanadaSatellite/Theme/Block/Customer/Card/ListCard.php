<?php

namespace CanadaSatellite\Theme\Block\Customer\Card;

class ListCard extends \Magento\Customer\Block\Account\Dashboard
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
    public function __construct(
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


    public function getCustomerCards()
    {
        $customerId = $this->getCustomer()->getCustomerId();
        $result = array();

        if (!empty($customerId)) {
            $result = $this->_collectionFactory->create()->addCustomerFilter($customerId);
        }

        return $result;
    }

    public function getInfoHtml($card)
    {
        $cardholderName = $card->getCardholderName();
        $cardType = $card->getCardTypeLabel();
        $result = "$cardType<br/>$cardholderName<br/>";

        return $result;
    }

    public function getCustomer()
    {
        return $this->_currentCustomer;
    }


    public function getBackUrl()
    {
        return $this->getUrl('casat/customer/viewsim');
    }
    
    public function getAddCardUrl()
    {
        return $this->getUrl('casat/customer/card_add');
    }

    public function getEditCardUrl($card)
    {
        return $this->getUrl('casat/customer/card_edit') . 'id/' . $card->getId();
    }

    public function getDeleteCardUrl()
    {
        return $this->getUrl('casat/customer/card_delete');
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
}
