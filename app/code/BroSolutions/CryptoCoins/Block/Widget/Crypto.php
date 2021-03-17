<?php

namespace BroSolutions\CryptoCoins\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Crypto
 * @package BroSolutions\CryptoCoins\Block\Widget
 */
class Crypto extends Template implements BlockInterface
{

    protected $_template = "BroSolutions_CryptoCoins::widget/crypto.phtml";

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData('crypto_coins_title');
    }

    /**
     * @return string|int|null
     */
    public function getPriceChangePercent()
    {
        return $this->getData('crypto_coins_price_change_percent');
    }

    /**
     * @return string|int|null
     */
    public function getPriceChange()
    {
        return $this->getData('crypto_coins_price_change');
    }

    /**
     * @return string|int|null
     */
    public function getHighPriceChange()
    {
        return $this->getData('crypto_coins_high_price_change');
    }

    /**
     * @return string|int|null
     */
    public function getLowPriceChange()
    {
        return $this->getData('crypto_coins_low_price_change');
    }

    /**
     * @return string|int|null
     */
    public function getSymbol()
    {
        return $this->getData('crypto_coins_symbol');
    }

    /**
     * @return string|int|null
     */
    public function getLimit()
    {
        $limit = $this->getData('crypto_coins_limit');

        if (!$limit) {
            return 1000000;
        }

        return $limit;
    }

    /**
     * @return string|int|null
     */
    public function getCtaLink()
    {
        return $this->getData('crypto_coins_cta_link');
    }

    /**
     * @return string|int|null
     */
    public function getCtaText()
    {
        return $this->getData('crypto_coins_cta_text');
    }
}
