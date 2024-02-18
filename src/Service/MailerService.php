<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private MailerInterface $mailer;
    private string $sender;

    public function __construct(MailerInterface $mailer, string $sender)
    {
        $this->mailer = $mailer;
        $this->sender = $sender;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmail(string $email, string $subject, string $message): void
    {
        $this->mailer->send((new Email())
            ->from($this->sender)
            ->to($email)
            ->subject($subject)
            ->text($message));
    }
}