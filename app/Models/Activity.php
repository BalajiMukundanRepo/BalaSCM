<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    const CREATE_CLIENT = 1;
    const UPDATE_CLIENT = 2;
    const ARCHIVE_CLIENT = 3;
    const DELETE_CLIENT = 4;
    const CREATE_INVOICE = 5;
    const UPDATE_INVOICE = 6;
    const ARCHIVE_INVOICE = 7;
    const DELETE_INVOICE = 8;
    const CREATE_PAYMENT = 9;
    const UPDATE_PAYMENT = 10;
    const ARCHIVE_PAYMENT = 11;
    const DELETE_PAYMENT = 12;
    const CREATE_PRODUCT = 13;
    const UPDATE_PRODUCT = 14;
    const ARCHIVE_PRODUCT = 15;
    const DELETE_PRODUCT = 16;
    const RESTORE_INVOICE = 17;
    const RESTORE_CLIENT = 18;
    const RESTORE_PAYMENT = 19;
    const RESTORE_PRODUCT = 20;

    protected $fillable = [
        "company_id", "user_id", "client_id", "invoice_id", "payment_id",
        "credit_id", "activity_type_id", "ip", "notes", "is_system",
    ];

    protected $casts = ["is_system" => "boolean"];

    public function user() { return $this->belongsTo(User::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function payment() { return $this->belongsTo(Payment::class); }
    public function company() { return $this->belongsTo(Company::class); }
    public function backup() { return $this->hasOne(Backup::class); }
}
