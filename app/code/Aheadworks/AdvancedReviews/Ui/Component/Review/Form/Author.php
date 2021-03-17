<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Review\Form;

use Magento\Ui\Component\Form\Field;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver;

/**
 * Class Author
 * @package Aheadworks\AdvancedReviews\Ui\Component\Review\Form
 */
class Author extends Field
{
    /**
     * @var Resolver
     */
    protected $authorResolver;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Resolver $authorResolver
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Resolver $authorResolver,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->authorResolver = $authorResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        if (!empty($dataSource['data'])) {
            $data = $dataSource['data'];
            if (isset($data[ReviewInterface::AUTHOR_TYPE])) {
                $authorType = $data[ReviewInterface::AUTHOR_TYPE];
                $customerId = isset($data[ReviewInterface::CUSTOMER_ID]) ? $data[ReviewInterface::CUSTOMER_ID] : null;
                $data[$fieldName] = $this->authorResolver->getBackendLabel($authorType, $customerId);
                if ($url = $this->authorResolver->getBackendUrl($authorType, $customerId)) {
                    $data[$fieldName . '_url'] = $url;
                }
            }
            $dataSource['data'] = $data;
        }
        return $dataSource;
    }
}
