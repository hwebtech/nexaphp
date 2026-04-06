<?php

namespace app\models;

use core\DbModel;

class EmailLog extends DbModel
{
    public string $recipient = '';
    public string $subject = '';
    public string $body = '';
    public string $status = '';
    public ?string $error_message = null;

    public function rules(): array
    {
        return [
            'recipient' => [self::RULE_REQUIRED, self::RULE_EMAIL],
            'subject' => [self::RULE_REQUIRED],
            'body' => [self::RULE_REQUIRED],
        ];
    }

    public static function tableName(): string
    {
        return 'email_logs';
    }

    public function attributes(): array
    {
        return ['recipient', 'subject', 'body', 'status', 'error_message'];
    }

    public static function primaryKey(): string
    {
        return 'id';
    }
}
