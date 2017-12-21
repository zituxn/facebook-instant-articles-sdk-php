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
        Vec size tests ---------------
     */
    public function testVecSize(): void
    {
        $result = Type::isVecSize(vec[1,2,3], 3);
        $this->assertTrue($result);
    }

    public function testVecNotSize(): void
    {
        $result = Type::isVecSize(vec[1,2,3], 2);
        $this->assertFalse($result);
    }

    public function testVecMinSizeExact(): void
    {
        $result = Type::isVecSizeGreaterThan(vec[1,2,3], 3);
        $this->assertTrue($result);
    }

    public function testVecMinSizeMore(): void
    {
        $result = Type::isVecSizeGreaterThan(vec[1,2,3], 2);
        $this->assertTrue($result);
    }

    public function testVecMinSizeFew(): void
    {
        $result = Type::isVecSizeGreaterThan(vec[1,2,3], 4);
        $this->assertFalse($result);
    }

    public function testEnforceVecMinSizeException(): void
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforceVecSizeGreaterThan(vec[1,2,3], 4);
    }

    public function testVecMaxSizeExact(): void
    {
        $result = Type::isVecSizeLowerThan(vec[1,2,3], 3);
        $this->assertTrue($result);
    }

    public function testVecMaxSizeFew(): void
    {
        $result = Type::isVecSizeLowerThan(vec[1,2,3], 4);
        $this->assertTrue($result);
    }

    public function testVecMaxSizeMore(): void
    {
        $result = Type::isVecSizeLowerThan(vec[1,2,3], 2);
        $this->assertFalse($result);
    }

    public function testEnforceVecMaxSizeException(): void
    {
        $this->setExpectedException('InvalidArgumentException');

        Type::enforceVecSizeLowerThan(vec[1,2,3], 2);
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
