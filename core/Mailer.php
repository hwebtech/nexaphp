<?php

namespace core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailException;

class Mailer
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = [
            'host' => $config['host'] ?? '',
            'port' => $config['port'] ?? 587,
            'user' => $config['user'] ?? '',
            'password' => $config['password'] ?? '',
            'encryption' => $config['encryption'] ?? 'tls',
            'from_email' => $config['from_email'] ?? '',
            'from_name' => $config['from_name'] ?? 'System',
        ];
    }

    public function send(string $to, string $subject, string $body)
    {
        $log = new \app\models\EmailLog();
        $log->recipient = $to;
        $log->subject = $subject;
        $log->body = $body;

        try {
            $mail = new PHPMailer(true);
            
            // Server settings
            $mail->SMTPDebug = 0; // Set to 3 for debugging
            $mail->isSMTP();
            $mail->Host       = $this->config['host'] ?: 'localhost';
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->config['user'] ?: '';
            $mail->Password   = $this->config['password'] ?: '';
            $mail->SMTPSecure = $this->config['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $this->config['port'] ?: 587;

            // Recipients
            $mail->setFrom($this->config['from_email'] ?: $this->config['user'], $this->config['from_name'] ?: 'System');
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            if ($mail->send()) {
                $log->status = 'sent';
                $log->save();
                return true;
            }
            
            throw new \Exception("Unknown mailer error");
        } catch (\Exception $e) {
            $log->status = 'failed';
            $log->error_message = $e->getMessage();
            $log->save();
            error_log("PHPMailer Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendBranded(string $to, string $subject, string $title, string $content, string $actionText = '', string $actionUrl = '')
    {
        $brandedBody = EmailTemplate::render($title, $content, $actionText, $actionUrl);
        return $this->send($to, $subject, $brandedBody);
    }
}
