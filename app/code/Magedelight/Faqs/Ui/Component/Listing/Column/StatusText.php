<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magedelight\Faqs\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magedelight\Faqs\Model\Source\Faq\Status;

class StatusText extends \Magento\Ui\Component\Listing\Columns\Column
{

    const STATUS = 'is_active';
    /**
     * @var \Magedelight\Faqs\Model\Source\Faq\Status
     */
    public $status;

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
        Status $status,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->status = $status;
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
        $sourceFieldName = self::STATUS;

        foreach ($dataSource['data']['items'] as &$item) {
            if (!empty($item[$sourceFieldName])) {
                $item[$fieldName] = $this->status->getOptionText($item[$sourceFieldName]);
            }
        }

        return $dataSource;
    }
}
