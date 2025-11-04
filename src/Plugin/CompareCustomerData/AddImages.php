<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\CompareCustomerData;

use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\CustomerData\CompareProducts;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class AddImages
{
    /**
     * @var ImageFactory
     */
    private $imageFactory;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ImageFactory $imageFactory, ProductRepository $productRepository)
    {
        $this->imageFactory = $imageFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * Iterates over the compare items and adds an image url to each item.
     *
     * @param CompareProducts $subject
     * @param array $result
     * @return array
     * @see CompareProducts::getSectionData
     */
    public function afterGetSectionData(CompareProducts $subject, array $result): array
    {
        /** @var array $compareItem */
        foreach ($result['items'] as &$compareItem) {
            try {
                /** @var Product $product */
                $product = $this->productRepository->getById((int)$compareItem['id']);
                $image = $this->imageFactory->create($product, 'product_comparison_list');
                $compareItem['image'] = $image->getImageUrl();
            } catch (NoSuchEntityException $exception) {
                continue;
            }
        }

        return $result;
    }
}
