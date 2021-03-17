<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Account\Listing\Update;

use Magento\Amazon\Api\Data\MultipleInterface;
use Magento\Amazon\Api\MultipleRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class MultipleSource
 */
class MultipleSource implements OptionSourceInterface
{
    /** @var MultipleRepositoryInterface $multipleRepository */
    protected $multipleRepository;
    /** @var Http $request */
    protected $request;

    /**
     * @param MultipleRepositoryInterface $multipleRepository
     * @param Http $request
     */
    public function __construct(
        MultipleRepositoryInterface $multipleRepository,
        Http $request
    ) {
        $this->multipleRepository = $multipleRepository;
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

        /** @var MultipleInterface[] */
        $multiples = $this->multipleRepository->getByParentId($parentId);

        foreach ($multiples as $multiple) {
            $label = __('ASIN: ' . $multiple->getAsin() . ' (' . $multiple->getTitle() . ')');
            $data[] = ['value' => $multiple->getId(), 'label' => $label];
        }

        $data[] = ['value' => 0, 'label' => 'Manually Enter Correct ASIN'];

        return $data;
    }
}
