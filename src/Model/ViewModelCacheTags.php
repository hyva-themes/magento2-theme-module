<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use function array_map as map;
use function array_merge as merge;
use function array_unique as unique;

/**
 * Record cache tags from the view models created by the view model registry, so they can be passed to the page cache.
 */
class ViewModelCacheTags
{
    /**
     * @var IdentityInterface[]
     */
    private $viewModels = [];

    public function get(): array
    {
        // wait with collecting the identities until they are needed to catch all of them
        return unique(merge([], ...map(function (IdentityInterface $viewModel): array {
            return $viewModel->getIdentities();
        }, $this->viewModels)));
    }

    public function collectFrom(ArgumentInterface $viewModel): void
    {
        if ($viewModel instanceof IdentityInterface) {
            $this->viewModels[] = $viewModel;
        }
    }
}
