<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class CiteTest extends \Facebook\Util\BaseHTMLTestCase
{
    public function testRenderEmpty(): void
    {
        $cite = Cite::create();

        $expected = '';

        $rendered = $cite->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic(): void
    {
        $cite =
            Cite::create()
                ->appendText('Citation simple text.');

        $expected =
            '<cite>'.
                'Citation simple text.'.
            '</cite>';

        $rendered = $cite->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithPosition(): void
    {
        $cite =
            Cite::create()
                ->withPosition(Caption::POSITION_ABOVE)
                ->appendText('Citation simple text.');

        $expected =
            '<cite class="op-vertical-above">'.
                'Citation simple text.'.
            '</cite>';

        $rendered = $cite->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithTextAlign(): void
    {
        $cite =
            Cite::create()
                ->withTextAlignment(Caption::ALIGN_LEFT)
                ->appendText('Citation simple text.');

        $expected =
            '<cite class="op-left">'.
                'Citation simple text.'.
            '</cite>';

        $rendered = $cite->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithVerticalAlign(): void
    {
        $cite =
          Cite::create()
            ->withVerticalAlignment(Caption::VERTICAL_TOP)
            ->appendText('Citation simple text.');

        $expected =
          '<cite class="op-vertical-top">' .
          'Citation simple text.' .
          '</cite>';

        $rendered = $cite->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithPositionAndAlignment(): void
    {
        $cite =
            Cite::create()
                ->withPosition(Caption::POSITION_ABOVE)
                ->withTextAlignment(Caption::ALIGN_LEFT)
                ->withVerticalAlignment(Caption::VERTICAL_TOP)
                ->appendText('Citation simple text.');

        $expected =
            '<cite class="op-vertical-above op-left op-vertical-top">'.
                'Citation simple text.'.
            '</cite>';

        $rendered = $cite->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithUnescapedHTML(): void
    {
        $cite =
            Cite::create()
                ->appendText(
                    '<b>Some</b> text to be <i>within</i> a <em>paragraph</em> for <strong>testing.</strong>'
                );

        $expected =
            '<cite>'.
                '&lt;b&gt;Some&lt;/b&gt; text to be &lt;i&gt;within&lt;/i&gt; a'.
                ' &lt;em&gt;paragraph&lt;/em&gt; for &lt;strong&gt;testing.&lt;/strong&gt;'.
            '</cite>';

        $rendered = $cite->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithFormattedText(): void
    {
        $cite =
            Cite::create()
                ->appendText(Bold::create()->appendText('Some'))
                ->appendText(' text to be ')
                ->appendText(Italic::create()->appendText('within'))
                ->appendText(' a ')
                ->appendText(Italic::create()->appendText('paragraph'))
                ->appendText(' for ')
                ->appendText(Bold::create()->appendText('testing.'));

        $expected =
            '<cite>'.
                '<b>Some</b> text to be <i>within</i> a <i>paragraph</i> for <b>testing.</b>'.
            '</cite>';

        $rendered = $cite->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithLink(): void
    {
        $cite =
            Cite::create()
                ->appendText('Some ')
                ->appendText(
                    Anchor::create()
                        ->withHref('http://foo.com')
                        ->appendText('link')
                )
                ->appendText('.');

        $expected =
            '<cite>'.
                'Some <a href="http://foo.com">link</a>.'.
            '</cite>';

        $rendered = $cite->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithNestedFormattedText(): void
    {
        $cite =
            Cite::create()
                ->appendText(
                    Bold::create()
                        ->appendText('Some ')
                        ->appendText(Italic::create()->appendText('nested formatting'))
                        ->appendText('.')
                );

        $expected =
            '<cite>'.
                '<b>Some <i>nested formatting</i>.</b>'.
            '</cite>';

        $rendered = $cite->render();
        $this->assertEqualsHtml($expected, $rendered);
    }
}
