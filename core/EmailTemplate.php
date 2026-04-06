<?php

namespace core;

class EmailTemplate
{
    public static function render($title, $content, $actionText = '', $actionUrl = '')
    {
        $appName = getenv('APP_NAME') ?: 'HWeb Framework';
        $primaryColor = '#1e1b4b'; // Dark Navy
        $bgLight = '#f8fafc';

        $buttonHtml = '';
        if ($actionText && $actionUrl) {
            $buttonHtml = <<<HTML
                <tr>
                    <td align="center" style="padding: 30px 0;">
                        <table border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" style="border-radius: 8px;" bgcolor="{$primaryColor}">
                                    <a href="{$actionUrl}" target="_blank" style="font-size: 16px; font-family: sans-serif; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 8px; display: inline-block; font-weight: 600;">{$actionText}</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
HTML;
        }

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$title}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        body { width: 100% !important; height: 100% !important; padding: 0 !important; margin: 0 !important; background-color: {$bgLight}; font-family: sans-serif; }
        .content-table { max-width: 600px !important; margin: 0 auto; }
    </style>
</head>
<body>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" bgcolor="{$bgLight}" style="padding: 40px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="content-table">
                    <tr>
                        <td align="center" style="padding: 0 20px;">
                            <h1 style="color: {$primaryColor}; margin: 0;">{$appName}</h1>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" bgcolor="{$bgLight}">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="content-table" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <tr>
                        <td align="left" style="padding: 40px;">
                            <h2 style="margin: 0 0 20px 0; font-size: 24px; color: {$primaryColor};">{$title}</h2>
                            <div style="font-size: 16px; line-height: 24px; color: #475569;">
                                {$content}
                            </div>
                        </td>
                    </tr>
                    {$buttonHtml}
                    <tr>
                        <td align="left" style="padding: 0 40px 40px; font-size: 14px; color: #94a3b8;">
                            <p style="margin: 0;">Regards,<br>{$appName} Team</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }
}
