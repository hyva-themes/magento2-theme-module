<?php

/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */


declare(strict_types=1);

namespace Hyva\Theme\Block\SendFriend;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Framework\View\Element\Template\Context;


class Product extends \Magento\Framework\View\Element\Template
{
    /**
     * Small image value.
     *
     * @var string
     */
    const SMALL_IMAGE = 'product_small_image';

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var ImageFactory
     */
    protected $imageFactory;


    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        ImageFactory $imageFactory,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->imageFactory = $imageFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get request
     *
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get the product from id
     *
     * preferred to not use registry as it is deprecated
     *
     * @return object
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProduct(): object
    {
        $id = $this->getRequest()->getParam('id', null);
        return $this->productRepository->getById($id);
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function getImage($product)
    {
        $imageHelper = $this->imageFactory->create()->init($product, self::SMALL_IMAGE);
        return $imageHelper;
    }
}
