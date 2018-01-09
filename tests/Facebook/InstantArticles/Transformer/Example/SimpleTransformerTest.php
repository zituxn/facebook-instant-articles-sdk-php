<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer;

use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\Util\BaseHTMLTestCase;

class SimpleTransformerTest extends BaseHTMLTestCase
{
    public function testSelfTransformerContent()
    {
        $json_file = file_get_contents(__DIR__ . '/simple-rules.json');

        $instant_article = InstantArticle::create();
        $transformer = new Transformer();
        $transformer->loadRules($json_file);

        $html_file = file_get_contents(__DIR__ . '/simple.html');

        $transformer->transformString($instant_article, $html_file);
        $instant_article->addMetaProperty('op:generator:version', '1.0.0');
        $instant_article->addMetaProperty('op:generator:transformer:version', '1.0.0');
        $result = $instant_article->render('', true)."\n";
        $expected = file_get_contents(__DIR__ . '/simple-ia.html');

        //var_dump($result);
        // print_r($warnings);
        $this->assertEqualsHtml($expected, $result);
    }

    public function testDebugLog()
    {
        $expected = array(
            'Possible log levels: OFF, ERROR, INFO or DEBUG',
            '[INFO] Transformer initiated using encode [utf-8]',
        );
        $json_file = file_get_contents(__DIR__ . '/simple-rules.json');

        $instant_article = InstantArticle::create();
        $transformer = new Transformer();
        $transformer->loadRules($json_file);

        $html_file = file_get_contents(__DIR__ . '/simple.html');

        $transformer->setLogLevel(Transformer::LOG_DEBUG);
        $transformer->transformString($instant_article, $html_file);
        $result = array($transformer->getLogs()[0], $transformer->getLogs()[1]);

        $this->assertEqualsHtml($expected, $result);
    }
}
