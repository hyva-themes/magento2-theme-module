<?php declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Hyva\Theme\Model\Modal\ModalBuilderInterface;
use Hyva\Theme\Model\Modal\ModalBuilderFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Modal implements ArgumentInterface
{
    const DEFAULT_NAME = 'dialog';

    /**
     * @var ModalBuilderFactory
     */
    private $modalBuilderFactory;

    private $usedNames = [];

    public function __construct(ModalBuilderFactory $modalBuilderFactory)
    {
        $this->modalBuilderFactory = $modalBuilderFactory;
    }

    private function getNewName(): string
    {
        return empty($this->usedNames) ? self::DEFAULT_NAME : uniqid('dialog');
    }

    public function createModal(): ModalBuilderInterface
    {
        $name = $this->getNewName();
        $this->usedNames[] = $name;
        return $this->modalBuilderFactory->create(['data' => ['dialog-name' => $name]]);
    }
}
