<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * A registry that can return instances of any view model. They no longer need to be passed to each block via layout XML
 *
 * Available in templates as `$viewModels`. Uses the object manager internally, no need to duplicate its instance cache.
 */
class ViewModelRegistry
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ViewModelCacheTags
     */
    private $viewModelCacheTags;

    public function __construct(ObjectManagerInterface $objectManager, ViewModelCacheTags $viewModelCacheTags)
    {
        $this->objectManager      = $objectManager;
        $this->viewModelCacheTags = $viewModelCacheTags;
    }

    /**
     * Returns view model instance for given FQN
     *
     * @template T
     * @param string-class<T> $viewModelClass Fully qualified class name (FQN)
     * @return T
     * @throw InvalidViewModelClass if class not found or not a view model
     */
    public function require(string $viewModelClass): ArgumentInterface
    {
        try {
            $object = $this->objectManager->get($viewModelClass);
        } catch (\Exception $e) {
            throw InvalidViewModelClass::notFound($viewModelClass, $e);
        }
        if (!$object instanceof ArgumentInterface) {
            throw InvalidViewModelClass::notAViewModel($viewModelClass);
        }

        $this->viewModelCacheTags->collectFrom($object);

        return $object;
    }
}
