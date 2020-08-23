<?php
/**
 * Invoice Ninja (https://invoiceninja.com)
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2020. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://opensource.org/licenses/AAL
 */

namespace App\Http\ValidationRules\Invoice;

use App\Libraries\MultiDB;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class LockedInvoiceRule
 * @package App\Http\ValidationRules
 */
class LockedInvoiceRule implements Rule
{
    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->checkIfInvoiceLocked(); //if it exists, return false!
    }

    /**
     * @return string
     */
    public function message()
    {
        return ctrans('texts.locked_invoice');
    }

    /**
     * @param $email
     *
     * //off,when_sent,when_paid
     * 
     * @return bool
     */
    private function checkIfInvoiceLocked() : bool
    {
        $lock_invoices = $this->invoice->client->getSetting('lock_invoices');

        switch ($lock_invoices) {
            case 'off':
                return true;
                break;
            case 'when_sent':
                if($this->invoice->status_id == Invoice::STATUS_SENT)
                    return false;

                return true;

                break;
            case 'when_paid':
                if($this->invoice->status_id == Invoice::STATUS_PAID)
                    return false;

                return true;
                break;
            default:
                return true;
                break;
        }

    }
}