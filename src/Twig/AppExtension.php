<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('preview', [$this, 'preview']),
        ];
    }

    /**
     * Get blog preview text (Text before break line).
     *
     * @param string $content
     *
     * @return string
     */
    public function preview(string $content): string
    {
        $breakPoint = strpos($content, '<div style="page-break-after:always"><span style="display:none">&nbsp;</span></div>');

        if (false !== $breakPoint) {
            $content = substr($content, 0, $breakPoint);
        }

        return $content;
    }
}