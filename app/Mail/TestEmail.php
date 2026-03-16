<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ทดสอบส่งอีเมลจาก Laravel', // หัวข้ออีเมล
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.test', // ชี้ไปที่ไฟล์ Blade ที่เราจะสร้างในขั้นตอนถัดไป
        );
    }
}