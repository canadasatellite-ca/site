<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class PrintManifestButton
 */
class PrintManifestButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    private $_urlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $_request;

    /**
     * @var \Mageside\CanadaPostShipping\Model\Manifest
     */
    private $_manifest;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Mageside\CanadaPostShipping\Model\Manifest $manifest
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Mageside\CanadaPostShipping\Model\Manifest $manifest
    ) {
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_request = $context->getRequest();
        $this->_manifest = $manifest;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $manifestId = $this->_request->getParam('manifest_id');

        $data = [];
        if ($manifestId) {
            $manifest = $this->_manifest->load($manifestId);
            if ($manifest->getCpManifestId()) {
                $data = [
                    'label' => 'Print Manifest',
                    'class' => 'primary',
                    'on_click' => sprintf(
                        "location.href = '%s';",
                        $this->_urlBuilder->getUrl('*/*/printManifest', ['manifest_id' => $manifestId])
                    ),
                    'sort_order' => 20,
                ];
            }
        }

        return $data;
    }
}
