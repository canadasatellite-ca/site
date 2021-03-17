<?php
	
	namespace Digit\OrderButtons\Model\Pdf;
	
	use Magento\Sales\Model\Order\Pdf\Shipment;
	
	class Packingslip extends Shipment
	{
		/**
			* Return PDF document
			*
			* @param  \Magento\Sales\Model\Order[] $shipments
			*
			* @return \Zend_Pdf
		*/
		public function getPdf($shipments = [])
		{
			$this->_beforeGetPdf();
			$this->_initRenderer('shipment');
			
			$pdf = new \Zend_Pdf();
			$this->_setPdf($pdf);
			
			foreach ($shipments as $shipment) {
				if ($shipment->getStoreId()) {
					$this->_localeResolver->emulate($shipment->getStoreId());
					$this->_storeManager->setCurrentStore($shipment->getStoreId());
				}
				$page = $this->newPage();
				$this->_setFontBold($page, 10);
				$shipment->setOrder($shipment);
				/* Add image */
				$this->insertLogo($page, $shipment->getStore());
				/* Add address */
				$this->insertAddress($page, $shipment->getStore());
				/* Add head */
				$this->insertOrder(
                $page,
                $shipment,
                $this->_scopeConfig->isSetFlag(
				self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
				$shipment->getStoreId()
                )
				);
				/* Add table */
				$this->_drawHeader($page);
				/* Add body */
				foreach ($shipment->getAllItems() as $item) {
					if ($item->getParentItem()) {
						continue;
					}
					
					/* Keep it compatible with the invoice */
					$item->setQty($item->getQtyOrdered());
					$item->setOrderItem($item);
					
					/* Draw item */
					$this->_drawItem($item, $page, $shipment);
					$page = end($pdf->pages);
				}
				/* Add totals */
				//$this->insertTotals($page, $shipment);
				if ($shipment->getStoreId()) {
					$this->_localeResolver->revert();
				}
			}
			$this->_afterGetPdf();
			return $pdf;
		}
	}
