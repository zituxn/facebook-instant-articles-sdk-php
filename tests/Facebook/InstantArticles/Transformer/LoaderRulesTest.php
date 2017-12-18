<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer;

use Facebook\InstantArticles\Elements\InstantArticle;

use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Transformer\Rules\ConfigurationSelectorRule;
use Facebook\InstantArticles\Transformer\Rules\BoldRule;
use Facebook\InstantArticles\Transformer\Rules\H1Rule;
use Facebook\InstantArticles\Transformer\Rules\ItalicRule;
use Facebook\InstantArticles\Transformer\Rules\ParagraphRule;
use Facebook\InstantArticles\Transformer\Rules\TextNodeRule;

class LoaderRulesTest extends \Facebook\Util\BaseHTMLTestCase
{
    public function testLoad()
    {
        $json_file = file_get_contents(__DIR__ . '/loader-test-rules.json');

        $transformer = new Transformer();
        $transformer->loadRules($json_file);
        $rules = $transformer->getRules();

        $selectorNames = vec[];
        foreach ($rules as $rule) {
            invariant($rule instanceof ConfigurationSelectorRule, "Expected ConfigurationSelectorRules");
            $selectorNames[] = $rule->getSelector();
        }

        $expectedNames =
            vec[
                null,
                'html',
                'h3,h4,h5,h6',
                'img',
                'blockquote.instagram-media',
            ];
        $this->assertEquals($expectedNames, $selectorNames);
    }
}
