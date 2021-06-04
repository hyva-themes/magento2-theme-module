<?php declare(strict_types=1);

namespace Hyva\Theme\StubViewModels;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class MultipleIdentities implements ArgumentInterface, IdentityInterface
{
    const TAGS = ['multiple_identities1', 'multiple_identities2'];

    public function getIdentities()
    {
        return self::TAGS;
    }
}
