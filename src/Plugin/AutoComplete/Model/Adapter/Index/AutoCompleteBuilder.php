<?php
namespace Hyva\Theme\Plugin\AutoComplete\Model\Adapter\Index;

class AutoCompleteBuilder  {

    public function afterBuild(\Magento\Elasticsearch\Model\Adapter\Index\Builder $subject, $result)
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


    protected function getLikeTokenizer()
    {
        $tokenizer = [
            'default_tokenizer' => [
                'type' => 'ngram'
            ],
        ];
        return $tokenizer;
    }
}
