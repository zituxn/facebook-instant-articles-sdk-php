<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Validators;

use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Video;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\AnimatedGIF;

/**
 *
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{
    /*
        Array size tests ---------------
     */
    public function testArraySize(): void
    {
        $result = Type::isArraySize(vec[1,2,3], 3);
        $this->assertTrue($result);
    }

    public function testArrayNotSize(): void
    {
        $result = Type::isArraySize(vec[1,2,3], 2);
        $this->assertFalse($result);
    }

    public function testArrayMinSizeExact(): void
    {
        $result = Type::isArraySizeGreaterThan(vec[1,2,3], 3);
        $this->assertTrue($result);
    }

    public function testArrayMinSizeMore(): void
    {
        $result = Type::isArraySizeGreaterThan(vec[1,2,3], 2);
        $this->assertTrue($result);
    }

    public function testArrayMinSizeFew(): void
    {
        $result = Type::isArraySizeGreaterThan(vec[1,2,3], 4);
        $this->assertFalse($result);
    }

    public function testEnforceArrayMinSizeException(): void
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforceArraySizeGreaterThan(vec[1,2,3], 4);
    }

    public function testArrayMaxSizeExact(): void
    {
        $result = Type::isArraySizeLowerThan(vec[1,2,3], 3);
        $this->assertTrue($result);
    }

    public function testArrayMaxSizeFew(): void
    {
        $result = Type::isArraySizeLowerThan(vec[1,2,3], 4);
        $this->assertTrue($result);
    }

    public function testArrayMaxSizeMore(): void
    {
        $result = Type::isArraySizeLowerThan(vec[1,2,3], 2);
        $this->assertFalse($result);
    }

    public function testEnforceArrayMaxSizeException(): void
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforceArraySizeLowerThan(vec[1,2,3], 2);
    }

    public function testIsWithinTrueString(): void
    {
        $result = Type::isWithin('x', vec['x', 'y', 'z']);
        $this->assertTrue($result);
    }

    public function testIsWithinTrueObj(): void
    {
        $image = Image::create();
        $video = Video::create();
        $result = Type::isWithin($image, vec[$image, $video, 'z']);
        $this->assertTrue($result);
    }

    public function testIsWithinFalse(): void
    {
        $result = Type::isWithin('a', vec['x', 'y', 'z']);
        $this->assertFalse($result);
    }

    public function testIsWithinFalseObj(): void
    {
        $image = Image::create();
        $video = Video::create();
        $anotherImg = Image::create();
        $result = Type::isWithin($image, vec[$anotherImg, $video, 'z']);
        $this->assertFalse($result);
    }

    public function testEnforceWithinTrueString(): void
    {
        $result = Type::enforceWithin('x', vec['x', 'y', 'z']);
        $this->assertTrue($result);
    }

    public function testEnforceWithinExceptionString(): void
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforceWithin('a', vec['x', 'y', 'z']);
    }

    public function testStringNotEmpty(): void
    {
        $this->assertFalse(Type::isTextEmpty("not empty"));
        $this->assertFalse(Type::isTextEmpty("\nnot empty\t"));
        $this->assertFalse(Type::isTextEmpty(" not empty "));
        $this->assertFalse(Type::isTextEmpty("&nbsp;not empty"));
        $this->assertFalse(Type::isTextEmpty("<3 strings"));
        $this->assertFalse(Type::isTextEmpty("<br />"));
    }

    public function testStringEmpty(): void
    {
        $this->assertTrue(Type::isTextEmpty(""));
        $this->assertTrue(Type::isTextEmpty("  "));
        $this->assertTrue(Type::isTextEmpty("\t\n\r"));
        $this->assertTrue(Type::isTextEmpty("&nbsp;"));
        $this->assertTrue(Type::isTextEmpty("\n"));
        $this->assertTrue(Type::isTextEmpty(null));
    }

    public function testEnforceElementTag(): void
    {
        $document = new \DOMDocument();
        Type::enforceElementTag($document->createElement('img'), 'img');
    }

    public function testEnforceElementTagFalse(): void
    {
        $document = new \DOMDocument();
        $this->setExpectedException('InvalidArgumentException');
        Type::enforceElementTag($document->createElement('body'), 'img');
    }

    public function testIsElementTag(): void
    {
        $document = new \DOMDocument();
        $this->assertTrue(Type::isElementTag($document->createElement('img'), 'img'));
    }

    public function testIsElementTagFalse(): void
    {
        $document = new \DOMDocument();
        $this->assertFalse(Type::isElementTag($document->createElement('body'), 'img'));
    }
}
