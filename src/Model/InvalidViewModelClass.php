<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use Magento\Framework\View\Element\Block\ArgumentInterface;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction

class InvalidViewModelClass extends \OutOfBoundsException
{
    
    public static function notFound(string $viewModelClass, \Exception $previous = null): self
    {
        return new self("Class $viewModelClass not found.", 0, $previous);
    }

    public static function notAViewModel(string $viewModelClass): self
    {
        return new self(
            implode(
                "\n",
                [
                    "Class $viewModelClass is not a view model.",
                    "Only classes that implement " . ArgumentInterface::class . " can be used as view model",
                ]
            )
        );
    }
}
