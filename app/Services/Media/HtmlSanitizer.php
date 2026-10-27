<?php

namespace App\Services\Media;

use HTMLPurifier;
use HTMLPurifier_Config;

/**
 * Allowlist based HTML sanitiser used for every piece of rich text that is
 * authored in the admin panel (article bodies, FAQ answers, page content).
 *
 * The same sanitiser is applied before the value is written to the database
 * and again right before the markup is rendered, so a value that somehow
 * bypassed the write path can never reach the browser.
 */
class HtmlSanitizer
{
    /**
     * Tags (and attributes) an editor is allowed to use.
     *
     * @var string
     */
    public const ALLOWED_TAGS = 'a[href|title|target|rel], p, br, hr, h2, h3, h4, strong, b, em, i, u, s, sub, sup, '
        .'ul, ol, li, blockquote, img[src|alt|title|width|height], '
        .'table, thead, tbody, tfoot, tr, th, td, caption, code, pre, span, div, small, abbr, cite';

    private ?HTMLPurifier $purifier = null;

    /**
     * Strip every tag or attribute that is not explicitly allowed.
     */
    public function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        return $this->purifier()->purify($html);
    }

    /**
     * Remove every tag and return readable plain text (used for excerpts and
     * JSON-LD answers).
     */
    public function toPlainText(?string $html): string
    {
        $text = preg_replace('#</(p|div|h[1-6]|li|tr|td|th|blockquote|pre|figcaption)>#i', ' ', $this->sanitize($html)) ?? '';
        $text = str_ireplace(['<br>', '<br/>', '<br />'], ' ', $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    }

    private function purifier(): HTMLPurifier
    {
        if ($this->purifier instanceof HTMLPurifier) {
            return $this->purifier;
        }

        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache.SerializerPath', $this->cachePath());
        $config->set('HTML.Allowed', self::ALLOWED_TAGS);
        $config->set('Attr.AllowedFrameTargets', ['_blank' => true]);
        $config->set('URI.AllowedSchemes', [
            'http' => true,
            'https' => true,
            'mailto' => true,
            'tel' => true,
        ]);
        $config->set('Core.Encoding', 'UTF-8');
        $config->set('AutoFormat.RemoveEmpty', true);
        $config->set('Output.FlashCompat', false);

        return $this->purifier = new HTMLPurifier($config);
    }

    private function cachePath(): string
    {
        $path = storage_path('framework/cache/htmlpurifier');

        if (! is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return $path;
    }
}
