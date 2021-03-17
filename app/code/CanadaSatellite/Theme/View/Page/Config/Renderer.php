<?php

namespace CanadaSatellite\Theme\View\Page\Config;

use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Config\Renderer as MagentoRenderer;
use Magento\Framework\View\Asset\GroupedCollection;

class Renderer extends MagentoRenderer
{
    protected $_helper;

    public function __construct(
        Config $pageConfig,
        \Magento\Framework\View\Asset\MergeService $assetMergeService,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Psr\Log\LoggerInterface $logger,
        \CanadaSatellite\Theme\Helper\MobileDetect $helper)
    {

        $this->_helper = $helper;
        parent::__construct(
            $pageConfig,
            $assetMergeService,
            $urlBuilder,
            $escaper,
            $string,
            $logger);
    }

    protected function renderAssetHtml(\Magento\Framework\View\Asset\PropertyGroup $group)
    {
        $isMobile = $this->_helper->isMobile();
        $isTablet = $this->_helper->isTablet();
        $assets = $this->processMerge($group->getAll(), $group);
        $attributes = $this->getGroupAttributes($group);

        $result = '';
        try {
            /** @var $asset \Magento\Framework\View\Asset\AssetInterface */
            foreach ($assets as $asset) {
                if ($isMobile && !$isTablet && method_exists($asset, 'getFilePath') && $asset->getFilePath()=='css/styles-l.css'){
                    continue;
                }
                $template = $this->getAssetTemplate(
                    $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE),
                    $this->addDefaultAttributes($this->getAssetContentType($asset), $attributes)
                );
                $result .= sprintf($template, $asset->getUrl());
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e);
            $result .= sprintf($template, $this->urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']));
        }
        return $result;
    }

    /**
     * @param string $contentType
     * @param string $attributes
     * @return string
     */
    protected function addDefaultAttributes($contentType, $attributes)
    {
        switch ($contentType) {
            case 'js':
                $attributes = 'type="text/javascript"' . $attributes;
                break;

            case 'css':
                if (strpos($attributes, 'media') === false) {
                    $attributes .= ' media="all"';
                }
                $attributes = ' rel="stylesheet" type="text/css" ' . $attributes;
                break;
        }
        return $attributes;
    }
}
