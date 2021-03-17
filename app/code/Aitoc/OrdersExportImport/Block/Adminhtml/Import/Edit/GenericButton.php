<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\OrdersExportImport\Block\Adminhtml\Import\Edit;

use Magento\Backend\Block\Widget\Context;
use Aitoc\OrdersExportImport\Api\ProfileRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 *
 * @package Aitoc\OrdersExportImport\Block\Adminhtml\Import\Edit
 */
class GenericButton
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @param Context $context
     * @param ProfileRepositoryInterface $profileRepository
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
