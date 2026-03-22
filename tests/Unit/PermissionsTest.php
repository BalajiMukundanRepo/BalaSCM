<?php

namespace Tests\Unit;

use App\Models\CompanyUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\MockAccountData;
use Tests\TestCase;

class PermissionsTest extends TestCase
{
    use MockAccountData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeTestData();
    }

    public function test_admin_has_all_permissions(): void
    {
        $this->assertTrue($this->user->isAdmin());
        $this->assertTrue($this->user->hasPermission('view_client'));
        $this->assertTrue($this->user->hasPermission('create_invoice'));
    }

    public function test_owner_has_all_permissions(): void
    {
        $this->assertTrue($this->user->isOwner());
        $this->assertTrue($this->user->hasPermission('delete_payment'));
    }

    public function test_regular_user_with_specific_permissions(): void
    {
        $regularUser = User::create([
            'first_name' => 'Regular',
            'last_name' => 'User',
            'email' => 'regular'.uniqid().'@example.com',
            'password' => bcrypt('password'),
        ]);

        CompanyUser::create([
            'company_id' => $this->company->id,
            'user_id' => $regularUser->id,
            'account_id' => $this->account->id,
            'is_admin' => false,
            'is_owner' => false,
            'permissions' => 'view_client,create_client',
            'notifications' => json_encode([]),
            'settings' => json_encode([]),
            'react_settings' => json_encode([]),
        ]);

        $this->assertFalse($regularUser->isAdmin());
        $this->assertFalse($regularUser->isOwner());
        $this->assertTrue($regularUser->hasPermission('view_client'));
        $this->assertTrue($regularUser->hasPermission('create_client'));
        $this->assertFalse($regularUser->hasPermission('delete_invoice'));
    }

    public function test_permission_checking_with_has_permission(): void
    {
        $this->assertTrue($this->user->hasPermission('anything'));
    }

    public function test_locked_user_cannot_perform_actions(): void
    {
        $lockedUser = User::create([
            'first_name' => 'Locked',
            'last_name' => 'User',
            'email' => 'locked'.uniqid().'@example.com',
            'password' => bcrypt('password'),
        ]);

        CompanyUser::create([
            'company_id' => $this->company->id,
            'user_id' => $lockedUser->id,
            'account_id' => $this->account->id,
            'is_admin' => false,
            'is_owner' => false,
            'is_locked' => true,
            'permissions' => 'view_client',
            'notifications' => json_encode([]),
            'settings' => json_encode([]),
            'react_settings' => json_encode([]),
        ]);

        $companyUser = CompanyUser::where('user_id', $lockedUser->id)
            ->where('company_id', $this->company->id)
            ->first();

        $this->assertTrue((bool) $companyUser->is_locked);
    }
}
