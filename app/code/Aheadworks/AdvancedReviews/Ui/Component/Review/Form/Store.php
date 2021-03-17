<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Review\Form;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Store
 * @package Aheadworks\AdvancedReviews\Ui\Component\Review\Form
 */
class Store extends Field
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ContextInterface $context
     * @param StoreManagerInterface $storeManager
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        StoreManagerInterface $storeManager,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        if (!empty($dataSource['data'])) {
            $data = $dataSource['data'];
            if (isset($data[ReviewInterface::STORE_ID])) {
                $storeId = $data[ReviewInterface::STORE_ID];
                $data[$fieldName] = $this->storeManager->getStore($storeId)->getName();
            }
            $dataSource['data'] = $data;
        }
        return $dataSource;
    }
}
