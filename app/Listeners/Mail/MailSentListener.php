<?php

namespace App\Listeners\Mail;

use App\Models\Activity;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class MailSentListener
{
    public function handle(MessageSent $event): void
    {
        $message = $event->message;

        $to = '';
        $from = '';
        $subject = '';

        if (method_exists($message, 'getTo')) {
            $toAddresses = $message->getTo();
            if (is_array($toAddresses)) {
                $to = implode(', ', array_map(function ($addr) {
                    return $addr->getAddress();
                }, $toAddresses));
            }
        }

        if (method_exists($message, 'getFrom')) {
            $fromAddresses = $message->getFrom();
            if (is_array($fromAddresses)) {
                $from = implode(', ', array_map(function ($addr) {
                    return $addr->getAddress();
                }, $fromAddresses));
            }
        }

        if (method_exists($message, 'getSubject')) {
            $subject = $message->getSubject() ?? '';
        }

        Log::info('Email sent', [
            'to' => $to,
            'from' => $from,
            'subject' => $subject,
        ]);
    }
}
