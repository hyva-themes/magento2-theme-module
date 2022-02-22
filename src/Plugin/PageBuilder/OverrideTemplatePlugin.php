<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\PageBuilder;

use DOMDocument;
use Hyva\Theme\Service\CurrentTheme;
use Magento\Framework\Filter\Template as FrameworkTemplateFilter;
use Magento\Framework\View\ConfigInterface;
use Magento\PageBuilder\Plugin\Filter\TemplatePlugin;

class OverrideTemplatePlugin
{
    // matches data-background-images=\"(any character except quote)\"
    const BACKGROUND_IMAGE_PATTERN = '/data-background-images=\"([^"]*)\"/si';

    // matches any html element with the attribute data-content-type="html"
    const HTML_CONTENT_TYPE_PATTERN = '/<(\w+)\s[^>]*data-content-type=\"html\"[^>]*>[\s\S]*\1>/si';

    /**
     * @var CurrentTheme
     */
    protected $theme;

    /**
     * @var ConfigInterface
     */
    private $viewConfig;

    /**
     * @param CurrentTheme $theme
     * @param ConfigInterface $viewConfig
     */
    public function __construct(CurrentTheme $theme, ConfigInterface $viewConfig)
    {
        $this->theme = $theme;
        $this->viewConfig = $viewConfig;
    }

    /**
     * On Hyvä frontends, replace this plugin to prevent attributes like `@resize.window=""` from being removed.
     *
     * @param TemplatePlugin $subject
     * @param \Closure $proceed
     * @param FrameworkTemplateFilter $interceptor
     * @param string $result
     *
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundAfterFilter(
        TemplatePlugin $subject,
        \Closure $proceed,
        FrameworkTemplateFilter $interceptor,
        $result
    ): string {
        if ($this->theme->isHyva() && is_string($result)) {
            return $this->applyFilters($result);
        }
        return $proceed($interceptor, $result);
    }

    /**
     * @param string $htmlContent
     * @return string
     */
    protected function applyFilters(string $htmlContent): string
    {
        // Validate if the filtered result requires background image processing
        if (preg_match_all(self::BACKGROUND_IMAGE_PATTERN, $htmlContent, $matches, PREG_SET_ORDER)) {
            // Each match contains two keys:
            // 0 => the entire matching snippet
            // 1 => the first regex group match, in this case the json object with bg image data
            $htmlContent = $this->generateBackgroundImageStyles($htmlContent, $matches);
        }

        // Validate if the filtered result requires html decoding
        if (preg_match_all(self::HTML_CONTENT_TYPE_PATTERN, $htmlContent, $matches, PREG_SET_ORDER)) {
            $htmlContent = $this->decodeHtmlContent($htmlContent, $matches);
        }

        return $htmlContent;
    }

    /**
     * @param string $htmlContent
     * @param array $htmlContentMatches
     * @return void
     */
    private function decodeHtmlContent(string $htmlContent, array $htmlContentMatches): string
    {
        foreach ($htmlContentMatches as $htmlContentMatch) {
            //phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            $decodedHtml = html_entity_decode($htmlContentMatch[0]);

            $htmlContent = str_replace($htmlContentMatch[0], $decodedHtml, $htmlContent);
        }

        return $htmlContent;
    }

    /**
     * Generate the CSS for any background images on the page.
     *
     * @param string $htmlContent
     * @param array $backgroundMatches
     * @return string
     */
    private function generateBackgroundImageStyles(string $htmlContent, array $backgroundMatches): string
    {
        $domDocument = new DomDocument();
        $generatedStyles = '';

        foreach ($backgroundMatches as $backgroundMatch) {

            // input: {\"desktop_image\":\"imagepath.jpg\", \"mobile_image\": \"imagepath.jpg\"}"
            // output: ["desktop_image" => "imagepath.jpg", "mobile_imag" => "imagepath.jpg"];
            //phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            $backgroundImages = json_decode(stripslashes(html_entity_decode($backgroundMatch[1])), true);

            if (!empty($backgroundImages)) {
                $dataAttribute = 'data-image-id="' . uniqid() . '"';
                $cssDataAttributeSelector = '[' . $dataAttribute . ']';

                // generate responsive styles with the data-image-id as CSS selector
                $generatedStyles .= $this->generateCssFromImages($cssDataAttributeSelector, $backgroundImages);

                // replace the data-background-images attribute with a unique data-image-id="id" attribute
                $htmlContent = str_replace($backgroundMatch[0], $dataAttribute, $htmlContent);
            }
        }

        // create a <style> tag, insert the generated CSS string
        $styleElement = $domDocument->createElement(
            'style',
            $generatedStyles
        );
        $styleElement->setAttribute('type', 'text/css');

        $domDocument->appendChild($styleElement);

        // append the generated <style/> tag to the htmlContent
        return $htmlContent . $domDocument->saveHTML();
    }

    /**
     * Generate CSS based on the images array from our attribute.
     *
     * Copied from @see \Magento\PageBuilder\Model\Filter\Template::generateCssFromImages and modified.
     *
     * @param string $elementClass
     * @param array $images
     *
     * @return string
     */
    private function generateCssFromImages(string $elementClass, array $images) : string
    {
        $css = [];
        if (isset($images['desktop_image'])) {
            $css[$elementClass] = [
                'background-image' => 'url(' . $images['desktop_image'] . ')',
            ];
        }
        if (isset($images['mobile_image']) && $this->getMediaQuery('mobile')) {
            $css[$this->getMediaQuery('mobile')][$elementClass] = [
                'background-image' => 'url(' . $images['mobile_image'] . ')',
            ];
        }
        if (isset($images['mobile_image']) && $this->getMediaQuery('mobile-small')) {
            $css[$this->getMediaQuery('mobile-small')][$elementClass] = [
                'background-image' => 'url(' . $images['mobile_image'] . ')',
            ];
        }
        return $this->cssFromArray($css);
    }

    /**
     * Generate a CSS string from an array.
     *
     * Copied from @see \Magento\PageBuilder\Model\Filter\Template::cssFromArray.
     *
     * @param array $css
     *
     * @return string
     */
    private function cssFromArray(array $css) : string
    {
        $output = '';
        foreach ($css as $selector => $body) {
            if (is_array($body)) {
                $output .= $selector . ' {';
                $output .= $this->cssFromArray($body);
                $output .= '}';
            } else {
                $output .= $selector . ': ' . $body . ';';
            }
        }
        return $output;
    }

    /**
     * Generate the mobile media query from view configuration.
     *
     * Copied from @see \Magento\PageBuilder\Model\Filter\Template::getMediaQuery.
     *
     * @param string $view
     * @return null|string
     */
    private function getMediaQuery(string $view) : ?string
    {
        $breakpoints = $this->viewConfig->getViewConfig()->getVarValue(
            'Magento_PageBuilder',
            'breakpoints/' . $view . '/conditions'
        );
        if ($breakpoints && count($breakpoints) > 0) {
            $mobileBreakpoint = '@media only screen ';
            foreach ($breakpoints as $key => $value) {
                $mobileBreakpoint .= 'and (' . $key . ': ' . $value . ') ';
            }
            return rtrim($mobileBreakpoint);
        }
        return null;
    }
}
