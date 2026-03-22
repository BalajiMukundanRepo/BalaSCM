<?php

namespace App\Http\ValidationRules\Company;

use App\Models\Company;
use Illuminate\Contracts\Validation\Rule;

class ValidExpenseMailbox implements Rule
{
    protected ?int $companyId;

    public function __construct(?int $companyId = null)
    {
        $this->companyId = $companyId;
    }

    public function passes($attribute, $value): bool
    {
        if (empty($value)) {
            return true;
        }

        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $allowedDomains = ['invoicemanager.com', 'expense.invoicemanager.com'];
        $domain = substr(strrchr($value, '@'), 1);

        if (! in_array($domain, $allowedDomains)) {
            return false;
        }

        $query = Company::where('expense_mailbox', $value);

        if ($this->companyId) {
            $query->where('id', '!=', $this->companyId);
        }

        return $query->count() === 0;
    }

    public function message(): string
    {
        return 'The expense mailbox is invalid or already in use by another company.';
    }
}
