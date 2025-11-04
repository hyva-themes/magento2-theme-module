<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Magento\Review\Model\Review;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hyva\Theme\ViewModel\Customer\ForgotPasswordButton
 * @covers \Hyva\Theme\ViewModel\Customer\CreateAccountButton
 * @covers \Hyva\Theme\ViewModel\Customer\LoginButton
 * @covers \Hyva\Theme\ViewModel\Customer\ReviewList
 */
class CustomerViewModelsTest extends TestCase
{
    public function testForgotPasswordButtonDisabledDefaultsToFalse(): void
    {
        $sut = ObjectManager::getInstance()->create(\Hyva\Theme\ViewModel\Customer\ForgotPasswordButton::class);
        $this->assertFalse($sut->disabled());
    }

    public function testCreateAccountButtonDisabledDefaultsToFalse(): void
    {
        $sut = ObjectManager::getInstance()->create(\Hyva\Theme\ViewModel\Customer\CreateAccountButton::class);
        $this->assertFalse($sut->disabled());
    }

    public function testLoginButtonDisabledDefaultsToFalse(): void
    {
        $sut = ObjectManager::getInstance()->create(\Hyva\Theme\ViewModel\Customer\LoginButton::class);
        $this->assertFalse($sut->disabled());
    }

    public function testAddressRegionProviderReturnsRegionJson(): void
    {
        $sut = ObjectManager::getInstance()->create(\Hyva\Theme\ViewModel\Customer\Address\RegionProvider::class);
        $regionJson = $sut->getRegionJson();
        $regionData = json_decode($regionJson, true);
        $this->assertNotSame('', $regionJson);
        $this->assertNotEmpty($regionData);
    }

    /**
     * @magentoDataFixture Magento/Review/_files/customer_review.php
     */
    public function testReturnsEmailsForReviews(): void
    {
        $sut = ObjectManager::getInstance()->create(\Hyva\Theme\ViewModel\Customer\ReviewList::class);
        /** @var Review $reviewFixture */
        $reviewFixture = ObjectManager::getInstance()->get(\Magento\Framework\Registry::class)->registry('review_data');

        $reviewToEmailMap = $sut->getCustomerEmailsForReviews([$reviewFixture]);

        $this->assertSame([$reviewFixture->getData('customer_id') => 'customer@example.com'], $reviewToEmailMap);
    }
}
