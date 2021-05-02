<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Pdf;
use Magento\Store\Model\Store;
/**
 * Quotation PDF abstract model
 */
trait AbstractPdf
{
    /**
     * Get string standard library utilities
     *
     * @return \Magento\Framework\Stdlib\StringUtils
     */
    private function getStringUtils()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->string;
		}
	}
    /**
     * Insert address to pdf page
     *
     * @param \Zend_Pdf_Page $page
     * @param null|Store $store
     * @throws \Zend_Pdf_Exception
     */
    private function insertAddress(&$page, $store = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $font = $this->_setFontRegular($page, 10);
        $page->setLineWidth(0);
        $this->y = $this->y ? $this->y : 815;
        $top = 815;
        $font = $this->_setFontRegular($page, 10);
        $addr = $this->_scopeConfig->getValue(
            self::XML_PATH_SALES_PDF_INVOICE_PACKINGSLIP_ADDRESS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getCode()
        ) . "\n";
        $rightAlign = 130;
        if ($addr == "\n") {
            $rightAlign = $rightAlign - 12;
            $font = $this->_setFontBold($page, 13);
            $addr = __("Quotation") . "\n";
        }
        foreach (explode("\n", $addr) as $value) {
            if ($value !== '') {
                $value = preg_replace('/<br[^>]*>/i', "", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $tst = $this->getAlignRight($_value, $rightAlign, 440, $font, 10);
                    $page->drawText(
                        trim(strip_tags($_value)),
                        $tst,
                        $top,
                        'UTF-8'
                    );
                    $top -= 10;
                }
            }
        }
        $this->y = $this->y > $top ? $top : $this->y;
		}
	}
    /**
     * Insert General comment to PDF
     *
     * @param \Zend_Pdf_Page $page
     * @param string $text
     * @throws \Zend_Pdf_Exception
     */
    private function insertFooter(\Zend_Pdf_Page $page, $text)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$fontSize = 10;
        $margin = 10;
        $feed = 35;
        $left = $feed - $margin;
        $right = $page->getWidth() - ($feed - $margin);
        $footerMaxLineLength = 100;
        // Get text and split into array
        $footerlines = $this->string->split($text, $footerMaxLineLength, true, true);
        $lines = [];
        foreach ($footerlines as $footerline) {
            if (!empty($footerline)) {
                //Replace html breaks with empty strings
                $lines[] = preg_replace('/<br[^>]*>/i', "", $footerline);
            }
        }
        //Calculate bottom
        $lineCount = count($lines);
        $lineHeight = $fontSize + 2;
        $requiredSpace = ($lineHeight * $lineCount) + $margin;
        $top = $requiredSpace + $margin;
        if ($this->y < $top) {
            $page = $this->newPage();
        }
        $bottom = $top - $requiredSpace;
        //Draw footer box
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle($left, $top, $right, $bottom);
        //Draw footer text
        $font = $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.20));
        $nextLine = $top;
        foreach ($lines as $line) {
            $feed = $this->getAlignCenter(
                trim(strip_tags($line)),
                $margin,
                $page->getWidth() - ($margin * 2),
                $font,
                $fontSize
            );
            $page->drawText(trim(strip_tags($line)), $feed, $nextLine -= $lineHeight, 'UTF-8');
        }
		}
	}
    /**
     * Insert quote comment to PDF
     *
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return \Zend_Pdf_Page
     * @throws \Zend_Pdf_Exception
     */
    private function insertComments(\Zend_Pdf_Page $page, $quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			//Add title
        $commentLabel = __("Remarks with quote: ");
        $comments = array_merge([$commentLabel], explode("\n", $quote->getCustomerNote()));
        $lines = [];
        $commentMaxLineLength = 50;
        foreach ($comments as $value) {
            if (!empty($value)) {
                //Replace html breaks with empty strings
                $value = preg_replace('/<br[^>]*>/i', "", $value);
                //Split the string for specified length
                foreach ($this->string->split($value, $commentMaxLineLength, true, true) as $part) {
                    $lines[] = $part;
                }
            }
        }
        $fontSize = 10;
        $lineCount = count($lines);
        $lineHeight = $fontSize + 2;
        $margin = 10;
        $feed = 35;
        $top = $this->y - $margin;
        $left = $feed - $margin;
        $right = 305;
        $bottom = ($top - ($lineHeight * $lineCount)) - $margin;
        //Draw comment box
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.90));
        $page->drawRectangle($left, $top, $right, $bottom);
        //Draw comments
        $this->_setFontBold($page, $fontSize);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $nextLine = $top;
        foreach ($lines as $line) {
            $page->drawText(trim(strip_tags($line)), $feed, $nextLine -= $lineHeight, 'UTF-8');
            $this->_setFontRegular($page, $fontSize);
        }
        $this->y -= 20;
        return $page;
		}
	}
    /**
     * Insert Quote to pdf page
     *
     * @param \Zend_Pdf_Page &$page
     * @param \Magento\Sales\Model\Order $obj
     * @param bool $putQuoteId
     * @return void
     */
    private function insertQuote(&$page, $obj, $putQuoteId = true)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($obj instanceof \Cart2Quote\Quotation\Model\Quote) {
            $shipment = null;
            $quote = $obj;
        }
        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
            $page->setLineColor(new \Zend_Pdf_Color_GrayScale(1));
            $page->drawRectangle(25, $top, 570, $top - 55);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $this->setDocHeaderCoordinates([25, $top, 570, $top - 55]);
            $this->y += 20;
