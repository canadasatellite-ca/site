<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Pdf\Items;
/**
 * Trait QuoteItem
 *
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Items
 */
trait QuoteItem
{
    /**
     * Draw item line
     *
     * @return void
     */
    private function draw()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->getQuote();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $this->_setFontRegular();
        $drawItems = [];
        $line = [];
        $feed = 30;
        $split = 65;
        $skuFeed = 205;
        $isCustomProduct = $this->customProductHelper->isCustomProduct($item);
        //Print Thumbnails Next to Product Name if enabled
        $productThumbs = $this->drawProductImage($item, $pdf, $page, $feed, $skuFeed, $split);
        /* in case Product name is longer than 80 chars - it is written in a few lines */
        $product =  $this->productRepositoryInterface->
        getById($item->getProduct()->getId(), false, $quote->getStoreId());
        if ($isCustomProduct) {
            $name = $this->customProductHelper->getCustomProductName($item);
        } else {
            $name = $product->getName();
        }
        /* optional items */
        if ($item->getCurrentTierItem()->getMakeOptional()) {
            $name .= '*';
        }
            $nameArray['font'] = 'bold';
            $nameArray['text'] = $pdf->getStringUtils()->split($name, $split, true, true);
            $nameArray['feed'] = 65;
            $nameArray['addToTop'] = -5;
            $nameArray['isProductLine'] = true;
            $nameArray['width'] = 340;
            $nameArray['align'] = 'left';
        $line[0] = $nameArray;
        $nameLineCount = count($nameArray['text']);
        $enabledShortDescription = $this->_scopeConfig->getValue(
            'cart2quote_pdf/quote/pdf_enable_short_description',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $splitDescription = [];
//        if ($enabledShortDescription) {
//            /* set short description beneath name*/
//            $product = $this->productRepositoryInterface->getById($item->getProductId());
//            $shortDescription = $product->getShortDescription();
//            if ($nameLineCount > 1) {
//                $shortDescription = ' ';
//            }
//            if ($shortDescription != '' && $shortDescription != null) {
//                $shortDescription = strip_tags(trim($shortDescription));
//            }
//            $splitDescription = $pdf->getStringUtils()->split($shortDescription, 65, true, true);
//            $line[] = [
//                'text' => $splitDescription,
//                'feed' => 65,
//                'addToLeft' => 5,
//                'addToTop' => 5,
//                'font' => 'italic',
//                'isProductLine' => true
//            ];
//        }

        // draw SKUs
            $text = [];
            $skuSplit = 65;
            $skuLineCount = count($pdf->getStringUtils()->split($item->getSku(), $skuSplit));
            foreach ($pdf->getStringUtils()->split($item->getSku(), $skuSplit) as $part) {
                if ($isCustomProduct) {
                    $text[] = $this->customProductHelper->getCustomProductSku($item);
                } else {
                    $text[] = $part;
                }
            }
            $text[0] = 'SKU: ' . $text[0];
            $skuPosition = $nameLineCount * 10;
            $line[] = ['text' => $text, 'feed' => 65, 'isProductLine' => false, 'width' => 340, 'align' => 'left', 'addToTop' => $skuPosition];
            $line[] = ['text' => '', 'feed' => 65, 'isProductLine' => false, 'width' => 340, 'align' => 'left', 'addToTop' => $skuPosition + 10];


        // draw prices
//            var_dump($line); die;
        $line = $this->drawProductPrices($item, $quote, $line);
        /**
         * Draw Options
         */
        $options = $this->getQuoteItemOptions();
            if ($options) {
                $optionsLabels = [];
                foreach ($options as $ket => $option) {
                    $optionsLabels[] = $option['label'] . ':';
                    if ($option['value']) {
                        if (isset($option['print_value'])) {
                            $optionsLabels[] = $option['print_value'];
                        } else {
                            $optionsLabels = $this->filterManager->stripTags($option['value']);
                        }
                    }
                }
                $optionsLines = [];

                foreach ($optionsLabels as $key => $optionsLabel) {
                    if ($key % 2 == 0) {
                        $optionsLines[][] = [
                            'text' => $optionsLabel,
                            'font' => 'bold',
                            'feed' => 65,
                            'font_size' => 10
                        ];
                        continue;
                    }

                    $optionsLines[][] = [
                        'text' => $optionsLabel,
                        'feed' => 85,
                        'font_size' => 10
                    ];
                }

                $optionsLines[][] = [
                    'text' => '',
                    'feed' => 65,
                    'align' => 'left',
                    'width' => 70,
                    'font_size' => 10,
                    'height' => 12
                ];
            }
//        if ($options) {
//            foreach ($options as $option) {
//                $customOption = sprintf('%s: %s', $option['label'], strip_tags($option['value']));
//                foreach ($pdf->getStringUtils()->split($customOption, $split) as $part) {
//                    $optionText[] = $part;
//                }
//            }
//            if ($nameLineCount == 1) {
//                $nameLineCount = 2;
//            }
//            $top = (count($splitDescription) + ($nameLineCount * 2)) * 10;
//            $line[] = [
//                'text' => $optionText,
//                'feed' => 65,
//                'isProductLine' => true,
//                'addToTop' => $top
//            ];
//            $line[] = ['text' => '', 'feed' => 95, 'isProductLine' => false, 'width' => 340, 'align' => 'left', 'addToTop' => $top + 50];
//
//        }
        /**
         * Print Bundle Options
         */
        if ($item->getProductType() == 'bundle') {
            $longestChildLine = 0;
            foreach ($item->getChildren() as $child) {
                $childProduct = $this->productRepositoryInterface->getById(
                    $child->getProductId(),
                    false,
                    $quote->getStoreId()
                );
                $childName = $childProduct->getName();
                $childQty = $child->getQty();
                $childLine = sprintf("%s x %s", $childName, $childQty);
                $longestChildLine = max($longestChildLine, $pdf->getStringUtils()->strlen($childLine));
                $bundleText[] = $childLine;
            }
            if ($longestChildLine < ($split - 5)) {
                $top = (count($splitDescription) + $nameLineCount) * 10 + 20;
            } else {
                $top = (count($splitDescription) + max($nameLineCount, $skuLineCount)) * 10 + 20;
            }
            $line[] = [
                'feed' => $feed + 10,
                'isProductLine' => true,
                'addToTop' => $top,
                'font' => 'italic'
            ];
        }
        $optionLineCount = 0;
        if (isset($optionText)) {
            $optionLineCount += count($optionText);
        }
        if (isset($bundleText)) {
            $optionLineCount += count($bundleText);
        }
        //draw comment
        $comment = '';
        if ($item->getDescription()) {
            $comment = $item->getDescription();
            $top = (count($splitDescription) + $nameLineCount + $optionLineCount) * 10;
            $line[] = [
                'text' => $pdf->getStringUtils()->split(__('Comment'), 35, true, true),
                'feed' => $feed + 5,
                'addToLeft' => 5,
                'addToTop' => $top,
                'font' => 'bold',
                'isProductLine' => true
            ];
            $line[] = [
                'text' => $pdf->getStringUtils()->split($comment, 35, true, true),
                'feed' => $feed + 5,
                'addToLeft' => 5,
                'addToTop' => $top + 10,
                'font' => 'italic',
                'isProductLine' => true
            ];
        }
        $drawItems[]['lines'][] = $line;
        /**
         * Need to add space for Thumbnails if a product is on one line
         */
        if ($productThumbs && !($item->getProductType() == 'bundle') && !$options) {
            $lines[][] = [
                'text' => [''],
                'feed' => 0,
                'addToTop' => 20
            ];
            $drawItems[] = ['lines' => $lines, 'height' => 15];
        }
        /**
         * Print all lines on the page
         */
        $page = $pdf->drawLineBlocks($page, $drawItems, ['table_header' => true]);
            if (count($optionsLines) > 0) {
                $optionsLineBlock = ['lines' => $optionsLines, 'height' => 15];
                $page = $pdf->drawLineBlocks($page, [$optionsLineBlock], ['table_header' => true]);
            }
        $this->setPage($page);
		}
	}
    /**
     * Get Selected Custom Options from a Product
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getQuoteItemOptions()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$result = [];
        $item = $this->getItem();
        $keys = ['options', 'additional_options', 'attributes_info'];
        $product = $item->getProduct();
        if (isset($product)) {
            $options = $product->getTypeInstance()->getOrderOptions($product);
            foreach ($keys as $key) {
                if (isset($options[$key])) {
                    $result = array_merge($result, $options[$key]);
                }
            }
        }
        return $result;
		}
	}
    /**
     * Set the tier quantity to PDF
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Cart2Quote\Quotation\Model\Quote\TierItem $item
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function setTierItemsPdf($quote, $item)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$fontType = 'italic'; //differentiate selected from options
        $prices = $this->getQuoteTierItemPricesForDisplay();
        $tierItems = $item->getTierItems();
        $addToTop = 15;
        $line = [];
        $bottomMargin = 15;
        if ($this->_taxData->displaySalesBothPrices()) {
            $bottomMargin = 0;
        }
        foreach ($tierItems->getItems() as $tierItem) {
            if ($tierItem->getQty() != $item->getQty()) {
                $tax = $quote->formatPriceTxt($tierItem->getTaxAmount());
                $addToTop += 15;
                $tierPrices = $prices[$tierItem->getId()];
                foreach ($tierPrices as $priceData) {
                    // draw Price
                    $line[] = [
                        'text' => $priceData['label'] . $priceData['price'],
                        'feed' => $this::PRICE_FEED,
                        'font' => $fontType,
                        'align' => 'right',
                        'isProductLine' => true,
                        'addToTop' => $addToTop - $bottomMargin
                    ];
                    // draw Subtotal
                    $line[] = [
                        'text' => $priceData['subtotal'],
                        'feed' => $this::ROW_TOTAL_FEED,
                        'font' => $fontType,
                        'align' => 'right',
                        'isProductLine' => true,
                        'addToTop' => $addToTop - $bottomMargin
                    ];
                    $addToTop += 15;
                }
                $line[] = [
                    'text' => $tierItem->getQty() * 1,
                    'feed' => $this::QTY_FEED,
                    'font' => $fontType,
                    'isProductLine' => true,
                    'addToTop' => $addToTop - 30
                ];
                $line[] = [
                    'text' => $tax,
                    'feed' => $this::TAX_FEED,
                    'font' => $fontType,
                    'align' => 'right',
                    'isProductLine' => true,
                    'addToTop' => $addToTop - 30
                ];
            }
        }
        return $line;
		}
	}
    /**
     * Print Thumbnails Next to Product Name if enabled
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Magento\Sales\Model\Order\Pdf\AbstractPdf $pdf
     * @param \Zend_Pdf_Page $page
     * @param int $feed
     * @param int $skuFeed
     * @param int $split
     * @return bool
     * @throws \Zend_Pdf_Exception
     */
    private function drawProductImage(
        \Magento\Framework\DataObject $item,
        \Magento\Sales\Model\Order\Pdf\AbstractPdf $pdf,
        \Zend_Pdf_Page $page,
        &$feed,
        &$skuFeed,
        &$split
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if ($productThumbs = $this->thumbnailHelper->showProductThumbnailPdf()) {
            $feed = 70;
            $skuFeed = 210;
            $split = 30;
            if ($thumbnail = $item->getProduct()->getThumbnail()) {
                $imagePath = self::CATALOG_PRODUCT_PATH . $thumbnail;
                if ($this->_mediaDirectory->isFile($imagePath)) {
                    $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
                    $widthLimit = 40;
                    //half of the page width
                    $heightLimit = 40;
                    //assuming the image is not a "skyscraper"
                    $width = $image->getPixelWidth();
                    $height = $image->getPixelHeight();
                    //preserving aspect ratio (proportions)
                    $ratio = $width / $height;
                    if ($ratio > 1 && $width > $widthLimit) {
                        $width = $widthLimit;
                        $height = $width / $ratio;
                    } elseif ($ratio < 1 && $height > $heightLimit) {
                        $height = $heightLimit;
                        $width = $height * $ratio;
                    } elseif ($ratio == 1 && $height > $heightLimit) {
                        $height = $heightLimit;
                        $width = $widthLimit;
                    }
                    $top = $pdf->y + 5;
                    if ($top > 840) {
                        $top = 830;
                    }
                    $x1 = 5;
                    $x2 = $x1 + $width;
                    $y1 = $top - $height;
                    $y2 = $top;
                    //coordinates after transformation are rounded by Zend
                    $page->drawImage($image, $x1, $y1, $x2, $y2);
                }
            }
        }
        return $productThumbs;
		}
	}
    /**
     * Draw product prices
     *
     * @param \Magento\Framework\DataObject $item
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param array $line
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function drawProductPrices(
        \Magento\Framework\DataObject $item,
        \Cart2Quote\Quotation\Model\Quote $quote,
        array $line
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$fontType = 'bold';
        $qty = $item->getQty();
        $prices = $this->getQuoteTierItemPricesForDisplay();
        $itemPrices = $prices[$item->getCurrentTierItem()->getId()];
        if ($item->getParentItem()) {
            $fontType = 'regular';
            $tax = null;
            $row_total = null;
            $qty = null;
        } else {
            $tax = $quote->formatPriceTxt($item->getTaxAmount());
        }
        $i = -5;
        foreach ($itemPrices as $priceData) {
            // draw Price
            $line[] = [
                'text' => $priceData['label'] . $priceData['price'],
                'feed' => 430,
                'font' => 'bold',
                'align' => 'center',
                'isProductLine' => true,
                'addToTop' => $i,
                'width' => 70
            ];
            // draw Subtotal
            $line[] = [
                'text' => $priceData['subtotal'],
                'feed' => $this::ROW_TOTAL_FEED,
                'font' => 'bold',
                'align' => 'right',
                'isProductLine' => true,
                'addToTop' => $i
            ];
            $i += 15;
        }
        if ($item->getCurrentTierItem()->getCustomPrice() != null) {
            $line[] = [
                'text' => $qty,
                'feed' => 5,
                'font' => $fontType,
                'isProductLine' => true,
                'addToTop' => -5,
                'width' => 50,
                'align' => 'center'
            ];
//            $line[] = [
//                'text' => $tax,
//                'feed' => $this::TAX_FEED,
//                'font' => $fontType,
//                'align' => 'right',
//                'isProductLine' => true,
//                'addToTop' => -5
//            ];
            if ($item->getTierItems()) {
                $lineTier = $this->setTierItemsPdf($quote, $item);
                $line = array_merge($line, $lineTier);
            }
        }
        return $line;
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
     * Set font as regular
     *
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    private function _setFontRegular($size = 7)
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
        $this->getPage()->setFont($font, $size);
        return $font;
		}
	}
    /**
     * Set font as bold
     *
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    private function _setFontBold($size = 7)
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
        $this->getPage()->setFont($font, $size);
        return $font;
		}
	}
    /**
     * Set font as italic
     *
     * @param  int $size
     * @return \Zend_Pdf_Resource_Font
     */
    private function _setFontItalic($size = 7)
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
        $this->getPage()->setFont($font, $size);
        return $font;
		}
	}
}
