<?hh // strict
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
use Facebook\InstantArticles\Validators\Type;

class Parser
{
    /**
     * @param string|DOMDocument $document The document html of an Instant Article
     * @param Transformer $transformer The Transformer instance to use. A fresh one will be created by default.
     *
     * @return InstantArticle filled element that was parsed from the DOMDocument parameter
     */
    public function parse(\DOMDocument $document, ?Transformer $transformer = null): InstantArticle
    {
        $json_file = file_get_contents(__DIR__ . '/instant-articles-rules.json');

        $instant_article = InstantArticle::create();

        if ($transformer === null) {
            $transformer = new Transformer();
        }
        $transformer->loadRules($json_file);
        $transformer->transform($instant_article, $document);

        return $instant_article;
    }

    public function parseString(string $content, ?Transformer $transformer = null, string $encoding = "utf-8"): InstantArticle
    {
        $libxml_previous_state = libxml_use_internal_errors(true);
        $document = new \DOMDocument('1.0');
        if (function_exists('mb_convert_encoding')) {
            $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', $encoding));
        } else {
            // wrap the content with charset meta tags
            $document->loadHTML(
                '<html><head>' .
                '<meta http-equiv="Content-Type" content="text/html; charset=' . $encoding . '">' .
                '</head><body>' . $content . '</body></html>'
            );
        }
        libxml_clear_errors();
        libxml_use_internal_errors($libxml_previous_state);
        return $this->parse($document, $transformer);
    }
}
