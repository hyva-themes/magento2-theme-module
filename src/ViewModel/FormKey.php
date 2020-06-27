<?php
declare(strict_types=1);

namespace Wigman\Tailwind\ViewModel;

use Magento\Framework\Data\Form\FormKey as FormKeyData;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class FormKey implements ArgumentInterface
{
    /**
     * @var FormKeyData
     */
    protected $formKey;

    /**
     * @param FormKeyData $formKey
     */
    public function __construct(
        FormKeyData $formKey
    ) {
        $this->formKey = $formKey;
    }

    /**
     * Get form key
     *
     * @return string
     * @throws LocalizedException
     */
    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }
}
