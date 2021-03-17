<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block\Reviews\Page;

use Aheadworks\AdvancedReviews\Model\Captcha\CaptchaAdapterInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Layout\LayoutProcessorProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Aheadworks\AdvancedReviews\Model\Captcha\Factory as CaptchaFactory;

/**
 * Class Container
 *
 * @package Aheadworks\AdvancedReviews\Block\Reviews\Page
 */
class Container extends Template implements IdentityInterface
{
    /**
     * @var LayoutProcessorProviderInterface
     */
    private $layoutProcessorProvider;

    /**
     * @var int
     */
    private $currentStoreId;
    
    /**
     * @var CaptchaFactory
     */
    private $captchaFactory;
    
    /**
     * @var Json
     */
    private $serializer;

    /**
     * @param Context $context
     * @param LayoutProcessorProviderInterface $layoutProcessorProvider
     * @param StoreManagerInterface $storeManager
     * @param CaptchaFactory $captchaFactory
     * @param Json $serializer
     * @param array $data
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        Context $context,
        LayoutProcessorProviderInterface $layoutProcessorProvider,
        StoreManagerInterface $storeManager,
        CaptchaFactory $captchaFactory,
        Json $serializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layoutProcessorProvider = $layoutProcessorProvider;
        $this->serializer = $serializer;
        $this->captchaFactory = $captchaFactory;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout'])
            ? $data['jsLayout']
            : [];
        $this->currentStoreId = $storeManager->getStore(true)->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessorProvider->getLayoutProcessors() as $layoutProcessor) {
            $this->jsLayout = $layoutProcessor->process(
                $this->jsLayout,
                null,
                $this->currentStoreId
            );
        }

        return \Zend_Json::encode($this->jsLayout);
    }
    
    /**
     * Retrieve serialized config
     *
     * @return string
     */
    public function getSerializedConfig()
    {
        $config = [];
        $commentCaptcha = $this->captchaFactory->create(CaptchaAdapterInterface::COMMENT_FORM_ID_BASE);
        if ($commentCaptcha && $commentCaptcha->isEnabled()) {
            $config = array_merge($config, $commentCaptcha->getConfigData());
        }
        return $this->serializer->serialize($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [];
        $identities = array_merge($identities, $this->getContainerIdentities());
        return $identities;
    }

    /**
     * Retrieve current container identities
     *
     * @return array
     */
    protected function getContainerIdentities()
    {
        $containerIdentities = [
            ReviewInterface::CACHE_ALL_REVIEWS_PAGE_TAG
        ];
        return $containerIdentities;
    }
}
