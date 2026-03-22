<?php

namespace App\Policies;

use App\Models\BankTransaction;
use App\Models\User;

class BankTransactionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('view_bank_transaction');
    }

    public function view(User $user, BankTransaction $bankTransaction): bool
    {
        $company = $user->getCompany();

        return $company && $company->id === $bankTransaction->company_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasPermission('create_bank_transaction');
    }

    public function update(User $user, BankTransaction $bankTransaction): bool
    {
        $company = $user->getCompany();

        return $user->isAdmin() || ($user->hasPermission('edit_bank_transaction') && $company && $company->id === $bankTransaction->company_id);
    }

    public function delete(User $user, BankTransaction $bankTransaction): bool
    {
        $company = $user->getCompany();

        return $user->isAdmin() || ($user->hasPermission('delete_bank_transaction') && $company && $company->id === $bankTransaction->company_id);
    }
}
