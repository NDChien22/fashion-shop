<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $employeeName,
        public string $employeeCode,
        public string $username,
        public string $password,
        public string $roleLabel,
        public string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'FashionShop - Thong tin tai khoan nhan vien',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.employee-welcome',
            with: [
                'employeeName' => $this->employeeName,
                'employeeCode' => $this->employeeCode,
                'username' => $this->username,
                'password' => $this->password,
                'roleLabel' => $this->roleLabel,
                'loginUrl' => $this->loginUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
