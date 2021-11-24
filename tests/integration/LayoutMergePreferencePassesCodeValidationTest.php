<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Magento\Framework\Code\Validator\ConstructorIntegrity as ConstructorIntegrityValidator;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class LayoutMergePreferencePassesCodeValidationTest extends TestCase
{
    public function testPassesSetupDiCompileCodeValidation(): void
    {
        $constructorValidator = ObjectManager::getInstance()->get(ConstructorIntegrityValidator::class);

        $this->assertTrue($constructorValidator->validate(\Hyva\Theme\Framework\View\Model\Layout\Merge::class));
    }
}
