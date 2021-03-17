<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Review\Form;

use Aheadworks\AdvancedReviews\Model\Config;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Form\Element\DataType\Media;

/**
 * Class Attachments
 * @package Aheadworks\AdvancedReviews\Ui\Component\Review\Form
 */
class Attachments extends Media
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param ContextInterface $context
     * @param Config $config
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Config $config,
        $components = [],
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getData('config');
        $config['maxFileSize'] = $this->config->getMaxUploadFileSize();
        $config['allowedExtensions'] = $this->config->getAllowFileExtensions();
        $this->setData('config', $config);
        parent::prepare();
    }
}
