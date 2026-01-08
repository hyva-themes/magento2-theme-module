<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Model\Template;

use Hyva\Theme\ViewModel\SvgIcons;
use Magento\Framework\Filter\SimpleDirective\ProcessorInterface;
use Magento\Framework\View\Asset\File\NotFoundException;

/**
 * A custom directive to parse {{icon}} variables to inline SVGs
 */
class IconProcessor implements ProcessorInterface
{
    /**
     * @var SvgIcons
     */
    protected $svgIcons;

    public function __construct(
        SvgIcons $svgIcons
    ) {
        $this->svgIcons = $svgIcons;
    }

    /**
     * Unique name of this directive.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'icon';
    }

    /**
     * Processes {{icon}} directives and turns them into inline SVG elements
     *
     * Valid formats:
     * {{icon "heroicons/solid/shopping-cart" classes="w-6 h-6" width=12 height=12}}
     *
     * Custom icons stored in `web/svg/` like `web/svg/cart.svg`:
     * {{icon "cart"}}
     *
     * @param mixed $path Template var, scalar or null if nothing has been passed to the directive.
     * @param string[] $parameters Additional parameters.
     * @param string|null $html HTML inside the directive.
     * @return string
     */
    public function process(
        $path,
        array $parameters,
        ?string $html
    ): string {
        // if no path provided -> remove the directive code
        if (!$path) {
            return '';
        }
        $attributes = [];
        $classes = $parameters['classes'] ?? '';
        $width = isset($parameters['width']) ? (int) $parameters['width'] : null;
        $height = isset($parameters['height']) ? (int) $parameters['height'] : null;

        if ($title = $parameters['title'] ?? null) {
            $attributes['title'] = $title;
        }

        try {
            return $this->svgIcons->renderHtml($path, $classes, $width, $height, $attributes);
        } catch (NotFoundException $error) {
            // if source file not found -> remove the directive code
            return '';
        }
    }

    /**
     * Default filters to apply if none provided in a template.
     *
     * @return string[]|null
     */
    public function getDefaultFilters(): ?array
    {
        return [];
    }
}
