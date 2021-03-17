<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AbstractPostAction
 *
 * @package Aheadworks\AdvancedReviews\Controller
 */
abstract class AbstractPostAction extends Action
{
    /**
     * @var FormKeyValidator
     */
    protected $formKeyValidator;

    /**
     * @param Context $context
     * @param FormKeyValidator $formKeyValidator
     */
    public function __construct(
        Context $context,
        FormKeyValidator $formKeyValidator
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * Validate form
     *
     * @throws LocalizedException
     */
    protected function validate()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            throw new LocalizedException(__('Invalid Form Key. Please refresh the page.'));
        }
    }
}
