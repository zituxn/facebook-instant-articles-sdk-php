<?hh //decl
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Parser\Parser;

class AuthorRuleTest extends \Facebook\Util\BaseHTMLTestCase
{
    public function testCreateFromProperties()
    {
        $author_rule = AuthorRule::createFrom(
            array (
                "class" => "Facebook\\InstantArticles\\Transformer\\Rules\\AuthorRule",
                "selector" => "div.post-content > p > em",
                "properties" => array (
                    "author.url" => array (
                        "type" => "string",
                        "selector" => "a",
                        "attribute" => "href"
                    ),
                    "author.description" => array (
                        "type" => "string",
                        "selector" => "#text:nth-child(2)"
                    )
                )
            )
        );
        $this->assertEquals(get_class($author_rule), AuthorRule::getClassName());
    }

    public function testCreate()
    {
        $author_rule = AuthorRule::create()
            ->withSelector("div.post-content > p > em")
            ->withProperties(
                Vector { AuthorRule::PROPERTY_AUTHOR_URL, AuthorRule::PROPERTY_AUTHOR_NAME, },
                array(
                    AuthorRule::PROPERTY_AUTHOR_URL =>
                    array (
                        "type" => "string",
                        "selector" => "a",
                        "attribute" => "href"
                    ),
                    AuthorRule::PROPERTY_AUTHOR_NAME =>
                    array (
                        "type" => "string",
                        "selector" => "span"
                    ),
                )
            );
        $this->assertEquals($author_rule->getClassName(), AuthorRule::getClassName());
    }

    public function testExpectedNameWithLink()
    {
        $expectedName = "The Author";
        $html =
            '<header>'.
                '<h1>Article Title</h1>'.
                // The name is inside an <a> element
                "<address><a>$expectedName</a></address>".
            '</header>';

        $parser = new Parser();
        $instantArticle = $parser->parse($html);
        $author = $instantArticle->getHeader()->getAuthors()[0];

        $this->assertEqualsHtml($expectedName, $author->getName());
    }

    public function testExpectedNameWithoutLink()
    {
        $expectedName = "The Other Author";
        $html =
            '<header>'.
                '<h1>Article Title</h1>'.
                // The name is inside the <address> element
                "<address>$expectedName</address>".
            '</header>';

        $parser = new Parser();
        $instantArticle = $parser->parse($html);
        $author = $instantArticle->getHeader()->getAuthors()[0];

        $this->assertEqualsHtml($expectedName, $author->getName());
    }
}
