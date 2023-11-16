<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme;

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
        $reviewFixture = ObjectManager::getInstance()->get(\Magento\Framework\Registry::class)->registry('review_data');

        $reviewToEmailMap = $sut->getCustomerEmailsForReviews([$reviewFixture]);

        $this->assertSame([$reviewFixture->getId() => 'customer@example.com'], $reviewToEmailMap);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testReturnsCustomerEmailById(): void
    {
        $sut = ObjectManager::getInstance()->create(\Hyva\Theme\ViewModel\Customer\ReviewList::class);
        $this->assertSame('customer@example.com', $sut->getEmailByCustomerId(1));
    }
}
