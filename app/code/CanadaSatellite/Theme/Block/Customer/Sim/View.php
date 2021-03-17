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
    public function __construct(
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
    public function getSimData()
    {
        return $this->_coreRegistry->registry('current_sim');
    }

    /**
     * Return sim url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('casat/customer/viewsim');
    }

    public function getCardsUrl()
    {
        return $this->getUrl('casat/customer/card_listing');
    }

    public function getAutoRechargeUrl($sim)
    {
        return $this->getUrl('casat/customer/simrecharge') . 'id/' . $sim->getId();
    }

    public function updateNickname($sim)
    {
        return $this->getUrl('casat/customer/updatenickname') . 'id/' . $sim->getId();
    }

    public function getNoRechargeUrl($sim)
    {
        return $this->getUrl('casat/customer/simnorecharge') . 'id/' . $sim->getId();
    }

    /**
     * Get formatted date
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::LONG);
    }

    /**
     * Get formatted time
     *
     * @param string $date
     * @return string
     */
    public function timeFormat($date)
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
