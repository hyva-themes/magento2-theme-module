<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
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

            $doc = <<<EOT
<!doctype html>
<html>
<head>
  <link  rel="stylesheet" type="text/css"  media="all" href="{$subject->getViewFileUrl('css/styles.css')}"/>
</head>
<body>
  $result
</body>
</html>
EOT;

            return <<<EOT
<iframe id="{$this->escaper->escapeHtmlAttr($iframeId)}"
        srcdoc="{$this->escaper->escapeHtmlAttr($doc)}"
        style="width: 100%; border: 0; pointer-events: none"></iframe>
<script>
(() => {
  // update the iframe height to match the content
  setTimeout(() => {
  const iframe = document.getElementById('{$this->escaper->escapeJs($iframeId)}');
  const doc = iframe.contentWindow.document;
  const height = Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight);
  iframe.style.height = height + 'px';
}, 50)
})()
</script>
EOT;
    }
}
