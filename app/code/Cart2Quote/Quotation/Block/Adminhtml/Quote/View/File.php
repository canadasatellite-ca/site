<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

/**
 * Class File
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View
 */
class File extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\AbstractView
{
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\File
     */
    private $fileModel;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * File constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Cart2Quote\Quotation\Model\Quote $quoteCreate
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Quotation\Model\Quote\File $fileModel
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Cart2Quote\Quotation\Model\Quote $quoteCreate,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Quotation\Model\Quote\File $fileModel,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        array $data = []
    ) {
        $this->fileModel = $fileModel;
        $this->urlEncoder = $urlEncoder;
        parent::__construct($context, $sessionQuote, $quoteCreate, $orderCreate, $priceCurrency, $registry, $data);
    }

    /**
     * @return array
     */
    public function getUploadedFiles()
    {
        return $this->fileModel->getFileDataFromQuotation();
    }

    /**
     * @param string $file
     * @return string
     */
    public function getDownloadUrl($file)
    {
        $file = $this->urlEncoder->encode($file);
        return $this->getUrl('quotation/quote/downloadfile', ['file' => $file]);
    }

    /**
     * @param string $file
     * @return bool|string
     */
    public function trimFileName($file)
    {
        return basename($file);
    }
}
