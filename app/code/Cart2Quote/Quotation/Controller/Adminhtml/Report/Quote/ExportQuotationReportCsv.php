<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Report\Quote;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class ExportQuotationReportCsv
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Report\Quote
 */
class ExportQuotationReportCsv extends \Cart2Quote\Quotation\Controller\Adminhtml\Report\Quote\BaseReport
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $fileName = 'quotationreport.csv';
        $grid = $this->createGrid();

        return $this->_fileFactory->create($fileName, $grid->getCsvFile(), DirectoryList::VAR_DIR);
    }
}