<?php

namespace CanadaSatellite\Theme\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;

class Attributes extends AbstractModifier
{
    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $meta = $this->addValidationToWeightAttribute($meta);

        return $meta;
    }

    private function addValidationToWeightAttribute(array $meta)
    {
        $meta['product-details']['children']['container_weight']['children']
            ['weight']['arguments']['data']['config']['validation']
            ['validate-number-range'] = '0.0001-30';
        return $meta;
    }
}
