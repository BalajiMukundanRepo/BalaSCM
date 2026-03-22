<?php

namespace App\DataMapper;

class CompanySettings
{
    public string $name = '';
    public string $address1 = '';
    public string $address2 = '';
    public string $city = '';
    public string $state = '';
    public string $postal_code = '';
    public string $country_id = '';
    public string $phone = '';
    public string $email = '';
    public string $website = '';
    public string $logo = '';
    public string $currency_id = '1';
    public string $language_id = '1';
    public string $timezone_id = '15';
    public string $date_format_id = '1';
    public string $payment_terms = '';
    public string $invoice_terms = '';
    public string $invoice_footer = '';
    public string $quote_terms = '';
    public string $quote_footer = '';
    public bool $auto_archive_invoice = false;
    public bool $auto_archive_quote = false;
    public string $lock_invoices = 'off';
    public bool $auto_email_invoice = true;
    public bool $auto_convert_quote = true;
    public bool $inclusive_taxes = false;
    public string $translations = '';
    public string $counter_number_applied = 'when_saved';
    public string $invoice_number_pattern = '';
    public int $invoice_number_counter = 1;
    public string $quote_number_pattern = '';
    public int $quote_number_counter = 1;
    public string $client_number_pattern = '';
    public int $client_number_counter = 1;
    public string $credit_number_pattern = '';
    public int $credit_number_counter = 1;
    public string $payment_number_pattern = '';
    public int $payment_number_counter = 1;
    public string $custom_value1 = '';
    public string $custom_value2 = '';
    public string $custom_value3 = '';
    public string $custom_value4 = '';
    public string $pdf_variables = '';
    public string $email_style = 'light';
    public string $email_subject_invoice = '';
    public string $email_template_invoice = '';
    public string $email_subject_quote = '';
    public string $email_template_quote = '';
    public string $email_subject_payment = '';
    public string $email_template_payment = '';
    public string $reply_to_email = '';
    public string $reply_to_name = '';
    public string $bcc_email = '';
    public bool $all_pages_header = true;
    public bool $all_pages_footer = true;
    public string $entity_send_time = '6';

    public static function defaults(): self
    {
        $settings = new self();
        $settings->currency_id = '1';
        $settings->language_id = '1';
        $settings->timezone_id = '15';
        $settings->date_format_id = '1';
        $settings->email_style = 'light';
        $settings->counter_number_applied = 'when_saved';
        $settings->lock_invoices = 'off';
        $settings->auto_email_invoice = true;
        $settings->auto_convert_quote = true;
        $settings->all_pages_header = true;
        $settings->all_pages_footer = true;
        $settings->entity_send_time = '6';
        $settings->invoice_number_counter = 1;
        $settings->quote_number_counter = 1;
        $settings->client_number_counter = 1;
        $settings->credit_number_counter = 1;
        $settings->payment_number_counter = 1;

        return $settings;
    }
}
