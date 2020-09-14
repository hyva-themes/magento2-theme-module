<?php
declare(strict_types=1);

namespace Wigman\Tailwind\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Checkout\Helper\Cart;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductPage implements ArgumentInterface
{
    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var Cart
     */
    private $cartHelper;

    /**
     * @param Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        Cart $cartHelper
    ) {
        $this->coreRegistry = $registry;
        $this->priceCurrency = $priceCurrency;
        $this->cartHelper = $cartHelper;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->coreRegistry->registry('product');
        }
        return $this->_product;
    }

    public function getShortDescription(): string
    {
        $product = $this->getProduct();

        if ($shortDescription = $product->getShortDescription()) {
            return $shortDescription;
        }

        if ($description = $product->getDescription()) {
            return $this->excerptFromDescription($description);
        }
        return "";
    }

    protected function excerptFromDescription(string $description): string
    {
        // if we have at least one <p></p>, take the first one as excerpt
        if ( $paragraphs = preg_split('#</p><p>|<p>|</p>#i', $description, -1, PREG_SPLIT_NO_EMPTY)) {
            return strip_tags($paragraphs[0]);
        }
        // otherwise, take the first sentence
        return explode('.', strip_tags($description))[0] . '.';
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->cartHelper->getAddUrl($product, $additional);
    }
    
    
    public function getCurrency()
    {
        return $this->priceCurrency->getCurrency()->getCurrencyCode();
    }
}
