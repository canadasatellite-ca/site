<?php

namespace CanadaSatellite\Theme\Plugin\Framework;

use Magento\Framework\View\Element\AbstractBlock as MagentoAbstractBlock;
use CanadaSatellite\Theme\Helper\MobileDetect;
use Magento\Store\Model\StoreManagerInterface;

class AbstractBlock {

    /**
     * @var MobileDetect
     */
    protected $_helper;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * AbstractBlock constructor.
     * @param MobileDetect $mobileDetect
     * @param StoreManagerInterface $storeManager
     */
    function __construct(
        MobileDetect $mobileDetect,
        StoreManagerInterface $storeManager
)
    {
        $this->_helper = $mobileDetect;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\View\Element\AbstractBlock $subject
     * @param callable $proceed
     * @return array
     */
    function aroundGetCacheKeyInfo(MagentoAbstractBlock $subject, callable $proceed)
    {
        $result = $proceed();
        $result[] = $this->_helper->getDeviceParam();
        $result[] = $this->_storeManager->getStore()->getId();
        return $result;
    }
}