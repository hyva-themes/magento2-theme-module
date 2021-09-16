<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\AutoComplete\Model\Adapter\Index;

use Magento\Elasticsearch\Model\Adapter\Index\Builder;

class AutoCompleteBuilder
{
    /**
     * @param Builder $subject
     * @param $result
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterBuild(Builder $subject, $result): array
    {
        $likeToken = $this->getLikeTokenizer();

        $result['analysis']['tokenizer'] = $likeToken;
        $result['analysis']['filter']['trigrams_filter'] = [
            'type' => 'ngram',
            'min_gram' => 3,
            'max_gram' => 3
        ];
        $result['analysis']['analyzer']['my_analyzer'] = [
            'type' => 'custom',
            'tokenizer' => 'standard',
            'filter' => [
                'lowercase', 'trigrams_filter'
            ]
        ];
        return $result;
    }

    /**
     * @return array
     */
    protected function getLikeTokenizer(): array
    {
        return [
            'default_tokenizer' => [
                'type' => 'ngram'
            ],
        ];
    }
}
