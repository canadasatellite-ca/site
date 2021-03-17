<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Block\Plugin;

use Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Front;
use Magento\CatalogSearch\Model\Source\Weight;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\Element\Fieldset;

class FrontTabPlugin
{
    /**
     * @var Weight
     */
    private $weightSource;

    /**
     * @param Weight $weightSource
     */
    public function __construct(Weight $weightSource)
    {
        $this->weightSource = $weightSource;
    }

    /**
     * @param Front $subject
     * @param callable $proceed
     * @param Form $form
     * @return Front
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSetForm(Front $subject, \Closure $proceed, Form $form)
    {
        $block = $proceed($form);
        /** @var Fieldset $fieldset */
        $fieldset = $form->getElement('front_fieldset');
        $fieldset->addField(
            'specifications_position',
            'text',
            [
                'name' => 'specifications_position',
                'label' => __('Specification Position'),
            ],
            'is_visible_on_front'
        );

        $fieldset->addField(
            'compare_position',
            'text',
            [
                'name' => 'compare_position',
                'label' => __('Compare Position'),
            ],
            'is_comparable'
        );

        $subject->getChildBlock('form_after')
            ->addFieldMap(
                'specifications_position',
                'specifications_position'
            )
            ->addFieldMap(
                'is_visible_on_front',
                'is_visible_on_front'
            )
            ->addFieldMap(
                'compare_position',
                'compare_position'
            )
            ->addFieldMap(
                'is_comparable',
                'is_comparable'
            )
            ->addFieldDependence(
                'compare_position',
                'is_comparable',
                '1'
            );
        return $block;
    }
}
