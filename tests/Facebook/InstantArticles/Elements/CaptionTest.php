<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

class CaptionTest extends \Facebook\Util\BaseHTMLTestCase
{
    public function testRenderEmpty(): void
    {
        $caption = Caption::create()->withTitle(H1::create()->appendText(""));

        $expected = '';

        $rendered = $caption->render();
        $this->assertEquals($expected, $rendered);
    }

    public function testRenderBasic(): void
    {
        $caption =
            Caption::create()
                ->appendText('Caption Title');

        $expected =
            '<figcaption>'.
                'Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithSubTitle(): void
    {
        $caption =
            Caption::create()
                ->withTitle(H1::create()->appendText('Caption Title'))
                ->withSubTitle(H2::create()->appendText('Caption SubTitle'));

        $expected =
            '<figcaption>'.
                '<h1>Caption Title</h1>'.
                '<h2>Caption SubTitle</h2>'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithCredit(): void
    {
        $cite = Cite::create();
        $cite->appendText('Caption Credit');
        $caption =
            Caption::create()
                ->withTitle(H1::create()->appendText('Caption Title'))
                ->withCredit($cite);

        $expected =
            '<figcaption>'.
                '<h1>Caption Title</h1>'.
                '<cite>Caption Credit</cite>'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithPosition(): void
    {
        $caption =
            Caption::create()
                ->withPosition(Caption::POSITION_BELOW)
                ->appendText('Caption Title');

        $expected =
            '<figcaption class="op-vertical-below">'.
                'Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithLargeFontSize(): void
    {
        $caption =
            Caption::create()
                ->withFontsize(Caption::SIZE_LARGE)
                ->appendText('Caption Title');

        $expected =
            '<figcaption class="op-large">'.
                'Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithSmallFontSize(): void
    {
        $caption =
            Caption::create()
                ->withFontsize(Caption::SIZE_SMALL)
                ->appendText('Small Caption Title');

        $expected =
            '<figcaption class="op-small">'.
                'Small Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithTextAlignment(): void
    {
        $caption =
            Caption::create()
                ->withTextAlignment(Caption::ALIGN_LEFT)
                ->appendText('Caption Title');

        $expected =
            '<figcaption class="op-left">'.
                'Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithVerticalAlignment(): void
    {
        $caption =
            Caption::create()
                ->withVerticalAlignment(Caption::VERTICAL_BOTTOM)
                ->appendText('Caption Title');

        $expected =
            '<figcaption class="op-vertical-bottom">'.
                'Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }

    public function testRenderWithAllFormating(): void
    {
        $caption =
            Caption::create()
                ->withFontsize(Caption::SIZE_LARGE)
                ->withPosition(Caption::POSITION_BELOW)
                ->withTextAlignment(Caption::ALIGN_LEFT)
                ->withVerticalAlignment(Caption::VERTICAL_BOTTOM)
                ->appendText('Caption Title');

        $expected =
            '<figcaption class="op-left op-vertical-bottom op-large op-vertical-below">'.
                'Caption Title'.
            '</figcaption>';

        $rendered = $caption->render();
        $this->assertEqualsHtml($expected, $rendered);
    }
}
