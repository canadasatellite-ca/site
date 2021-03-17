<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AbstractCustomerPostAction
 *
 * @package Aheadworks\AdvancedReviews\Controller
 */
abstract class AbstractCustomerPostAction extends AbstractCustomerAction
{
    /**
     * @var FormKeyValidator
     */
    protected $formKeyValidator;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param FormKeyValidator $formKeyValidator
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        FormKeyValidator $formKeyValidator
    ) {
        parent::__construct(
            $context,
            $customerSession
        );
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
