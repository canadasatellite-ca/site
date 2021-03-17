<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller\Review;

use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Controller\AbstractPostAction as BasePostAction;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Aheadworks\AdvancedReviews\Model\Captcha\Factory as CaptchaFactory;

/**
 * Class AbstractPostAction
 * @package Aheadworks\AdvancedReviews\Controller\Review
 */
abstract class AbstractPostAction extends BasePostAction
{
    /**
     * @var CaptchaFactory
     */
    protected $captchaFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Context $context
     * @param FormKeyValidator $formKeyValidator
     * @param CaptchaFactory $captchaFactory
     * @param Config $config
     */
    public function __construct(
        Context $context,
        FormKeyValidator $formKeyValidator,
        CaptchaFactory $captchaFactory,
        Config $config
    ) {
        parent::__construct($context, $formKeyValidator);
        $this->captchaFactory = $captchaFactory;
        $this->config = $config;
    }

    /**
     * Validate form
     *
     * @param string $formId
     * @throws LocalizedException
     */
    protected function validate($formId = '')
    {
        parent::validate();
        $captcha = $this->captchaFactory->create($formId);
        if ($captcha && $captcha->isEnabled() && !$captcha->isValid()) {
            throw new LocalizedException(__('Incorrect captcha.'));
        }
    }

    /**
     * Retrieve success message
     *
     * @return \Magento\Framework\Phrase
     */
    abstract protected function getSuccessMessage();
}
