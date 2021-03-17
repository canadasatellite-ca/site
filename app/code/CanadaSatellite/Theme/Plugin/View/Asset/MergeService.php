<?php

namespace CanadaSatellite\Theme\Plugin\View\Asset;

use Magento\Framework\View\Asset\MergeService as MagentoMergeService;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Asset\ConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\State;
use CanadaSatellite\Theme\Helper\MobileDetect;

class MergeService
{
    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Config
     *
     * @var ConfigInterface
     */
    protected $config;

    /**
     * Filesystem
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * State
     *
     * @var State
     */
    protected $state;

    /**
     * @var MobileDetect
     */
    protected $_helper;

    /**
     * MergeService constructor.
     * @param ObjectManagerInterface $objectManager
     * @param ConfigInterface $config
     * @param Filesystem $filesystem
     * @param State $state
     * @param MobileDetect $helper
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ConfigInterface $config,
        Filesystem $filesystem,
        State $state,
        MobileDetect $helper
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->state = $state;
        $this->_helper = $helper;
    }

    public function aroundGetMergedAssets(MagentoMergeService $subject, callable $proceed, array $assets, $contentType)
    {
        $isCss = $contentType == 'css';
        $isJs = $contentType == 'js';

        if ($contentType == 'woff2') {
            return $assets;
        }

        if (!$isCss && !$isJs) {
            throw new \InvalidArgumentException("Merge for content type '{$contentType}' is not supported.");
        }

        $isCssMergeEnabled = $this->config->isMergeCssFiles();
        $isJsMergeEnabled = $this->config->isMergeJsFiles();
        if (($isCss && $isCssMergeEnabled) || ($isJs && $isJsMergeEnabled)) {
            $mergeStrategyClass = \Magento\Framework\View\Asset\MergeStrategy\FileExists::class;

            if ($this->state->getMode() === \Magento\Framework\App\State::MODE_DEVELOPER) {
                $mergeStrategyClass = \Magento\Framework\View\Asset\MergeStrategy\Checksum::class;
            }

            $mergeStrategy = $this->objectManager->get($mergeStrategyClass);

            $isMobile = $this->_helper->isMobile();
            $isTablet = $this->_helper->isTablet();
            if ($isMobile && !$isTablet && array_key_exists('css/styles-l.css', $assets)) {
                unset($assets['css/styles-l.css']);
            }

            $assets = $this->objectManager->create(
                \Magento\Framework\View\Asset\Merged::class,
                ['assets' => $assets, 'mergeStrategy' => $mergeStrategy]
            );
        }

        return $assets;
    }
}
