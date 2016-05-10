<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Validators;

use Facebook\InstantArticles\Elements\Ad;
use Facebook\InstantArticles\Elements\Analytics;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Video;
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\Footer;
use Facebook\InstantArticles\Elements\Paragraph;
use Facebook\InstantArticles\Elements\SlideShow;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\AnimatedGIF;

/**
 *
 */
class InstantArticleValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantArticle()
    {
        $article =
            InstantArticle::create()
                ->withCanonicalUrl('')
                ->withHeader(Header::create())
                // Paragraph1
                ->addChild(
                    Paragraph::create()
                        ->appendText('Some text to be within a paragraph for testing.')
                )

                // Empty paragraph
                ->addChild(Paragraph::create())

                // Paragraph with only whitespace
                ->addChild(Paragraph::create()->appendText(" \n \t "))

                ->addChild(
                    // Image without src
                    Image::create()
                )

                ->addChild(
                    // Image with empty src
                    Image::create()->withURL('')
                )

                // Slideshow
                ->addChild(
                    SlideShow::create()
                        ->addImage(
                            Image::create()
                                ->withURL('https://jpeg.org/images/jpegls-home.jpg')
                        )
                        ->addImage(
                            // Image without src URL for image
                            Image::create()
                        )
                )

                // Ad
                ->addChild(Ad::create()->withSource('http://foo.com'))

                // Paragraph4
                ->addChild(
                    Paragraph::create()
                        ->appendText('Other text to be within a second paragraph for testing.')
                )

                // Analytics
                ->addChild(
                    // Empty fragment
                    Analytics::create()
                )

                // Empty Footer
                ->withFooter(Footer::create());

        $expected =
            '<!doctype html>'.
            '<html>'.
                '<head>'.
                    '<link rel="canonical" href=""/>'.
                    '<meta charset="utf-8"/>'.
                    '<meta property="op:generator" content="facebook-instant-articles-sdk-php"/>'.
                    '<meta property="op:generator:version" content="1.0.6"/>'.
                    '<meta property="op:markup_version" content="v1.0"/>'.
                '</head>'.
                '<body>'.
                    '<article>'.
                        '<p>Some text to be within a paragraph for testing.</p>'.
                        '<figure class="op-ad">'.
                            '<iframe src="http://foo.com"></iframe>'.
                        '</figure>'.
                        '<p>Other text to be within a second paragraph for testing.</p>'.
                    '</article>'.
                '</body>'.
            '</html>';

        $result = $article->render();
        $this->assertEquals($expected, $result);
    }
}
