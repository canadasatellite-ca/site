<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Controller\Adminhtml\Amazon\Account\Settings\Listings;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Attributes
 */
class Attributes extends Action
{
    /** @var ProductAttributeRepositoryInterface $productAttributeRepository */
    protected $productAttributeRepository;

    /**
     * @param Context $context
     * @param ProductAttributeRepositoryInterface $productAttributeRepository
     */
    public function __construct(
        Context $context,
        ProductAttributeRepositoryInterface $productAttributeRepository
    ) {
        parent::__construct($context);
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_SalesChannels::channel_amazon');
    }

    /**
     * Provides AJAX feature to product attribute selections
     *
     * @return void
     */
    public function execute()
    {
        /** @var string */
        $selectedValue = $this->getRequest()->getParam('selectedValue');
        /** @var string */
        $response = $this->generateAttributeOptions($selectedValue);

        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($response);
    }

    /**
     * Generates dynamic form field options
     *
     * @param string $selectedValue
     * @return string
     */
    private function generateAttributeOptions($selectedValue)
    {
        try {
            /** @var ProductAttributeInterface */
            $attribute = $this->productAttributeRepository->get($selectedValue);
        } catch (NoSuchEntityException $e) {
            // no attribute found
            return json_encode([]);
        }

        $response = ($attribute->getFrontendInput() === 'select') ? [] : '';

        // special handling for select type
        if ($options = $attribute->getOptions()) {
            foreach ($options as $option) {
                if (!$value = $option->getData('value')) {
                    continue;
                }

                // add label to select list
                if ($label = $option->getData('label')) {
                    $response[] = [
                        'value' => $value,
                        'label' => $label,
                        'labelTitle' => $label
                    ];
                }
            }
        }

        return json_encode($response);
    }
}
