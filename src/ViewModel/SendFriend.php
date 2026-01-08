<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Helper\ImageFactory as ProductImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class SendFriend implements ArgumentInterface
{
    public const SMALL_IMAGE = 'product_small_image';

    private $productRepository;

    private $request;

    private $productImageFactory;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param RequestInterface $request
     * @param ProductImageFactory $productImageFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        RequestInterface $request,
        ProductImageFactory $productImageFactory
    ) {
        $this->productRepository = $productRepository;
        $this->request = $request;
        $this->productImageFactory = $productImageFactory;
    }

    /**
     * @return null|ProductInterface|Product
     * @throws NoSuchEntityException
     */
    public function getProduct(): ?ProductInterface
    {
        try {
            $id = (int)$this->request->getParam('id');
            return $this->productRepository->getById($id);
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    public function getImage(Product $product): Image
    {
        return $this->productImageFactory->create()->init($product, self::SMALL_IMAGE);
    }
}
