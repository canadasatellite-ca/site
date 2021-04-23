<?php
namespace MageSuper\Casat\Plugin\Directory\Model;

class Currency{
    function afterGetConfigBaseCurrencies(\Magento\Directory\Model\Currency $currency, $result)
    {
        $result[]='USD';
        $result[]='EUR';
        return array_unique($result);
    }
}