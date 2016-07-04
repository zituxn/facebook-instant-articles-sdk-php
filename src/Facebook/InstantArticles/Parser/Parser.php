<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Parser;

use Facebook\InstantArticles\Transformer\Transformer;
use Facebook\InstantArticles\Elements\InstantArticle;

class Parser
{
    /**
     * @param \DOMDocument $document The document html of an Instant Article
     *
     * @return InstantArticle filled element that was parsed from the DOMDocument parameter
     */
    public function parse($document)
    {
        $json_file = file_get_contents(__DIR__ . '/instant-articles-rules.json');

        $instant_article = InstantArticle::create();
        $transformer = new Transformer();
        $transformer->loadRules($json_file);

        $transformer->transform($instant_article, $document);

        return $instant_article;
    }
}
