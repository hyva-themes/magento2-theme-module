<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Bundle;

use Magento\Bundle\Helper\Catalog\Product\Configuration;
use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hyva\Theme\Plugin\Bundle\HideZeroFixedBundleSelectionPricePlugin
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 */
class HideZeroFixedBundleSelectionPricePluginTest extends TestCase
{
    private function executeSut(int $priceType, array $values): array
    {
        $objectManager = ObjectManager::getInstance();

        $product = $objectManager->create(Product::class);
        $product->setData('price_type', (string) $priceType);
        $item = $this->createStub(ItemInterface::class);
        $item->method('getProduct')->willReturn($product);

        $options = [['label' => 'Desk', 'value' => $values, 'has_html' => true]];

        return $objectManager->get(HideZeroFixedBundleSelectionPricePlugin::class)->afterGetBundleOptions(
            $objectManager->get(Configuration::class),
            $options,
            $item
        )[0]['value'];
    }

    private function price(float $amount): string
    {
        return ObjectManager::getInstance()->get(PriceCurrencyInterface::class)->format($amount);
    }

    /**
     * The selection value as built by Magento 2.4.7+ if the cart displays both prices
     */
    private function bothPricesValue(float $inclTax, float $exclTax): string
    {
        return '1 x Desk ' . $this->price($inclTax) . ' ' . __('Excl. tax:') . ' ' . $this->price($exclTax);
    }

    public function testHidesZeroSelectionPriceOfFixedPriceBundle(): void
    {
        $this->assertSame(
            ['1 x Desk'],
            $this->executeSut(Price::PRICE_TYPE_FIXED, ['1 x Desk ' . $this->price(0)])
        );
    }

    public function testHidesSelectionPriceRoundedToZeroOfFixedPriceBundle(): void
    {
        $this->assertSame(
            ['1 x Desk'],
            $this->executeSut(Price::PRICE_TYPE_FIXED, ['1 x Desk ' . $this->price(0.004)])
        );
    }

    public function testPrefixesNonZeroSelectionPriceOfFixedPriceBundle(): void
    {
        $this->assertSame(
            ['1 x Desk + ' . $this->price(12.1)],
            $this->executeSut(Price::PRICE_TYPE_FIXED, ['1 x Desk ' . $this->price(12.1)])
        );
    }

    public function testDoesNotChangeDynamicPriceBundle(): void
    {
        $values = ['1 x Desk ' . $this->price(0), '1 x Chair ' . $this->price(12.1)];

        $this->assertSame($values, $this->executeSut(Price::PRICE_TYPE_DYNAMIC, $values));
    }

    /**
     * @magentoConfigFixture current_store tax/cart_display/price 3
     */
    public function testHidesZeroSelectionPriceInclAndExclTax(): void
    {
        $this->assertSame(
            ['1 x Desk'],
            $this->executeSut(Price::PRICE_TYPE_FIXED, [$this->bothPricesValue(0, 0)])
        );
    }

    /**
     * Before Magento 2.4.7 only one price is appended, even if the cart displays both prices
     *
     * @magentoConfigFixture current_store tax/cart_display/price 3
     */
    public function testHidesZeroSelectionPriceWithoutExclTaxIfCartDisplaysBothPrices(): void
    {
        $this->assertSame(
            ['1 x Desk'],
            $this->executeSut(Price::PRICE_TYPE_FIXED, ['1 x Desk ' . $this->price(0)])
        );
    }

    /**
     * @magentoConfigFixture current_store tax/cart_display/price 3
     */
    public function testPrefixesNonZeroSelectionPriceInclAndExclTax(): void
    {
        $this->assertSame(
            ['1 x Desk + ' . $this->price(12.1) . ' ' . __('Excl. tax:') . ' + ' . $this->price(10)],
            $this->executeSut(Price::PRICE_TYPE_FIXED, [$this->bothPricesValue(12.1, 10)])
        );
    }
}
