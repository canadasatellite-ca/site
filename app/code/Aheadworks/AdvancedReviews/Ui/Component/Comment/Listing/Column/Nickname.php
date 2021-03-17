<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\Component\Comment\Listing\Column;

use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Model\Review\Comment\Resolver as CommentResolver;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Nickname
 * @package Aheadworks\AdvancedReviews\Ui\Component\Comment\Listing\Column
 */
class Nickname extends Column
{
    /**
     * @var CommentResolver
     */
    private $commentResolver;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CommentResolver $commentResolver
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CommentResolver $commentResolver,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->commentResolver = $commentResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $currentNickname = isset($item[CommentInterface::NICKNAME]) ? $item[CommentInterface::NICKNAME] : '';
                $item[$this->getData('name')] = $this->commentResolver->getNicknameForBackend($currentNickname);
            }
        }

        return $dataSource;
    }
}
