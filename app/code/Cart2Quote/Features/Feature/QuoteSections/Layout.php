<?php
 namespace Cart2Quote\Features\Feature\QuoteSections; final class Layout extends \Cart2Quote\License\Plugin\AbstractPlugin { public final function aroundSetBlock(\Magento\Framework\View\Layout $rOxje, callable $XZtE_, $G1k9A, $dWRTy) { if (!($dWRTy instanceof \Cart2Quote\Quotation\Block\Adminhtml\Quote\Sections && !$this->{"\x69\163\x41\x6c\x6c\157\167\x65\144"}())) { goto tPOKd; } return null; tPOKd: return $XZtE_($G1k9A, $dWRTy); } }