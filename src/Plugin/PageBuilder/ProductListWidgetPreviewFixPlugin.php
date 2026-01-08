<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\PageBuilder;

use Hyva\Theme\Service\CurrentTheme;
use Magento\CatalogWidget\Block\Product\ProductsList as ProductsListWidget;
use Magento\Framework\Escaper;

class ProductListWidgetPreviewFixPlugin
{
    /**
     * @var CurrentTheme
     */
    private $currentTheme;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        CurrentTheme $currentTheme,
        Escaper $escaper
    ) {
        $this->currentTheme = $currentTheme;
        $this->escaper = $escaper;
    }

    /**
     * Apply Hyvä frontend styles to product grid preview.
     */
    public function afterToHtml(ProductsListWidget $subject, string $result): string
    {
        if (! $this->currentTheme->isHyva()) {
            return $result;
        }
        $iframeId = uniqid();

        $initCarouselJs = $fixCarouselNavJs = '';
        if (strpos($result, 'widget-product-carousel') !== false) {
            $initCarouselJs = $subject->fetchView($subject->getTemplateFile('Magento_PageBuilder::widgets/carousel.phtml'));
            $fixCarouselNavJs = <<<EOT
      const carouselNav = doc.querySelector('.carousel-nav');
      if (carouselNav) {
        carouselNav.style.pointerEvents = 'auto';
      };
EOT;
        }
            $doc = <<<EOT
<!doctype html>
<html>
<head>
  <link  rel="stylesheet" type="text/css"  media="all" href="{$subject->getViewFileUrl('css/styles.css')}"/>
</head>
<body>
  <div style="pointer-events: none" data-content-type="products" data-appearance="carousel">$result</div>
  {$initCarouselJs}
</body>
</html>
EOT;

            return <<<EOT
<iframe id="{$this->escaper->escapeHtmlAttr($iframeId)}"
        srcdoc="{$this->escaper->escapeHtmlAttr($doc)}"
        style="width: 100%; border: 0;"></iframe>
<script>
(() => {
  // update the iframe height to match the content
  const iframe = document.getElementById('{$this->escaper->escapeJs($iframeId)}');
  iframe.addEventListener('load', () => {
    // wait until the iframe contents are done rendering.
    setTimeout(() => {
      const iframe = document.getElementById('{$this->escaper->escapeJs($iframeId)}');
      const doc = iframe.contentWindow.document;
      {$fixCarouselNavJs}
      const height = Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight);
      iframe.style.height = height + 'px';
    }, 50);
  });
})()
</script>
EOT;
    }
}
