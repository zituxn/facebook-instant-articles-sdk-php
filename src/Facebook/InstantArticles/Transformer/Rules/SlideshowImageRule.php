<?hh
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Elements\H1;
use Facebook\InstantArticles\Elements\Cite;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Slideshow;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;
use Facebook\InstantArticles\Transformer\Transformer;

class SlideshowImageRule extends ConfigurationSelectorRule
{
    const PROPERTY_IMAGE_URL = 'image.url';
    const PROPERTY_CAPTION_TITLE = 'caption.title';
    const PROPERTY_CAPTION_CREDIT = 'caption.credit';

    public function getContextClass(): Vector<string>
    {
        return Vector { Slideshow::getClassName() };
    }

    public static function create(): SlideshowImageRule
    {
        return new SlideshowImageRule();
    }

    public static function createFrom(array $configuration): SlideshowImageRule
    {
        $image_rule = self::create();
        $image_rule->withSelector($configuration['selector']);

        $image_rule->withProperties(
            Vector {
                self::PROPERTY_IMAGE_URL,
                self::PROPERTY_CAPTION_TITLE,
                self::PROPERTY_CAPTION_CREDIT,
            },
            $configuration
        );

        return $image_rule;
    }

    public function apply(Transformer $transformer, Element $slideshow, \DOMNode $node): Element
    {
        $image = Image::create();
        invariant($slideshow instanceof Slideshow, 'Error, $slideshow is not Slideshow.');

        // Builds the image
        $url = $this->getPropertyString(self::PROPERTY_IMAGE_URL, $node);
        if ($url) {
            $image->withURL($url);
            $slideshow->addImage($image);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_IMAGE_URL,
                    $slideshow,
                    $node,
                    $this
                )
            );
        }

        $caption = Caption::create();

        $caption_title = $this->getPropertyString(self::PROPERTY_CAPTION_TITLE, $node);
        if ($caption_title) {
            $caption->withTitle(H1::create()->appendText($caption_title));
            $image->withCaption($caption);
        }

        $caption_credit = $this->getPropertyString(self::PROPERTY_CAPTION_CREDIT, $node);
        if ($caption_credit) {
            $cite = Cite::create();
            $cite->appendText($caption_credit);
            $caption->withCredit($cite);
        }

        return $slideshow;
    }
}
