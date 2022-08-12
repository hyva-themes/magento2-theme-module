<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Hyva\Theme\Model\Modal\ConfirmationBuilderInterface;
use Hyva\Theme\Model\Modal\ModalBuilderInterface;
use Hyva\Theme\Model\Modal\ModalBuilderFactory;
use Hyva\Theme\Model\Modal\ConfirmationBuilderFactory;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Modal implements ArgumentInterface
{
    public const DEFAULT_NAME = 'dialog';

    /**
     * @var ModalBuilderFactory
     */
    private $modalBuilderFactory;

    /**
     * @var ConfirmationBuilderFactory
     */
    private $confirmationBuilderFactory;

    private $usedNames = [];

    public function __construct(
        ModalBuilderFactory $modalBuilderFactory,
        ConfirmationBuilderFactory $confirmationBuilderFactory
    ) {
        $this->modalBuilderFactory        = $modalBuilderFactory;
        $this->confirmationBuilderFactory = $confirmationBuilderFactory;
    }

    private function getNewName(): string
    {
        return empty($this->usedNames) ? self::DEFAULT_NAME : uniqid(self::DEFAULT_NAME);
    }

    public function createModal(): ModalBuilderInterface
    {
        $name = $this->getNewName();

        $this->usedNames[] = $name;
        return $this->modalBuilderFactory->create(['data' => ['dialog-name' => $name]]);
    }

    /**
     * @param string|Phrase|null $message
     * @return ConfirmationBuilderInterface
     */
    public function confirm($message = null): ConfirmationBuilderInterface
    {
        $name = $this->getNewName();

        $this->usedNames[] = $name;

        $confirmation = $this->confirmationBuilderFactory->create(['data' => ['dialog-name' => $name]]);
        $confirmation->withTemplate('Hyva_Theme::modal/confirmation.phtml');

        if ($message) {
            $confirmation->withTitle($message);
        }

        return $confirmation;
    }
}
