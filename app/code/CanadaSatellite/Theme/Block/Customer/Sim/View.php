<?php
namespace CanadaSatellite\Theme\Block\Customer\Sim;

use CanadaSatellite\Theme\Model\Sim;

/**
 * Sim detailed view block
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Class view constructor
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Retrieve current Sim model instance
     *
     * @return \CanadaSatellite\Theme\Model\Sim
     */
    function getSimData()
    {
        return $this->_coreRegistry->registry('current_sim');
    }

    /**
     * Return sim url
     *
     * @return string
     */
    function getBackUrl()
    {
        return $this->getUrl('casat/customer/viewsim');
    }

    function getCardsUrl()
    {
        return $this->getUrl('casat/customer/card_listing');
    }

    function getAutoRechargeUrl($sim)
    {
        return $this->getUrl('casat/customer/simrecharge') . 'id/' . $sim->getId();
    }

    function updateNickname($sim)
    {
        return $this->getUrl('casat/customer/updatenickname') . 'id/' . $sim->getId();
    }

    function getNoRechargeUrl($sim)
    {
        return $this->getUrl('casat/customer/simnorecharge') . 'id/' . $sim->getId();
    }

    /**
     * Get formatted date
     *
     * @param string $date
     * @return string
     */
    function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::LONG);
    }

    /**
     * Get formatted time
     *
     * @param string $date
     * @return string
     */
    function timeFormat($date)
    {
        return $this->formatTime($date);
    }

//    /**
//     * Block to HTML
//     *
//     * @return string
//     */
//    protected function _toHtml()
//    {
//        return $this->_currentCustomer->getCustomerId() ? parent::_toHtml() : '';
//    }
}
