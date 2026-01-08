<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\PageBuilder\Block\GoogleMapsApi as GoogleMapsApiBlock;

/**
 * This class makes the GoogleMapsApi block methods available through a view model.
 * It does not use class inheritance, since otherwise it is not possible to composer-replace page-builder.
 */
class GoogleMapsApi implements ArgumentInterface
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var GoogleMapsApiBlock
     */
    private $subject;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    private function getSubject(): GoogleMapsApiBlock
    {
        if (!$this->subject) {
            if (!class_exists(GoogleMapsApiBlock::class)) {
                throw new \RuntimeException('GoogleMapsApiBlock not found - install magento/module-page-builder to use this class.');
            }
            $this->subject = $this->objectManager->get(GoogleMapsApiBlock::class);
        }
        return $this->subject;
    }

    public function getApiKey(): ?string
    {
        return $this->getSubject()->getApiKey();
    }

    public function getLibraryUrl(): string
    {
        return $this->getSubject()->getLibraryUrl();
    }

    public function getStyle(): ?string
    {
        return $this->getSubject()->getStyle();
    }

    public function getInvalidApiKeyMessage(): Phrase
    {
        return $this->getSubject()->getInvalidApiKeyMessage();
    }

    public function shouldIncludeGoogleMapsLibrary(): bool
    {
        return $this->getSubject()->shouldIncludeGoogleMapsLibrary();
    }
}
