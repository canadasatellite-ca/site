<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Quote\Status\History;

/**
 * Class Validator
 *
 * @package Cart2Quote\Quotation\Model\Quote\Status\History
 */
class Validator
{
    use \Cart2Quote\Features\Traits\Model\Quote\Status\History\Validator {
        validate as private traitValidate;
    }

    /**
     * @var array
     */
    protected $requiredFields = ['parent_id' => 'Quote Id'];

    /**
     * Validate
     *
     * @param \Cart2Quote\Quotation\Model\Quote\Status\History $history
     * @return array
     */
    public function validate(\Cart2Quote\Quotation\Model\Quote\Status\History $history)
    {
        return $this->traitValidate($history);
    }
}
