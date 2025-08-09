<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService
{
    public function __construct(private array $config) {}

    public function send(string $toEmail, string $toName, string $subject, string $html, ?string $text = null): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $this->config['host'];
            $mail->Port       = $this->config['port'];
            $mail->SMTPAuth   = !empty($this->config['username']);
            if ($mail->SMTPAuth) {
                $mail->Username = $this->config['username'];
                $mail->Password = $this->config['password'];
            }
            if (!empty($this->config['encryption']) && $this->config['encryption'] !== 'null') {
                $mail->SMTPSecure = $this->config['encryption'];
            }

            $mail->setFrom($this->config['from']['address'], $this->config['from']['name']);
            $mail->addAddress($toEmail, $toName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $text ?: strip_tags($html);

            return $mail->send();
        } catch (Exception $e) {
            error_log('[Mailer] ' . $e->getMessage());
            return false;
        }
    }
}