//        $top += 20;

            $headerLines[0][0] = [
                'text' => __('Quote - '),
                'feed' => 470,
                'align' => 'right',
//            'font' => 'italic',
                'width' => 50,
                'font_size' => 16
            ];

            $headerLines[0][1] = [
                'text' => $quote->getIncrementId(),
                'feed' => 530,
                'align' => 'center',
                'font' => 'bold',
                'width' => 50,
                'font_size' => 16
            ];

            $headerLines[2][] = [
                'text' =>$this->_localeDate->formatDate(
                    $this->_localeDate->scopeDate(
                        $quote->getStore(),
                        $quote->getCreatedAt(),
                        true
                    ),
                    \IntlDateFormatter::LONG,
                    false
                ),
                'feed' => 544,
                'align' => 'right',
//            'font' => 'italic',
                'width' => 50,
                'font_size' => 14,
                'addToTop' => 5
            ];

            $lineBlock1 = [
                'lines' => $headerLines,
                'height' => 50
            ];

            $this->drawLineBlocks($page, [$lineBlock1], ['table_header' => true]);
//        if ($quote->getExpiryEnabled() && ($quote->getExpiryDate() !== null)) {
//            $page->drawText(
//                __('Quotation Valid Until: ') .
//                $this->_localeDate->formatDate(
//                    $this->_localeDate->scopeDate(
//                        $quote->getStore(),
//                        $quote->getExpiryDate(),
//                        true
//                    ),
//                    \IntlDateFormatter::MEDIUM,
//                    false
//                ),
//                375,
//                $top -= 15,
//                'UTF-8'
//            );
//        }
        /**
         * Guest should not display addres or name.
         * still return a white line to ensure layout does not break.
         */
        if ($quote->getCustomerIsGuest() &&
            $this->_scopeConfig->getValue(
                \Cart2Quote\Quotation\Helper\Address::ALLOW_GUEST,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            ) != 2) {
            $this->y = $top - 40;
            return;
            /** The rest of the styling is ignored */
        }
        $top -= 10;
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.714, 0.012, 0.016));
            $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.714, 0.012, 0.016));
            $page->setLineWidth(0.5);
            $page->drawRectangle(0, $top, 275, $top - 23);
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
            $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
            $page->drawRectangle(275, $top, 595, $top - 23);

            $page->setLineColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));
        /* Calculate blocks info */
        /* Billing Address */
        $billingAddress = $this->_formatAddress(
            $this->addressRenderer->formatQuoteAddress($quote->getBillingAddress(), 'pdf')
        );
        /* Payment */
        if ($quote->getPayment()->getMethod()) {
            //set the quote object as order data on the payment data
            // - to allow PDF printing of payment data after order creation
            $paymentData = $quote->getPayment();
            $paymentData->setOrder($quote);
            $paymentBlock = $this->_paymentData->getInfoBlock($paymentData);
            $paymentBlock->addChild(
                'payment_instructions',
                \Cart2Quote\Quotation\Block\Adminhtml\Payment\Info\Instructions::class,
                $paymentBlock->getData()
            );
            $paymentInfo = $paymentBlock->setIsSecureMode(true)->toPdf();
            $paymentInfo = htmlspecialchars_decode($paymentInfo, ENT_QUOTES);
            $payment = explode('{{pdf_row_separator}}', $paymentInfo);
            foreach ($payment as $key => $value) {
                if (strip_tags(trim($value)) == '') {
                    unset($payment[$key]);
                }
            }
            reset($payment);
        } else {
            $payment = [];
        }
        $page = $this->drawShippingAddressAndMethod($page, $quote, $top, $billingAddress, $payment, $shipment);
		}
	}
    /**
     * Insert totals to pdf page
     *
     * @param \Zend_Pdf_Page $page
     * @param \Magento\Sales\Model\AbstractModel $quote
     * @return \Zend_Pdf_Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function insertTotals($page, $quote)
    {
        if (\Cart2Quote\License\Model\License::getInstance()->isValid()) {
            $totals = $this->_getTotalsList();
            $lineBlock = ['lines' => [], 'height' => 20];
            $quote->collectTotals();
            foreach ($totals as $total) {
                $total->setQuote($quote)->setSource($quote)->setOrder($quote);
                $candisplay = $total->canDisplay();
                if ($candisplay)
                {
                    $total->setFontSize(10);
                    foreach ($total->getTotalsForDisplay() as $totalData) {
                        if ($totalData['label'] == '') {
                            continue;
                        }
                        if ($totalData['label'] == 'Shipping & Handling:') {
                            $totalData['label'] = 'Shipping Cost';
                        }
//                        $this->y -= 1;

                        $lineBlock['lines'][] = [
                            [
                                'text' => str_replace(':', '', $totalData['label']),
                                'feed' => 430,
                                'align' => 'center',
                                'font_size' => $totalData['font_size'],
                                'font' => 'bold',
                                'width' => 70,
                                'addToTop' => 10
                            ],
                            [
                                'text' => $totalData['amount'],
                                'align' => 'center',
                                'feed' => 500,
                                'font_size' => $totalData['font_size'],
                                'font' => 'bold',
                                'width' => 80,
                                'addToTop' => 10
                            ],
                        ];
                    }
                }
            }
//            $this->y -= 1;
            $page = $this->drawLineBlocks($page, [$lineBlock]);
            return $page;
        }
	}
    /**
     * Draw lines
     *
     * Draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param  \Zend_Pdf_Page $page
     * @param  array $draw
     * @param  array $pageSettings
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Zend_Pdf_Page
     */
    private function drawLineBlocks(\Zend_Pdf_Page $page, array $draw, array $pageSettings = [])
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
            foreach ($draw as $itemsProp) {
                if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('We don\'t recognize the draw line data. Please define the "lines" array.')
                    );
                }
                $lines = $itemsProp['lines'];
                $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;
                if (empty($itemsProp['shift'])) {
                    $shift = 0;
                    foreach ($lines as $line) {
                        $maxHeight = 0;
                        foreach ($line as $column) {
                            $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
							# 2021-05-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
							# «Notice: Undefined index: text
							# in app/code/Cart2Quote/Features/Traits/Model/Quote/Pdf/AbstractPdf.php on line 410»:
							# https://github.com/canadasatellite-ca/site/issues/94
                            $column += ['text' => ''];
                            if (!is_array($column['text'])) {
                                $column['text'] = [$column['text']];
                            }
                            $top = 0;
                            foreach ($column['text'] as $part) {
                                $top += $lineSpacing;
                            }
                            $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                        }
                        $shift += $maxHeight;
                    }
                    $itemsProp['shift'] = $shift;
                }
                if ($this->newPage == true) {
                    $this->newPage = false;
                }
                $fullLineHeight = 15;
                if ($this->thumbnailHelper->showProductThumbnailPdf() == true) {
                    $fullLineHeight = 40;
                }
                if ($this->y - $itemsProp['shift'] < $fullLineHeight) {
                    $page = $this->newPage($pageSettings);
                    $this->newPage = true;
                }
                foreach ($lines as $line) {
                    $page = $this->drawLineBlockRow($page, $pageSettings, $line, $height);
                }
            }
            return $page;
        }
	}
    /**
     * Before getPdf processing
     *
     * @return void
     */
    private function _beforeGetPdf()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->inlineTranslation != null) {
            $this->inlineTranslation->suspend();
        }
		}
	}
    /**
     * After getPdf processing
     *
     * @return void
     */
    private function _afterGetPdf()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->inlineTranslation != null) {
            $this->inlineTranslation->resume();
        }
		}
	}
    /**
     * Draw Quote Item process
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return \Zend_Pdf_Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function _drawQuoteItem(
        \Magento\Framework\DataObject $item,
        \Zend_Pdf_Page $page,
        \Cart2Quote\Quotation\Model\Quote $quote
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$type = $item->getProductType();
        $renderer = $this->getRenderer('quoteItem');
        $renderer->setItem($item);
        $renderer->setPdf($this);
        $renderer->setQuote($quote);
        $renderer->setPage($page);
        $renderer->setRenderedModel($this);
        $renderer->draw();
        return $renderer->getPage();
		}
	}
    /**
     * Function that draws the shipping options and prices
     *
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @param int $yPayments
     * @throws \Zend_Pdf_Exception
     */
    private function drawShippingOptionsAndPricesReplacement(&$page, $quote, $shipment, $yPayments)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$topMargin = 0;
        $methodStartY = $this->y;
        $this->y -= 15;
        foreach ($this->string->split(
            $quote->getShippingAddress()->getShippingDescription(),
            45,
            true,
            true
        ) as $_value) {
            $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
            $this->y -= 15;
        }
        $this->_setFontRegular($page, 11);
        $yShipments = $this->y;
        $totalShippingChargesText = "(";
        $totalShippingChargesText .= __('Total Shipping Charges');
        $totalShippingChargesText .= " ";
        $totalShippingChargesText .= $quote->formatPriceTxt($quote->getShippingAddress()->getShippingAmount());
        $totalShippingChargesText .= ")";
        $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
        $yShipments -= $topMargin + 10;
        $tracks = [];
        if ($shipment) {
            $tracks = $shipment->getAllTracks();
        }
        if (!empty($tracks)) {
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineWidth(0.5);
            $page->drawRectangle(285, $yShipments, 510, $yShipments - 10);
            $page->drawLine(400, $yShipments, 400, $yShipments - 10);
            $this->_setFontRegular($page, 9);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $page->drawText(__('Title'), 290, $yShipments - 7, 'UTF-8');
            $page->drawText(__('Number'), 410, $yShipments - 7, 'UTF-8');
            $yShipments -= 20;
            $this->_setFontRegular($page, 8);
            foreach ($tracks as $track) {
                $maxTitleLen = 45;
                $endOfTitle = strlen($track->getTitle()) > $maxTitleLen ? '...' : '';
                $truncatedTitle = substr($track->getTitle(), 0, $maxTitleLen) . $endOfTitle;
                $page->drawText($truncatedTitle, 292, $yShipments, 'UTF-8');
                $page->drawText($track->getNumber(), 410, $yShipments, 'UTF-8');
                $yShipments -= $topMargin - 5;
            }
        } else {
            $yShipments -= $topMargin - 5;
        }
        $currentY = min($yPayments, $yShipments);
        // replacement of Shipments-Payments rectangle block
        $page->drawLine(25, $methodStartY, 25, $currentY);
        //left
        $page->drawLine(25, $currentY, 570, $currentY);
        //bottom
        $page->drawLine(570, $currentY, 570, $methodStartY);
        //right
        $this->y = $currentY;
        $this->y -= 15;
		}
	}
    /**
     * Add name to the top.
     *
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param int $top
     * @return int
     */
    private function addName(&$page, $quote, $top)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$page->drawText(
            __('Name: ') . $quote->getCustomerName(),
            35,
            $top -= 15,
            'UTF-8'
        );
        return $top;
		}
	}
    /**
     * Set page font.
     *
     * - column array format
     * - font         string; font style, optional: bold, italic, regular
     * - font_file    string; path to font file (optional for use your custom font)
     * - font_size    int; font size (default 10)
     *
     * @param \Zend_Pdf_Page $page
     * @param array $column
     * @return \Zend_Pdf_Resource_Font
     * @throws \Zend_Pdf_Exception
     */
    private function setFont($page, &$column)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$fontSize = empty($column['font_size']) ? 10 : $column['font_size'];
        $column['font_size'] = $fontSize;
        if (!empty($column['font_file'])) {
            $font = \Zend_Pdf_Font::fontWithPath($column['font_file']);
            $page->setFont($font, $fontSize);
        } else {
            $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
            switch ($fontStyle) {
                case 'bold':
                    $font = $this->_setFontBold($page, $fontSize);
                    break;
                case 'italic':
                    $font = $this->_setFontItalic($page, $fontSize);
                    break;
                default:
                    $font = $this->_setFontRegular($page, $fontSize);
                    break;
            }
        }
        return $font;
		}
	}
    /**
     * Set font as regular
     *
     * @param \Zend_Pdf_Page $object
     * @param int $size
     * @return \Zend_Pdf_Resource_Font
     * @throws \Zend_Pdf_Exception
     */
    private function _setFontRegular($object, $size = 7)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->saveSpaceFonts()) {
            $font = \Zend_Pdf_Font::fontWithName(
                \Zend_Pdf_Font::FONT_TIMES,
                \Zend_Pdf_Font::EMBED_DONT_EMBED
            );
        } else {
            $fontPath = $this->_rootDirectory->getAbsolutePath('lib/internal/GnuFreeFont/FreeSerif.ttf');
            //fallback for <M2.3
            if (!file_exists($fontPath)) {
                $fontPath = $this->_rootDirectory
                    ->getAbsolutePath('lib/internal/LinLibertineFont/LinLibertine_Re-4.4.1.ttf');
            }
            $font = \Zend_Pdf_Font::fontWithPath($fontPath);
        }
            $font = \Zend_Pdf_Font::fontWithName(
                \Zend_Pdf_Font::FONT_HELVETICA
            );
        $object->setFont($font, $size);
        return $font;
		}
	}
    /**
     * Set font as regular bold
     *
     * @param \Zend_Pdf_Page $object
     * @param int $size
     * @return \Zend_Pdf_Resource_Font
     * @throws \Zend_Pdf_Exception
     */
    private function _setFontBold($object, $size = 7)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->saveSpaceFonts()) {
            $font = \Zend_Pdf_Font::fontWithName(
                \Zend_Pdf_Font::FONT_TIMES_BOLD,
                \Zend_Pdf_Font::EMBED_DONT_EMBED
            );
        } else {
            $fontPath = $this->_rootDirectory->getAbsolutePath('lib/internal/GnuFreeFont/FreeSerifBold.ttf');
            //fallback for <M2.3
            if (!file_exists($fontPath)) {
                $fontPath = $this->_rootDirectory
                    ->getAbsolutePath('lib/internal/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf');
            }
            $font = \Zend_Pdf_Font::fontWithPath($fontPath);
        }
            $font = \Zend_Pdf_Font::fontWithName(
                \Zend_Pdf_Font::FONT_HELVETICA_BOLD
            );
        $object->setFont($font, $size);
        return $font;
		}
	}
    /**
     * Set font as italic
     *
     * @param \Zend_Pdf_Page $object
     * @param int $size
     * @return \Zend_Pdf_Resource_Font
     * @throws \Zend_Pdf_Exception
     */
    private function _setFontItalic($object, $size = 7)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($this->saveSpaceFonts()) {
            $font = \Zend_Pdf_Font::fontWithName(
                \Zend_Pdf_Font::FONT_TIMES_ITALIC,
                \Zend_Pdf_Font::EMBED_DONT_EMBED
            );
        } else {
            $fontPath = $this->_rootDirectory->getAbsolutePath('lib/internal/GnuFreeFont/FreeSerifItalic.ttf');
            //fallback for <M2.3
            if (!file_exists($fontPath)) {
                $fontPath = $this->_rootDirectory
                    ->getAbsolutePath('lib/internal/LinLibertineFont/LinLibertine_It-2.8.2.ttf');
            }
            $font = \Zend_Pdf_Font::fontWithPath($fontPath);
        }
        $object->setFont($font, $size);
        return $font;
		}
	}
    /**
     * Get the setting to save fonts
     *
     * @return boolean
     */
    private function saveSpaceFonts()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return (boolean)$this->_scopeConfig->getValue(
            self::XML_PATH_SAVE_SPACE_FONTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
		}
	}

    /**
     * Draws row data in line block
     * @used-by drawLineBlocks()
     * @param \Zend_Pdf_Page $page
     * @param array $pageSettings
     * @param array $line
     * @param int $height
     * @return \Zend_Pdf_Page $page
     * @throws \Zend_Pdf_Exception
     */
    private function drawLineBlockRow(\Zend_Pdf_Page $page, array $pageSettings, $line, $height)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$maxHeight = 0;
        foreach ($line as $column) {
            if (isset($column['imageUrl'])) {
                continue;
            }
            $fontSize = empty($column['font_size']) ? 10 : $column['font_size'];
            $font = $this->setFont($page, $column);
			# 2021-05-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			# «Notice: Undefined index: text
			# in app/code/Cart2Quote/Features/Traits/Model/Quote/Pdf/AbstractPdf.php on line 754»:
			# https://github.com/canadasatellite-ca/site/issues/98
			$column += ['text' => ''];
            if (!is_array($column['text'])) {
                $column['text'] = [$column['text']];
            }
            $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
            if (is_array($column['text'])) {
                $lineSpacing = 10;
            }
            $top = 0;
            if (isset($column['isProductLine'])) {
                $top += 10;
            }
            if (isset($column['addToTop'])) {
                $top += $column['addToTop'];
            }
            foreach ($column['text'] as $part) {
                if ($this->y - $lineSpacing < 15) {
                    $page = $this->newPage($pageSettings);
                    $font = $this->setFont($page, $column);
                    $fontSize = empty($column['font_size']) ? 10 : $column['font_size'];
                }
                $feed = $column['feed'];
                $textAlign = empty($column['align']) ? 'left' : $column['align'];
                $width = empty($column['width']) ? 0 : $column['width'];
                switch ($textAlign) {
                    case 'right':
                        if ($width) {
                            $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                        } else {
                            $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                        }
                        break;
                    case 'center':
                        if ($width) {
                            $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                        }
                        break;
                    default:
                        break;
                }
                $page->drawText($part, $feed, $this->y - $top, 'UTF-8');
                $top += $lineSpacing;
            }
            $maxHeight = $top > $maxHeight ? $top : $maxHeight;
        }
        $this->y -= $maxHeight;
        return $page;
		}
	}
    /**
     * Draw shipping address and method
     *
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param int $top
     * @param array $billingAddress
     * @param array $payment
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return \Zend_Pdf_Page $page
     * @throws \Zend_Pdf_Exception
     */
    private function drawShippingAddressAndMethod(
        &$page,
        $quote,
        $top,
        array $billingAddress,
        array $payment,
        $shipment
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/* Shipping Address and Method */
        if (!$quote->getIsVirtual()) {
            /* Shipping Address */
            $shippingAddress = $this->_formatAddress(
                $this->addressRenderer->formatQuoteAddress($quote->getShippingAddress(), 'pdf')
            );
            $shippingMethod = $quote->getShippingDescription();
        }
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->_setFontBold($page, 14);
        $page->drawText(__('Quote for'), 5, $top - 15, 'UTF-8');
            if (!$quote->getIsVirtual()) {
                $page->drawText(__('Ship to'), 285, $top - 15, 'UTF-8');
            } else {
                $page->drawText(__('Payment Method'), 285, $top - 15, 'UTF-8');
            }
        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $top - 25, 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;
        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $text[] = str_replace('T:', '', $_value);
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 5, $this->y, 'UTF-8');
                    $this->y -= 12;
                }
            }
        }
        $addressesEndY = $this->y;
        if (!$quote->getIsVirtual()) {
            $this->y = $addressesStartY;
            foreach ($shippingAddress as $value) {
                if ($value !== '') {
                    $text = [];
                    foreach ($this->string->split($value, 45, true, true) as $_value) {
                        $text[] = str_replace('T:', '', $_value);
                    }
                    foreach ($text as $part) {
                        $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                        $this->y -= 12;
                    }
                }
            }
            $addressesEndY = min($addressesEndY, $this->y);
            $this->y = $addressesEndY;
            $page->setLineColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
            $page->setLineWidth(0.2);
            $page->drawLine(5, $this->y, 135, $this->y);
            $page->drawLine(285, $this->y, 410, $this->y);
            $this->y -= 10;

            $page->setLineColor(new \Zend_Pdf_Color_Rgb(255, 255, 255));
            $this->y -= 10;

            $this->_setFontBold($page, 14);
            $page->drawText(
                __('Payment Method'),
                5,
                $this->y,
                'UTF-8'
            );
            $page->drawText(
                __('Shipping Method'),
                285,
                $this->y,
                'UTF-8'
            );
            $this->y -= 10;

            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));

            $paymentLeft = 35;
            $yPayments = $this->y - 15;
        } else {
            $yPayments = $addressesStartY;
            $paymentLeft = 310;
        }
        foreach ($payment as $value) {
            if (trim($value) != '') {
                //Printing "Payment Method" lines
                $value = preg_replace('/<br[^>]*>/i', "\n", $value);
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
                    $yPayments -= 15;
                }
            }
        }
        if ($quote->getIsVirtual()) {
            // replacement of Shipments-Payments rectangle block
            $yPayments = min($addressesEndY, $yPayments);
            $page->drawLine(25, $top - 25, 25, $yPayments);
            $page->drawLine(570, $top - 25, 570, $yPayments);
            $page->drawLine(25, $yPayments, 570, $yPayments);
            $this->y = $yPayments - 15;
        } else {
            $this->drawShippingOptionsAndPricesReplacement($page, $quote, $shipment, $yPayments);
        }
        return $page;
		}
	}
}
