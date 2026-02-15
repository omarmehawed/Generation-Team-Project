<?php

namespace App\Helpers;

use ArPHP\I18N\Arabic;

class ArabicHtmlFactory
{
    public static function convert($text)
    {
        $arabic = new Arabic();
        // Convert to glyphs (reshaped)
        $text = $arabic->utf8Glyphs($text);
        return $text;
    }
}
