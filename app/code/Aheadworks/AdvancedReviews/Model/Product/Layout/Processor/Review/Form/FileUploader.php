<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\Form;

use Aheadworks\AdvancedReviews\Model\Config;
use Aheadworks\AdvancedReviews\Model\Layout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;

/**
 * Class FileUploader
 * @package Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\Form
 */
class FileUploader implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     * @param Config $config
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        Config $config
    ) {
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout, $productId = null, $storeId = null)
    {
        if ($this->config->isAllowCustomerAttachFiles($storeId)) {
            $reviewFormChildrenPath = 'components/awArReviewContainer/children/awArReviewForm/children';
            $jsLayout = $this->arrayManager->merge(
                $reviewFormChildrenPath,
                $jsLayout,
                [
                    'files' => [
                        'component' => 'Aheadworks_AdvancedReviews/js/product/view/review/form/file-uploader',
                        'dataScope' => 'attachments',
                        'isMultipleFiles' => true,
                        'provider' => 'awArReviewFormProvider',
                        'template' => 'Aheadworks_AdvancedReviews/product/view/review/form/uploader/uploader',
                        'previewTmpl' => 'Aheadworks_AdvancedReviews/product/view/review/form/uploader/preview',
                        'maxFileSize' => $this->config->getMaxUploadFileSize($storeId),
                        'allowedExtensions' => $this->config->getAllowFileExtensions($storeId),
                        'notice' => $this->getNotice($storeId),
                        'sortOrder' => 70,
                        'uploaderConfig' => [
                            'url' => $this->urlBuilder->getUrl('aw_advanced_reviews/review/upload', ['_secure' => true])
                        ]
                    ]
                ]
            );
        }

        return $jsLayout;
    }

    /**
     * Retrieve notice
     *
     * @param int|null $storeId
     * @return \Magento\Framework\Phrase|string
     */
    private function getNotice($storeId)
    {
        if (!empty($this->config->getAllowFileExtensions($storeId))) {
            $fileTypes = implode(', ', $this->config->getAllowFileExtensions());
            return __('The following file types are allowed: %1', $fileTypes);
        }

        return '';
    }
}
