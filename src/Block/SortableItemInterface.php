<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Block;

interface SortableItemInterface
{
    public const LABEL = 'label';
    public const PATH = 'path';
    public const SORT_ORDER = 'sort_order';
    public const SORT_ORDER_DEFAULT_VALUE = 1000;
    public const TEMPLATE = 'Hyva_Theme::sortable-item/link.phtml';

    public function getLabel(): string;

    public function getPath(): string;

    public function getSortOrder(): ?int;
}
