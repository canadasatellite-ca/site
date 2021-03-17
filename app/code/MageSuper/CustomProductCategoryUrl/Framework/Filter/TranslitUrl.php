<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\CustomProductCategoryUrl\Framework\Filter;

/**
 * Url compatible translit filter
 *
 * Process string based on convertation table
 */
class TranslitUrl extends \Magento\Framework\Filter\TranslitUrl
{
    /**
     * Filter value
     *
     * @param string $string
     * @return string
     */
    public function filter($string)
    {
        $string = preg_replace('#[^0-9a-z/]+#i', '-', self::filter1($string));
        //$string = strtolower($string);
        $string = trim($string, '-');

        return $string;
    }

    public function filter1($string)
    {
        $string = strtr($string, $this->getConvertTable());
        return '"libiconv"' == ICONV_IMPL ? iconv(
            \Magento\Framework\Stdlib\StringUtils::ICONV_CHARSET,
            'ascii//ignore//translit',
            $string
        ) : $string;
    }
}
