<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class FooterTest extends \Facebook\Util\BaseHTMLTestCase
{
    public function testRenderEmpty(): void
    {
        $footer = Footer::create();

        $expected = '';

        $rendered = $footer->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic(): void
    {
        $footer =
            Footer::create()
                ->withCredits(Vector {'Some plaintext credits.'});

        $expected =
            '<footer>'.
                '<aside>'.
                    'Some plaintext credits.'.
                '</aside>'.
            '</footer>';

        $rendered = $footer->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithParagraphCredits(): void
    {
        $footer =
            Footer::create()
                ->withCredits(Vector {
                    Paragraph::create()->appendText('Some paragraph credits.'),
                    Paragraph::create()->appendText('Other paragraph credits.'),
                });

        $expected =
            '<footer>'.
                '<aside>'.
                    '<p>'.
                        'Some paragraph credits.'.
                    '</p>'.
                    '<p>'.
                        'Other paragraph credits.'.
                    '</p>'.
                '</aside>'.
            '</footer>';

        $rendered = $footer->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithParagraphCreditsAppend(): void
    {
        $footer =
            Footer::create()
                ->addCredit(
                    Paragraph::create()->appendText('Some paragraph credits.')
                )
                ->addCredit(
                    Paragraph::create()->appendText('Other paragraph credits.')
                );

        $expected =
            '<footer>'.
                '<aside>'.
                    '<p>'.
                        'Some paragraph credits.'.
                    '</p>'.
                    '<p>'.
                        'Other paragraph credits.'.
                    '</p>'.
                '</aside>'.
            '</footer>';

        $rendered = $footer->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithCopyright(): void
    {
        $small = Small::create();
        $small->appendText('2016 Facebook');
        $footer =
            Footer::create()
                ->withCopyright($small);

        $expected =
            '<footer>'.
                '<small>'.
                    '2016 Facebook'.
                '</small>'.
            '</footer>';

        $rendered = $footer->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithCopyrightSmallElement(): void
    {
        $small = Small::create();
        $small
            ->appendText("2016 ")
            ->appendText(
                Anchor::create()
                    ->withHref('https://facebook.com')
                    ->appendText('Facebook')
            );
        $footer =
            Footer::create()
                ->withCopyright($small);

        $expected =
            '<footer>'.
                '<small>'.
                    '2016 <a href="https://facebook.com">Facebook</a>'.
                '</small>'.
            '</footer>';

        $rendered = $footer->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithRelatedArticles(): void
    {
        $small = Small::create();
        $small->appendText('2016 Facebook');
        $footer =
            Footer::create()
                ->withCopyright($small)
                ->withRelatedArticles(
                    RelatedArticles::create()
                        ->addRelated(RelatedItem::create()->withURL('http://related.com/1'))
                        ->addRelated(RelatedItem::create()->withURL('http://related.com/2'))
                        ->addRelated(RelatedItem::create()->withURL('http://sponsored.com/1')->enableSponsored())
                );

        $expected =
            '<footer>'.
                '<small>'.
                    '2016 Facebook'.
                '</small>'.
                '<ul class="op-related-articles">'.
                    '<li><a href="http://related.com/1"></a></li>'.
                    '<li><a href="http://related.com/2"></a></li>'.
                    '<li data-sponsored="true"><a href="http://sponsored.com/1"></a></li>'.
                '</ul>'.
            '</footer>';

        $rendered = $footer->render();
        $this->assertEqualsHtml($expected, $rendered);
    }
}
