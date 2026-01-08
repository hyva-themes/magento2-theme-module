<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Escaper;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class EscapeHtmlAllowingAnchorAttributes implements ArgumentInterface
{
    /**
     * The most important safe anchor attributes.
     */
    public const ANCHOR_ATTRIBUTES = [
        'href',
        'hreflang',
        'referrerpolicy',
        'rel',
        'target',
        'type',
        'accesskey',
        'class',
        'dir',
        'enterkeyhint',
        'hidden',
        'id',
        'inert',
        'lang',
        'popover',
        'role',
        'tabindex',
        'title',
        'translate',
        'aria-haspopup',
        'aria-hidden',
        'aria-label',
        'aria-busy',
        'aria-live',
        'aria-relevant',
        'aria-atomic',
        'aria-controls',
        'aria-describedby',
        'aria-description',
        'aria-details',
    ];

    /**
     * Dummy property to make PHPStorm static code analysis happy
     */
    private $allowedAttributes = [];

    public function escapeHtml($data, $allowedTags = null)
    {
        $origEscaper = new Escaper();
        $addAllowedAttributes = function ($allowedAttributes) {
            $this->allowedAttributes = $allowedAttributes;
        };
        $addAllowedAttributes->call($origEscaper, self::ANCHOR_ATTRIBUTES);

        return $origEscaper->escapeHtml($data, $allowedTags);
    }
}
