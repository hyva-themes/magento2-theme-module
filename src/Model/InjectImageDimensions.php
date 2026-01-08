<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface as DirectoryRead;
use Magento\Framework\Image\Factory as ImageFactory;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

class InjectImageDimensions
{

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var UrlInterface
     */
    private $baseUrl;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string|null
     */
    private $memoizedMediaBaseUrl = null;

    /**
     * @var DirectoryRead|null
     */
    private $memoizedMediaDir = null;

    public function __construct(
        Filesystem $filesystem,
        UrlInterface $baseUrl,
        ImageFactory $imageFactory,
        LoggerInterface $logger
    ) {
        $this->filesystem = $filesystem;
        $this->baseUrl = $baseUrl;
        $this->imageFactory = $imageFactory;
        $this->logger = $logger;
    }

    public function addNativeImageDimensionsToPageBuilderImages(string $result): string
    {
        if (preg_match_all('#<img [^>]*src="([^"]+)" [^>]*data-use-native-image-dimensions="true"[^>]*>#ms', $result, $m, PREG_SET_ORDER)) {
            $originals = [];
            $replacements = [];
            foreach (array_filter($m, [$this, 'isImageWithMediaBaseUrl']) as [$imgTag, $src]) {
                try {
                    if ($imgTagWithDimensions = $this->injectNativeDimensionsToImgTag($imgTag, $src)) {
                        $originals[] = $imgTag;
                        $replacements[] = str_replace(' data-use-native-image-dimensions="true"', '', $imgTagWithDimensions);
                    }
                } catch (\Exception $exception) {
                    $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                }
            }
            $result = str_replace($originals, $replacements, $result);
        }

        return $result;
    }

    public function injectNativeDimensionsToImgTag(string $imgTag, string $src): string
    {
        $pathInMedia = substr($src, strlen($this->getMediaBaseUrl()));
        if (! $this->getMediaDir()->isExist($pathInMedia)) {
            return '';
        }

        $imgTagWithoutDimensions = preg_replace('# (?:width|height)="[^"]*"#', '', $imgTag);
        $image = $this->imageFactory->create($this->getMediaDir()->getAbsolutePath($pathInMedia));

        return sprintf(
            '<img width="%d" height="%d" %s',
            $image->getOriginalWidth(),
            $image->getOriginalHeight(),
            substr($imgTagWithoutDimensions, 4)
        );
    }

    private function isImageWithMediaBaseUrl(array $match): bool
    {
        return strpos($match[1] ?? '-', $this->getMediaBaseUrl()) === 0;
    }

    private function getMediaBaseUrl(): string
    {
        if (! $this->memoizedMediaBaseUrl) {
            $this->memoizedMediaBaseUrl = $this->baseUrl->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        }
        return $this->memoizedMediaBaseUrl;
    }

    private function getMediaDir(): DirectoryRead
    {
        if (! isset($this->memoizedMediaDir)) {
            $this->memoizedMediaDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }
        return $this->memoizedMediaDir;
    }
}
