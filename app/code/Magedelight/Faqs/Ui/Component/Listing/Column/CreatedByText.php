<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magedelight\Faqs\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magedelight\Faqs\Model\Source\Faq\Created;

class CreatedByText extends \Magento\Ui\Component\Listing\Columns\Column
{

    const CREATEDBY = 'created_by';
    /**
     * @var \Magedelight\Faqs\Model\Source\Faq\Created
     */
    public $created;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Status $status
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Created $created,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->created = $created;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name');
        $sourceFieldName = self::CREATEDBY;
        foreach ($dataSource['data']['items'] as &$item) {
            if (!empty($item[$sourceFieldName])) {
                $item[$fieldName] = $this->created->getOptionText($item[$sourceFieldName]);
            }
        }

        return $dataSource;
    }
}
