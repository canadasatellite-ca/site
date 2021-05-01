<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Pdf;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaStorage\Helper\File\Storage\Database;

/**
 * Quote PDF model
 */
trait Quote
{

    public function __construct(\Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDatabase = null)
    {
        $this->fileStorageDatabase = $fileStorageDatabase ?:
            \Magento\Framework\App\ObjectManager::getInstance()->get(Database::class);
    }

    /**
     * Set Pdf model
     *
     * @param  \Cart2Quote\Quotation\Model\Quote\Pdf\AbstractPdf $pdf
     * @return $this
     */
    private function setPdf(\Cart2Quote\Quotation\Model\Quote\Pdf\AbstractPdf $pdf)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_pdf = $pdf;
        return $this;
		}
	}
    /**
     * Creates the Quote PDF and return the filepath
     *
     * @param array $quotes
     * @return string|null
     * @throws \Exception
     */
    private function createQuotePdf(array $quotes)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->setQuotes($quotes);
        try {
            $pdfRender = null;
            //event to allow other PDF renderer
            $this->eventManager->dispatch(
                'quotation_quote_pdf_create_before',
                ['quotes' => $quotes, 'render' => $pdfRender]
            );
            //if PDF render isn't set in the event, create te render
            if ($pdfRender === null || !is_string($pdfRender)) {
                //get the PDF object
                $pdf = $this->getPdf();
                if (isset($pdf)) {
                    //render the PDF
                    $pdfRender = $pdf->render();
                }
            }
            //write the PDF render to a file
            if (isset($pdfRender) && is_string($pdfRender)) {
                //write pdf to var/export_quotation/pdf directory
                $ds = DIRECTORY_SEPARATOR;
                $fileName = sprintf(
                    'export_quotation' . $ds . 'pdf' . $ds . '%s.pdf',
                    $this->getIncrementId($quotes)
                );
                $this->varDirectory->writeFile(
                    $fileName,
                    $pdfRender
                );
                //return the filename
                return $fileName;
            }
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getMessage());
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return null;
		}
	}
    /**
     * Get PDF document
     *
     * @return \Zend_Pdf
     * @internal param array|\Cart2Quote\Quotation\Traits\Model\Quote\Pdf\Collection $quotes
     */
    private function getPdf()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->_beforeGetPdf();
        $this->_initRenderer('quotation');
        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        $this->eventManager->dispatch(
            'quotation_quotepdf_getpdf_before',
            ['renderer' => $this]
        );
        foreach ($this->getQuotes() as $quote) {
            if ($quote->getStoreId()) {
                $this->setPdfLocale($quote->getStoreId());
            }
            $store = $quote->getStore();
            $page = $this->newPage();
            //extra event
            $this->eventManager->dispatch(
                'quotation_quotepdf_getpdf_before_logo',
                [
                    'renderer' => $this,
                    'page' => $page,
                    'quote' => $quote,
                    'store' => $store
                ]
            );
            /* Add image */
            $this->insertLogo($page, $store);

            //extra event
            $this->eventManager->dispatch(
                'quotation_quotepdf_getpdf_before_address',
                [
                    'renderer' => $this,
                    'page' => $page,
                    'quote' => $quote,
                    'store' => $store
                ]
            );
            /* Add address */
//            $this->insertAddress($page, $store);
            //extra event
            $this->eventManager->dispatch(
                'quotation_quotepdf_getpdf_before_quote',
                [
                    'renderer' => $this,
                    'page' => $page,
                    'quote' => $quote,
                    'store' => $store
                ]
            );
            /* Add quote data */
            $this->insertQuote($page, $quote);
            //extra event
            $this->eventManager->dispatch(
                'quotation_quotepdf_getpdf_before_sections',
                [
                    'renderer' => $this,
                    'page' => $page,
                    'quote' => $quote,
                    'store' => $store
                ]
            );
            /**
             * @var \Cart2Quote\Quotation\Model\Quote $quote
             */
            foreach ($quote->getSections() as $section) {
                $sectionItems = $quote->getSectionItems($section->getSectionId());
                if (!empty($sectionItems)) {
                    if ($section->getLabel()) {
                        $page = $this->_drawSectionHeader($page, $section);
                    }
                    /* Add table */
                    $headerY = $this->y;
                    $this->_drawHeader($page);
                    /*draw optional disclaimer */
                    $this->drawDisclaimer($page, $quote);
                    /* Add body */
                    foreach ($sectionItems as $item) {
                        if ($item->getParentItem() && ($item->getParentItem()->getProductType() != 'bundle')) {
                            continue;
                        }
                        /* Draw item */
                        $this->_drawQuoteItem($item, $page, $quote);
                        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
                        $page->setLineWidth(0.5);
                        $page->drawLine(5, $this->y + 12.5, 585, $this->y + 12.5);
                        $page = end($pdf->pages);
                    }
                }
            }
            //extra event
            $this->eventManager->dispatch(
                'quotation_quotepdf_getpdf_before_totals',
                [
                    'renderer' => $this,
                    'page' => $page,
                    'quote' => $quote,
                    'store' => $store
                ]
            );

            $page->drawLine(5, $headerY, 5, $this->y + 12.5);
            $page->drawLine(55, $headerY, 55, $this->y + 12.5);
            $page->drawLine(430, $headerY, 430, $this->y + 12.5);
            $page->drawLine(500, $headerY, 500, $this->y + 12.5);
            $page->drawLine(585, $headerY, 585, $this->y + 12.5);
            $this->_setFontBold($page, 10);
            $page->drawText(__('GST / HST Registration #775933914'), 5, $this->y - 5, 'UTF-8');
            /* Add totals */
//            $this->y += 10;
            $totalsY = $this->y;
            $beforeTotalsY = $this->y + 12.5;
            $this->insertTotals($page, $quote);
            $page->drawLine(430, $beforeTotalsY, 430, $this->y + 12.5);
            $page->drawLine(500, $beforeTotalsY, 500, $this->y + 12.5);
            $page->drawLine(585, $beforeTotalsY, 585, $this->y + 12.5);
            $beforeTotalsY -= 10;
            while (($beforeTotalsY -= 20) > ($this->y + 12.5)) {
                $page->drawLine(430, $beforeTotalsY, 585, $beforeTotalsY);
            }
            $page->drawLine(430, $beforeTotalsY, 430, $this->y + 12.5);
            $page->drawLine(500, $beforeTotalsY, 500, $this->y + 12.5);
            $page->drawLine(585, $beforeTotalsY, 585, $this->y + 12.5);
            $page->drawLine(430, $this->y + 5, 585, $this->y + 5);
            $page = end($pdf->pages);
            //extra event
            $this->eventManager->dispatch(
                'quotation_quotepdf_getpdf_before_comments',
                [
                    'renderer' => $this,
                    'page' => $page,
                    'quote' => $quote,
                    'store' => $store
                ]
            );
            /* Draw Comments */
//            if ($quote->getCustomerNoteNotify() && $quote->getCustomerNote() != '') {
//                $afterTotalsY = $this->y;
//                $this->y = $totalsY;
//                if ($this->newPage) {
//                    $this->y = self::NEW_PAGE_Y_VALUE;
//                }
//                $this->insertComments($page, $quote);
//                if ($afterTotalsY < $this->y) {
//                    $this->y = $afterTotalsY;
//                }
//            }
            //extra event
            $this->eventManager->dispatch(
                'quotation_quotepdf_getpdf_before_footer',
                [
                    'renderer' => $this,
                    'page' => $page,
                    'quote' => $quote,
                    'store' => $store
                ]
            );
            /* Get Footer Text */
            $footertext = $this->_scopeConfig->getValue(
                'cart2quote_pdf/quote/pdf_footer_text',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $quote->getStoreId()
            );
            $this->insertFooter($page, $footertext);
            if ($quote->getStoreId()) {
                $this->localeResolver->revert();
            }
            //extra event
            $this->eventManager->dispatch(
                'quotation_quotepdf_getpdf_after',
                [
                    'renderer' => $this,
                    'page' => $page,
                    'quote' => $quote,
                    'store' => $store
                ]
            );
        }
        //extra event
        $this->eventManager->dispatch(
            'quotation_quotepdf_getpdf_after_all',
            [
                'renderer' => $this
            ]
        );
        $this->_afterGetPdf();
        return $pdf;
		}
	}
    /**
     * Get array of quotes
     *
     * @return array
     */
    private function getQuotes()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->quotes;
		}
	}
    /**
     * Set array of quotes
     *
     * @param array $quotes
     * @return $this
     * @throws \Exception
     */
    private function setQuotes(array $quotes)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			foreach ($quotes as $quote) {
            if (!$quote instanceof \Cart2Quote\Quotation\Model\Quote) {
                throw new \Exception(__('Invalid quote class provided for the PDF. ' .
                    'Expected class \Cart2Quote\Quotation\Model\Quote'));
            }
        }
        $this->quotes = $quotes;
        return $this;
		}
	}
    /**
     * Draw section header
     *
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface $section
     * @return \Zend_Pdf_Page
     */
    private function _drawSectionHeader(
        \Zend_Pdf_Page $page,
        \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface $section
    ) {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->y -= 10;
        if ($this->y <= 45) {
            $page = $this->newPage(['table_header' => true]);
        }
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y + 10, 585, $this->y - 35);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
        //columns headers
        $lines[0][] = [
            'text' => $section->getLabel(),
            'feed' => 35,
            'font_size' => 15,
            'font' => 'bold',
        ];
        $lineBlock = ['lines' => $lines, 'height' => 5];
        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        return $page;
		}
	}
    /**
     * Draw line blocks
     *
     * @param \Zend_Pdf_Page $page
     * @param array $draw
     * @param array $pageSettings
     * @return \Zend_Pdf_Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function drawLineBlocks(\Zend_Pdf_Page $page, array $draw, array $pageSettings = [])
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			//extra event
        $this->eventManager->dispatch(
            'quotation_quotepdf_getpdf_before_drawlineblocks',
            [
                'renderer' => $this,
                'page' => $page,
                'draw' => $draw,
                'settings' => $pageSettings
            ]
        );
        return parent::drawLineBlocks($page, $draw, $pageSettings);
		}
	}
    /**
     * Draw header for item table
     *
     * @param \Zend_Pdf_Page $page
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Pdf_Exception
     */
    private function _drawHeader(\Zend_Pdf_Page $page)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			/* Add table head */
            $this->_setFontRegular($page, 10);
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));
            $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(5, $this->y, 585, $this->y - 25);
            $this->y -= 15;
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

            //columns headers
            $lines[0][] = [
                'text' => __('Quantity'),
                'feed' => 5,
                'align' => 'center',
                'font' => 'bold',
                'width' => 50,
                'font_size' => 12
            ];

            $lines[0][] = [
                'text' => __('Product'),
                'feed' => 75,
                'align' => 'center',
                'font' => 'bold',
                'width' => 340,
                'font_size' => 12
            ];

            $lines[0][] = [
                'text' => __('Each'),
                'feed' => 430,
                'align' => 'center',
                'font' => 'bold',
                'width' => 70,
                'font_size' => 12
            ];

            $lines[0][] = [
                'text' => __('Extended Cost'),
                'feed' => 500,
                'align' => 'center',
                'font' => 'bold',
                'width' => 85,
                'font_size' => 12
            ];

            $lineBlock = [
                'lines' => $lines,
                'height' => 5
            ];

            $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $this->y -= 10;
		}
	}
    /**
     * Draw disclaimer
     *
     * @param \Zend_Pdf_Page $page
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @throws \Zend_Pdf_Exception
     */
    private function drawDisclaimer(\Zend_Pdf_Page $page, $quote)
    {
        if (\Cart2Quote\License\Model\License::getInstance()->isValid()) {
            if ($quote->hasOptionalItems()) {
                $this->_setFontRegular($page, 7);
                $disclaimer = __('Products with * are optional');
                $page->drawText($disclaimer, 38, $this->y + 8);
            }
        }
	}
    /**
     * Get array of increments
     *
     * @param array $quotes
     * @return string
     */
    private function getIncrementId(array $quotes)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$incrementIds = [];
        foreach ($quotes as $quote) {
            $incrementIds[] = $quote->getIncrementId();
        }
        return implode("-", $incrementIds);
		}
	}
    /**
     * Set the correct store locale to the PDF
     *
     * @param int $storeId
     */
    private function setPdfLocale($storeId)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$locale = $this->localeResolver->emulate($storeId);
        $this->translate->setLocale($locale);
        $this->translate->loadData();
		}
	}
    /**
     * @return \Magento\Quote\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function createPrintQuote()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quote = $this->quoteFactory->create();
        $customerId = $this->customerSession->getCustomerId();
        $customer = $this->customerRepositoryInterface->getById($customerId);
        $quote->setPrintOnly(true);
        $quote->setIsActive(false);
        return $quote->assignCustomer($customer);
		}
	}
    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    private function createPrintQuotationQuote($quote)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$quoteModel = $this->quotationFactory->create();
        return $quoteModel->create($quote);
		}
	}
    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return \Zend_Pdf_Page
     */
    private function newPage(array $settings = [])
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			//extra event
        $this->eventManager->dispatch(
            'quotation_quotepdf_getpdf_before_new_page',
            [
                'renderer' => $this,
                'settings' => $settings
            ]
        );
        $page = parent::newPage($settings);
        //extra event
        $this->eventManager->dispatch(
            'quotation_quotepdf_getpdf_after_new_page',
            [
                'renderer' => $this,
                'page' => $page
            ]
        );
        return $page;
		}
	}

    protected function insertFooter(\Zend_Pdf_Page $page, $text)
    {
        $text = $this->_scopeConfig->getValue(
            'cart2quote_pdf/quote/pdf_footer_text',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $text = '';
        $boxTop = 65;
        $top = $boxTop + 20;
        $boxHeight = 20;
        $boxMargin = 20;
        $font = $this->_setFontRegular($page, 10);

        if ($this->y - 20 < 15) {
            $page = $this->newPage([]);
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        foreach ($this->string->split($text, 100, true, true) as $_value) {
            $boxHeight += 10;
        }
        //$page->drawRectangle($boxMargin, $boxTop, $page->getWidth() - $boxMargin, $boxTop + $boxHeight);
        $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.71,0,0));
        $page->setLineWidth(1);
        //$page->drawLine(30, $top-20, 570, $top-20);

        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.714, 0.012, 0.016));
        $page->setLineWidth(0.5);
        $page->drawRectangle(0, $top-20, 400, $top-10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
        $page->setLineColor(new \Zend_Pdf_Color_Rgb(0.5725, 0.5412, 0.5333));
        $page->drawRectangle(400, $top-20, 595, $top-10);


        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.20));
        foreach ($this->string->split($text, 100, true, true) as $_value) {
            $feed = $this->getAlignCenter(
                trim(strip_tags($_value)),
                $boxMargin,
                $page->getWidth() - ($boxMargin * 2),
                $font,
                10
            );

            $page->drawText(
                trim(strip_tags($_value)),
                $feed,
                $top,
                'UTF-8'
            );

            $top -= 10;
        }


        $this->insertAddress($page);
    }

    protected function insertAddress(&$page, $store = null)
    {
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $page->setLineWidth(0);
        $top = 45;

        $addr = $this->_scopeConfig->getValue(self::XML_PATH_SALES_PDF_INVOICE_PACKINGSLIP_ADDRESS) . "\n";
        $addr = str_replace("\nCanada", ' Canada', $addr);
        $this->_setFontBold($page, 10);
        $page->drawText(
            'Canada Satellite',
            5,
            $top,
            'UTF-8'
        );

        $top -= 10;

        $this->_setFontRegular($page, 10);
        foreach (explode("\n", $addr) as $value) {
            if ($value !== '') {
                $value = preg_replace('/<br[^>]*>/i', "", $value);
                foreach ($this->string->split($value, 55, true, true) as $_value) {
                    $page->drawText(
                        trim(strip_tags($_value)),
                        5,
                        $top,
                        'UTF-8'
                    );
                    $top -= 10;
                }
            }
        }

        /**
         * draw right block
         */
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 10);
        $page->drawText(
            'www.canadasatellite.ca',
            450,
            35,
            'UTF-8'
        );

        $page->drawText(
            $this->_scopeConfig->getValue('trans_email/ident_sales/email'),
            450,
            25,
            'UTF-8'
        );

        /**
         * draw middle block
         */
        $this->_setFontBold($page, 10);

        $page->drawText(
            'Toll Free:',
            239,
            35,
            'UTF-8'
        );

        $page->drawText(
            'Direct:',
            239,
            25,
            'UTF-8'
        );


        $this->_setFontRegular($page, 10);
        $page->drawText(
            '1 (403) 918-6300',
            275,
            25,
            'UTF-8'
        );
        $page->drawText(
            '1 (855) 552-2623',
            290,
            35,
            'UTF-8'
        );

        $this->y = 45;
    }

    protected function insertLogo(&$page, $store = null)
    {
        $this->y = $this->y ? $this->y : 815;
        $image = $this->_scopeConfig->getValue(
            'sales/identity/logo',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        if ($image) {
            $imagePath = '/sales/store/logo/' . $image;
//            if ($this->fileStorageDatabase->checkDbUsage() &&
//                !$this->_mediaDirectory->isFile($imagePath)
//            ) {
//                $this->fileStorageDatabase->saveFileToFilesystem($imagePath);
//            }
            if ($this->_mediaDirectory->isFile($imagePath)) {
                $image = \Zend_Pdf_Image::imageWithPath($this->_mediaDirectory->getAbsolutePath($imagePath));
                $top = 830;
                //top border of the page
                $widthLimit = 270;
                //half of the page width
                $heightLimit = 270;
                //assuming the image is not a "skyscraper"
                $width = 120;
                $height = 50;

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

                $y1 = $top - $height;
                $y2 = $top;
                $x1 = 5;
                $x2 = $x1 + $width;

                //coordinates after transformation are rounded by Zend
                $page->drawImage($image, $x1, $y1, $x2, $y2);

                $this->y = $y1 - 10;
            }
        }
    }
}
