<?php

namespace Tests\Integration;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\MockAccountData;
use Tests\TestCase;

class CompanyLedgerTest extends TestCase
{
    use MockAccountData;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeTestData();
    }

    public function test_creating_invoice_tracks_amount(): void
    {
        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'status_id' => Invoice::STATUS_DRAFT,
            'amount' => 100.00,
            'balance' => 100.00,
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'amount' => 100.00,
            'balance' => 100.00,
        ]);
    }

    public function test_creating_payment_tracks_amount(): void
    {
        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'status_id' => Invoice::STATUS_SENT,
            'amount' => 200.00,
            'balance' => 200.00,
        ]);

        $payment = Payment::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'status_id' => Payment::STATUS_COMPLETED,
            'amount' => 200.00,
            'applied' => 200.00,
            'date' => now()->format('Y-m-d'),
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'amount' => 200.00,
            'status_id' => Payment::STATUS_COMPLETED,
        ]);
    }

    public function test_balance_calculations(): void
    {
        $this->client->balance = 0;
        $this->client->save();

        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'status_id' => Invoice::STATUS_SENT,
            'amount' => 500.00,
            'balance' => 500.00,
        ]);

        $this->client->balance += $invoice->amount;
        $this->client->save();

        $this->assertEquals(500.00, $this->client->fresh()->balance);

        $payment = Payment::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'user_id' => $this->user->id,
            'status_id' => Payment::STATUS_COMPLETED,
            'amount' => 300.00,
            'applied' => 300.00,
            'date' => now()->format('Y-m-d'),
        ]);

        $this->client->balance -= $payment->amount;
        $this->client->paid_to_date += $payment->amount;
        $this->client->save();

        $this->assertEquals(200.00, $this->client->fresh()->balance);
        $this->assertEquals(300.00, $this->client->fresh()->paid_to_date);
    }
}
