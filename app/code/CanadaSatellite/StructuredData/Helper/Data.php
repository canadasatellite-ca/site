<?php

namespace CanadaSatellite\StructuredData\Helper;

use Magento\Theme\Block\Html\Header\Logo;
use Mageplaza\Core\Helper\AbstractData as CoreHelper;

/**
 * Class Data
 * @package Mageplaza\Seo\Helper
 */
class Data extends CoreHelper
{

    /**
     * Create structure data script
     *
     * @param $data
     * @param string $prefixComment
     * @param string $subfixComment
     *
     * @return string
     */
    public function createStructuredData($data, $prefixComment, $subfixComment = '')
    {
        if ($prefixComment != null) {
            $applicationLdJson = '<script type="application/ld+json" id="' . $prefixComment . '">' . json_encode(
                    $data,
                    JSON_PRETTY_PRINT
                ) . '</script>';
        } else {
            $applicationLdJson = '<script type="application/ld+json">' . json_encode(
                    $data,
                    JSON_PRETTY_PRINT
                ) . '</script>';
        }
        $applicationLdJson .= $subfixComment;

        return $applicationLdJson;
    }

    /**
     * get Logo image url
     *
     * @return string
     */
    public function getLogo()
    {
        $logo = $this->objectManager->get(Logo::class);

        return $logo->getLogoSrc();
    }
}
