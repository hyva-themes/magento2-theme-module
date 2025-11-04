<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
