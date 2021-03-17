<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class UpdateAvailable
 * @package Cart2Quote\Quotation\Block\Adminhtml\System\Config\Form\Field
 */
class UpdateAvailable extends Template
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serialize;

    /**
     * @var string
     */
    protected $_template = 'Cart2Quote_Quotation::system/config/updateAvailable.phtml';

    /**
     * UpdateAvailable constructor
     *
     * @param \Magento\Framework\Serialize\Serializer\Json $serialize
     * @param \Cart2Quote\Quotation\Helper\Data\LicenseInterface $license
     * @param \Cart2Quote\Quotation\Helper\Data\Metadata $metadata
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Serialize\Serializer\Json $serialize,
        \Cart2Quote\Quotation\Helper\Data\LicenseInterface $license,
        \Cart2Quote\Quotation\Helper\Data\Metadata $metadata,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($license, $metadata, $context, $data);
        $this->serialize = $serialize;
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        return $this->serialize->serialize($this->config);
    }

    /**
     * @return string
     */
    public function getBitbucketTagsUrl()
    {
        $apiUrl = 'https://bitbucket.org/api/2.0/repositories/cart2quote2/cart2quote2-releases/refs/tags';
        $params = '?pagelen=1&sort=-target.date&fields=values.name';

        return $apiUrl . $params;
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->jsLayout['components'] as $scope => &$component) {
            $component['config'] = \array_merge_recursive(
                [
                    'url' => $this->getBitbucketTagsUrl(),
                    'releaseNotesUrlFormat' => $this->getCart2QuotReleaseNotesUrl("%1"),
                    'currentVersion' => $this->getCurrentVersion()
                ],
                isset($component['config']) ? $component['config'] : []
            );

        }

        return parent::getJsLayout();
    }
}