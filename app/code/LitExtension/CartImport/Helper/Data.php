<?php
/**
 * @project: CartImport
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */

namespace LitExtension\CartImport\Helper;

class Data
{
    protected $_module;

    public function __construct(
        \Magento\Framework\Module\ResourceInterface $nhtfbd34a2b6e6a9fe8161f97dc435642609ac0bc29
    ) {
        $this->_module = $nhtfbd34a2b6e6a9fe8161f97dc435642609ac0bc29;
    }

    public function getVersion() {
        return $this->_module->getDataVersion('LitExtension_CartImport');
    }
}