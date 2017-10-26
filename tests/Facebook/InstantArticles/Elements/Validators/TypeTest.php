<?hh //decl
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
    public function testArraySize()
    {
        $result = Type::isArraySize(Vector { 1,2,3 }, 3);
        $this->assertTrue($result);
    }

    public function testArrayNotSize()
    {
        $result = Type::isArraySize(Vector { 1,2,3 }, 2);
        $this->assertFalse($result);
    }

    public function testArrayMinSizeExact()
    {
        $result = Type::isArraySizeGreaterThan(Vector { 1,2,3 }, 3);
        $this->assertTrue($result);
    }

    public function testArrayMinSizeMore()
    {
        $result = Type::isArraySizeGreaterThan(Vector { 1,2,3 }, 2);
        $this->assertTrue($result);
    }

    public function testArrayMinSizeFew()
    {
        $result = Type::isArraySizeGreaterThan(Vector { 1,2,3 }, 4);
        $this->assertFalse($result);
    }

    public function testEnforceArrayMinSizeException()
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforceArraySizeGreaterThan(Vector { 1,2,3 }, 4);
    }

    public function testArrayMaxSizeExact()
    {
        $result = Type::isArraySizeLowerThan(Vector { 1,2,3 }, 3);
        $this->assertTrue($result);
    }

    public function testArrayMaxSizeFew()
    {
        $result = Type::isArraySizeLowerThan(Vector { 1,2,3 }, 4);
        $this->assertTrue($result);
    }

    public function testArrayMaxSizeMore()
    {
        $result = Type::isArraySizeLowerThan(Vector { 1,2,3 }, 2);
        $this->assertFalse($result);
    }

    public function testEnforceArrayMaxSizeException()
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforceArraySizeLowerThan(Vector { 1,2,3 }, 2);
    }

    public function testIsWithinTrueString()
    {
        $result = Type::isWithin('x', Vector { 'x', 'y', 'z' });
        $this->assertTrue($result);
    }

    public function testIsWithinTrueObj()
    {
        $image = Image::create();
        $video = Video::create();
        $result = Type::isWithin($image, Vector { $image, $video, 'z' });
        $this->assertTrue($result);
    }

    public function testIsWithinFalse()
    {
        $result = Type::isWithin('a', Vector { 'x', 'y', 'z' });
        $this->assertFalse($result);
    }

    public function testIsWithinFalseObj()
    {
        $image = Image::create();
        $video = Video::create();
        $anotherImg = Image::create();
        $result = Type::isWithin($image, Vector { $anotherImg, $video, 'z' });
        $this->assertFalse($result);
    }

    public function testEnforceWithinTrueString()
    {
        $result = Type::enforceWithin('x', Vector { 'x', 'y', 'z' });
        $this->assertTrue($result);
    }

    public function testEnforceWithinExceptionString()
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforceWithin('a', Vector { 'x', 'y', 'z' });
    }

    public function testStringNotEmpty()
    {
        $this->assertFalse(Type::isTextEmpty("not empty"));
        $this->assertFalse(Type::isTextEmpty("\nnot empty\t"));
        $this->assertFalse(Type::isTextEmpty(" not empty "));
        $this->assertFalse(Type::isTextEmpty("&nbsp;not empty"));
        $this->assertFalse(Type::isTextEmpty("<3 strings"));
        $this->assertFalse(Type::isTextEmpty("<br />"));
    }

    public function testStringEmpty()
    {
        $this->assertTrue(Type::isTextEmpty(""));
        $this->assertTrue(Type::isTextEmpty("  "));
        $this->assertTrue(Type::isTextEmpty("\t\n\r"));
        $this->assertTrue(Type::isTextEmpty("&nbsp;"));
        $this->assertTrue(Type::isTextEmpty("\n"));
    }

    public function testEnforceElementTag()
    {
        $document = new \DOMDocument();
        Type::enforceElementTag($document->createElement('img'), 'img');
    }

    public function testEnforceElementTagFalse()
    {
        $document = new \DOMDocument();
        $this->setExpectedException('InvalidArgumentException');
        Type::enforceElementTag($document->createElement('body'), 'img');
    }

    public function testIsElementTag()
    {
        $document = new \DOMDocument();
        $this->assertTrue(Type::isElementTag($document->createElement('img'), 'img'));
    }

    public function testIsElementTagFalse()
    {
        $document = new \DOMDocument();
        $this->assertFalse(Type::isElementTag($document->createElement('body'), 'img'));
    }
}
