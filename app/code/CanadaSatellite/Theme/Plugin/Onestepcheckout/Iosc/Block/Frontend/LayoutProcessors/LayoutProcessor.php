<?php

namespace CanadaSatellite\Theme\Plugin\Onestepcheckout\Iosc\Block\Frontend\LayoutProcessors;

class LayoutProcessor
{
    private $arrayManager;

    function __construct(
        \Magento\Framework\Stdlib\ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
    }

    function afterProcess(
        \Onestepcheckout\Iosc\Block\Frontend\LayoutProcessors\LayoutProcessor $subject,
        $jsLayout
    ) {
        $streetPaths = $this->arrayManager->findPaths('street', $jsLayout);
        foreach ($streetPaths as $streetPath) {
            $jsLayout = $this->arrayManager->set($streetPath . '/label', $jsLayout, __('Address'));
            $jsLayout = $this->arrayManager->remove($streetPath . '/children/0/label', $jsLayout);
            $jsLayout = $this->arrayManager->remove($streetPath . '/children/1/label', $jsLayout);
            $jsLayout = $this->arrayManager->remove($streetPath . '/children/2/label', $jsLayout);

        }

        return $jsLayout;
    }
}
