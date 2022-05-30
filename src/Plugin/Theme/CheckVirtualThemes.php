<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Theme;

use Magento\Framework\View\Design\ThemeInterface;
use Magento\Theme\Model\ResourceModel\Theme as ThemeResource;
use Magento\Theme\Model\ResourceModel\Theme\Data\CollectionFactory as DbThemeCollectionFactory;
use Magento\Theme\Model\Theme as ThemeModel;
use Magento\Theme\Model\Theme\Data\Collection as FsThemeCollection;
use Magento\Theme\Model\Theme\Registration as ThemeRegistration;

class CheckVirtualThemes
{
    /**
     * @var DbThemeCollectionFactory
     */
    private $dbThemesFactory;

    /**
     * @var FsThemeCollection
     */
    private $fsThemes;

    /**
     * @var ThemeResource
     */
    private $themeResource;

    public function __construct(
        DbThemeCollectionFactory $dbThemesFactory,
        FsThemeCollection $fsThemes,
        ThemeResource $themeResource
    ) {

        $this->dbThemesFactory = $dbThemesFactory;
        $this->fsThemes        = $fsThemes;
        $this->themeResource   = $themeResource;
    }

    /**
     * Check if virtual themes exist in the filesystem and if so, set the type to physical.
     *
     * When the theme configuration page is opened in the Magento admin, it checks if themes are not present
     * in the filesystem, and if so, sets the `type` field for those to virtual. Sometimes that happens
     * by accident, and there is no way to correct the issue except by manually fixing the value in the db.
     * This plugin automatically corrects the theme type again when the theme actually exists in the filesystem.
     *
     * Reference Hyvä issue: https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/175
     * Reference upstream issue: https://github.com/magento/magento2/issues/4330
     *
     * @param ThemeRegistration $subject
     * @param ThemeRegistration $result
     * @return ThemeRegistration
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCheckPhysicalThemes(ThemeRegistration $subject, ThemeRegistration $result)
    {
        $this->checkVirtualThemes();

        return $result;
    }

    private function checkVirtualThemes(): void
    {
        $themes = $this->dbThemesFactory->create()->addTypeFilter(ThemeInterface::TYPE_VIRTUAL);
        /** @var $theme ThemeModel|ThemeInterface */
        foreach ($themes as $theme) {
            if ($this->fsThemes->hasTheme($theme)) {
                $theme->setType(ThemeInterface::TYPE_PHYSICAL);
                $this->themeResource->save($theme);
            }
        }
    }
}
