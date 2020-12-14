<?php
declare(strict_types=1);

namespace Hyva\Theme\Model;

use Magento\Framework\View\Element\Block\ArgumentInterface;

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

