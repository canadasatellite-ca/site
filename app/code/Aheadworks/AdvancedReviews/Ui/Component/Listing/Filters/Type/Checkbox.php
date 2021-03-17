<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Listing\Filters\Type;

use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Ui\Component\Filters\FilterModifier;
use Magento\Ui\Component\Filters\Type\AbstractFilter;
use Magento\Ui\Component\Form\Element\Input as ElementInput;

/**
 * Class Checkbox
 * @package Aheadworks\AdvancedReviews\Ui\Component\Listing\Filters\Type
 */
class Checkbox extends AbstractFilter
{
    /**
     * Filter name
     */
    const NAME = 'filter_checkbox';

    /**
     * Filter component
     */
    const COMPONENT = 'checkbox';

    /**
     * @var ElementInput
     */
    protected $wrappedComponent;

    /**
     * @var BooleanUtils
     */
    protected $booleanUtils;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param FilterBuilder $filterBuilder
     * @param FilterModifier $filterModifier
     * @param BooleanUtils $booleanUtils
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FilterBuilder $filterBuilder,
        FilterModifier $filterModifier,
        BooleanUtils $booleanUtils,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $filterBuilder, $filterModifier, $components, $data);
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->wrappedComponent = $this->uiComponentFactory->create(
            $this->getName(),
            static::COMPONENT,
            ['context' => $this->getContext()]
        );
        $this->wrappedComponent->prepare();

        $jsConfig = array_replace_recursive(
            $this->getJsConfig($this->wrappedComponent),
            $this->getJsConfig($this)
        );
        $this->setData('js_config', $jsConfig);

        $this->setData(
            'config',
            array_replace_recursive(
                (array)$this->wrappedComponent->getData('config'),
                (array)$this->getData('config')
            )
        );

        $this->applyFilter();

        parent::prepare();
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFilter()
    {
        if (isset($this->filterData[$this->getName()])) {
            $value = $this->filterData[$this->getName()];

            if ($this->booleanUtils->toBoolean($value)) {
                $filter = $this->filterBuilder->setConditionType('eq')
                    ->setField($this->getName())
                    ->setValue(1)
                    ->create();

                $this->getContext()->getDataProvider()->addFilter($filter);
            }
        }
    }
}
