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

class InteractiveRuleTest extends \Facebook\Util\BaseHTMLTestCase
{
    private $config = array();
    private $iframe;

    public function setUp()
    {
        $configJSON = <<<'JSON'
{
    "class": "InteractiveRule",
    "selector" : "iframe",
    "properties" : {
        "interactive.url" : {
            "type" : "string",
            "selector" : "iframe",
            "attribute": "src"
        },
        "interactive.width" : {
            "type" : "int",
            "selector" : "iframe",
            "attribute": "width"
        },
        "interactive.height" : {
            "type" : "int",
            "selector" : "iframe",
            "attribute": "height"
        },
        "interactive.iframe" : {
            "type" : "children",
            "selector" : "iframe"
        }
    }
}
JSON;
        $this->config = json_decode($configJSON, true);

        $this->html = file_get_contents(__DIR__ . '/instant-article-rule-test.html');

        $document = new \DOMDocument('utf-8');
        $this->iframe = $document->createElement('iframe');
        $div = $document->createElement('div');
        $h1 = $document->createElement('h1');

        $h1->appendChild($document->createTextNode('simple title'));
        $div->appendChild($h1);
        $this->iframe->appendChild($div);
    }


    public function testMatchContext()
    {
        $rule = InteractiveRule::createFrom($this->config);

        $this->assertTrue($rule->matchesContext(InstantArticle::create()));
        $this->assertTrue($rule->matchesNode($this->iframe));
        $this->assertTrue($rule->matches(InstantArticle::create(), $this->iframe));
    }

    public function testApply()
    {
        $rule = InteractiveRule::createFrom($this->config);

        $instant_article = $rule->apply(new Transformer(), InstantArticle::create(), $this->iframe);
        invariant($instant_article instanceof InstantArticle, 'Not an Instant Article');
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');
        $expected =
            '<!doctype html>'.
            '<html>'.
              '<head>'.
                '<link rel="canonical" href=""/>'.
                '<meta charset="utf-8"/>'.
                '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                '<meta property="op:generator:version" content="1.0.0"/>'.
                '<meta property="op:generator:transformer:version" content="1.0.0"/>'.
                '<meta property="op:markup_version" content="v1.0"/>'.
              '</head>'.
              '<body>'.
                '<article>'.
                  '<figure class="op-interactive">'.
                    '<iframe class="no-margin">'.
                      '<div>'.
                        '<h1>simple title</h1>'.
                      '</div>'.
                    '</iframe>'.
                  '</figure>'.
                '</article>'.
              '</body>'.
            '</html>';
        $this->assertEqualsHtml($expected, $instant_article->render());
    }
}
