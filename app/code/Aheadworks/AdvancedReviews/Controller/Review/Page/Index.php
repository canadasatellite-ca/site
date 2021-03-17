<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Review\Page;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\HTTP\Header as HttpHeader;
use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Http\UserAgent\Validator as UserAgentValidator;

/**
 * Class Index
 *
 * @package Aheadworks\AdvancedReviews\Controller\Review\Page
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var HttpHeader
     */
    private $httpHeader;

    /**
     * @var UserAgentValidator
     */
    private $userAgentValidator;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Config $config
     * @param HttpHeader $httpHeader
     * @param UserAgentValidator $userAgentValidator
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Config $config,
        HttpHeader $httpHeader,
        UserAgentValidator $userAgentValidator
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->resultPageFactory = $resultPageFactory;
        $this->httpHeader = $httpHeader;
        $this->userAgentValidator = $userAgentValidator;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();

        if ($this->userAgentValidator->isBot($this->httpHeader->getHttpUserAgent())) {
            $resultPage->addHandle('aw_advanced_reviews_static_review_page');
        }

        $pageConfig = $resultPage->getConfig();
        $pageConfig->getTitle()->set(__('All Customer Reviews'));
        $metaDescription = $this->config->getMetaDescriptionForAllReviewsPage();
        if ($metaDescription) {
            $pageConfig->setDescription($metaDescription);
        }

        return $resultPage;
    }
}
