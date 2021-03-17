<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Listing\Update;

use Magento\Amazon\Api\Data\VariantInterface;
use Magento\Amazon\Api\VariantRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class VariantsSource
 */
class VariantsSource implements OptionSourceInterface
{
    /** @var VariantRepositoryInterface $variantRepository */
    protected $variantRepository;
    /** @var Http $request */
    protected $request;

    /**
     * @param VariantRepositoryInterface $variantRepository
     * @param Http $request
     */
    public function __construct(
        VariantRepositoryInterface $variantRepository,
        Http $request
    ) {
        $this->variantRepository = $variantRepository;
        $this->request = $request;
    }

    /**
     * Get Magento product attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var array */
        $data = [];
        /** @var int */
        $parentId = $this->request->getParam('id');

        /** @var VariantInterface[] */
        $variants = $this->variantRepository->getByParentId($parentId);

        foreach ($variants as $variant) {
            $label = $variant->getVariantName();
            $label .= ' - ';
            $label .= $variant->getVariantValue();
            $label .= '  (';
            $label .= $variant->getAsin();
            $label .= ')';
            $label = __($label);
            $data[] = ['label' => $label, 'value' => $variant->getId()];
        }

        $data[] = ['value' => 0, 'label' => 'Manually Enter Correct ASIN'];

        return $data;
    }
}
