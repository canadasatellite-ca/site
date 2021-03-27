<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\Downloads\Block\Adminhtml\Attachment\Helper;

class MultiFile extends File
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * MultiFile constructor.
     * @param \MageWorx\Downloads\Model\Attachment\Link $fileLinkModel
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \MageWorx\Downloads\Model\Attachment\Link $fileLinkModel,
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        $data = []
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->urlBuilder = $urlBuilder;

        parent::__construct($fileLinkModel, $factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $_layout = $this->layoutFactory->create();
        /** @var \Magento\Framework\View\Element\Template $_block */
        $_block = $_layout->createBlock("Magento\Framework\View\Element\Template");
        $_block->setTemplate("MageWorx_Downloads::helper/multifile.phtml");
        $html = $_block->setData('multi_file', $this)->toHtml();
        return $html;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
    	# 2021-03-27 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		# "MageWorx_Downloads: «User Deprecated Functionality: Session ID is not used as URL parameter anymore»
		# in vendor/magento/framework/Url.php on line 76": https://github.com/canadasatellite-ca/site/issues/51
        $uploadUrl = $this->urlBuilder->getUrl('*/*/uploader');
        return $uploadUrl;
    }
}
