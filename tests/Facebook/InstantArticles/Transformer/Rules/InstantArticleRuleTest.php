<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Transformer\Transformer;

class InstantArticleRuleTest extends \Facebook\Util\BaseHTMLTestCase
{
    private $config = array();
    private $html;

    public function setUp()
    {
        $configJSON =
            '{'.
                '"class": "InstantArticleRule",'.
                '"selector" : "head",'.
                '"properties" : {'.
                    '"article.canonical" : {'.
                        '"type" : "string",'.
                        '"selector" : "link",'.
                        '"attribute": "href"'.
                    '},'.
                    '"article.charset" : {'.
                        '"type" : "xpath",'.
                        '"selector" : "//meta[@charset]",'.
                        '"attribute": "charset"'.
                    '},'.
                    '"article.markup.version" : {'.
                        '"type" : "xpath",'.
                        '"selector" : "//meta[@property=\'op:markup_version\']",'.
                        '"attribute": "content"'.
                    '},'.
                    '"article.auto.ad" : {'.
                        '"type" : "xpath",'.
                        '"selector" : "//meta[@property=\'fb:use_automatic_ad_placement\']",'.
                        '"attribute": "content"'.
                    '},'.
                    '"article.style" : {'.
                        '"type" : "xpath",'.
                        '"selector": "//meta[@property=\'fb:article_style\']",'.
                        '"attribute": "content"'.
                    '}'.
                '}'.
            '}';
        $this->config = json_decode($configJSON, true);

        $this->html = file_get_contents(__DIR__ . '/instant-article-rule-test.html');
    }
    public function testMatchContext()
    {
        $rule = InstantArticleRule::createFrom($this->config);

        $document = new \DOMDocument('utf-8');
        $document->loadHTML($this->html);

        $head = $document->getElementsByTagName('head')->item(0);

        $this->assertTrue($rule->matchesContext(InstantArticle::create()));
        $this->assertTrue($rule->matchesNode($head));
        $this->assertTrue($rule->matches(InstantArticle::create(), $head));
    }

    public function testApply()
    {
        $rule = InstantArticleRule::createFrom($this->config);

        $document = new \DOMDocument('utf-8');
        $document->loadHTML($this->html);

        $head = $document->getElementsByTagName('head')->item(0);

        $instant_article = $rule->apply(new Transformer(), InstantArticle::create(), $head);
        invariant($instant_article instanceof InstantArticle, 'Not an Instant Article');
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');
        $expected =
            '<!doctype html>'.
            '<html>'.
              '<head>'.
                '<link rel="canonical" href="http://foo.com/article.html"/><meta charset="utf-8"/>'.
                '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                '<meta property="op:generator:version" content="1.0.0"/>'.
                '<meta property="op:generator:transformer:version" content="1.0.0"/>'.
                '<meta property="op:markup_version" content="v1.0"/>'.
              '</head>'.
              '<body>'.
                '<article>'.
                '</article>'.
              '</body>'.
            '</html>';
        $this->assertEqualsHtml($expected, $instant_article->render());
    }
}
