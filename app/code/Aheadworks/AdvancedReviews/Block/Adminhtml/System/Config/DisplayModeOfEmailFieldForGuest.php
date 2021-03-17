<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field as ConfigFormField;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReviews\Model\Config;

/**
 * Class DisplayModeOfEmailFieldForGuest
 *
 * @package Aheadworks\AdvancedReviews\Block\Adminhtml\System\Config
 */
class DisplayModeOfEmailFieldForGuest extends ConfigFormField
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        if ($this->config->isAllowGuestSubmitReview()) {
            return parent::render($element);
        } else {
            return '';
        }
    }
}
