<?php

namespace app\models;

use core\DbModel;

class SystemSetting extends DbModel
{
    public string $key_name = '';
    public string $key_value = '';

    public function rules(): array
    {
        return [
            'key_name' => [self::RULE_REQUIRED, [self::RULE_UNIQUE, 'class' => self::class]],
            'key_value' => [self::RULE_REQUIRED],
        ];
    }

    public static function tableName(): string
    {
        return 'system_settings';
    }

    public function attributes(): array
    {
        return ['key_name', 'key_value'];
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public static function get($key, $default = '')
    {
        $setting = self::findOne(['key_name' => $key]);
        return $setting ? $setting->key_value : $default;
    }
}
