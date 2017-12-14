<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Elements\Interactive;
use Facebook\InstantArticles\Elements\Slideshow;
use Facebook\InstantArticles\Elements\Video;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Captionable;
use Facebook\InstantArticles\Elements\MapElement;
use Facebook\InstantArticles\Elements\H1;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class CaptionRule extends ConfigurationSelectorRule
{
    const PROPERTY_DEFAULT = 'caption.default';

    public function getContextClass(): vec<string>
    {
        return
            vec[
                MapElement::getClassName(),
                Image::getClassName(),
                Interactive::getClassName(),
                Slideshow::getClassName(),
                Video::getClassName(),
            ];
    }

    public static function create(): CaptionRule
    {
        return new CaptionRule();
    }

    public static function createFrom(array<string, mixed> $configuration): CaptionRule
    {
        $caption_rule = self::create();
        $caption_rule->withSelector(Type::mixedToString($configuration['selector']));

        $caption_rule->withProperties(
            vec[
                Caption::POSITION_BELOW,
                Caption::POSITION_CENTER,
                Caption::POSITION_ABOVE,

                Caption::ALIGN_LEFT,
                Caption::ALIGN_CENTER,
                Caption::ALIGN_RIGHT,

                Caption::SIZE_SMALL,
                Caption::SIZE_MEDIUM,
                Caption::SIZE_LARGE,
                Caption::SIZE_XLARGE,

                self::PROPERTY_DEFAULT,
            ],
            $configuration
        );

        return $caption_rule;
    }

    public function apply(Transformer $transformer, Element $container_of_caption, \DOMNode $node): Element
    {
        $caption = Caption::create();
        invariant($container_of_caption instanceof Captionable, 'Error, $container_of_caption is not a Captionable.');
        $container_of_caption->withCaption($caption);

        if ($this->getProperty(Caption::POSITION_BELOW, $node)) {
            $caption->withPosition(Caption::POSITION_BELOW);
        }
        if ($this->getProperty(Caption::POSITION_CENTER, $node)) {
            $caption->withPosition(Caption::POSITION_CENTER);
        }
        if ($this->getProperty(Caption::POSITION_ABOVE, $node)) {
            $caption->withPosition(Caption::POSITION_ABOVE);
        }

        if ($this->getProperty(Caption::ALIGN_LEFT, $node)) {
            $caption->withTextAlignment(Caption::ALIGN_LEFT);
        }
        if ($this->getProperty(Caption::ALIGN_CENTER, $node)) {
            $caption->withTextAlignment(Caption::ALIGN_CENTER);
        }
        if ($this->getProperty(Caption::ALIGN_RIGHT, $node)) {
            $caption->withTextAlignment(Caption::ALIGN_RIGHT);
        }

        if ($this->getProperty(Caption::SIZE_SMALL, $node)) {
            $caption->withFontsize(Caption::SIZE_SMALL);
        }
        if ($this->getProperty(Caption::SIZE_MEDIUM, $node)) {
            $caption->withFontsize(Caption::SIZE_MEDIUM);
        }
        if ($this->getProperty(Caption::SIZE_LARGE, $node)) {
            $caption->withFontsize(Caption::SIZE_LARGE);
        }
        if ($this->getProperty(Caption::SIZE_XLARGE, $node)) {
            $caption->withFontsize(Caption::SIZE_XLARGE);
        }

        $text_default = $this->getPropertyString(self::PROPERTY_DEFAULT, $node);
        if ($text_default !== null) {
            invariant(is_string($text_default), 'Error, $text_default is not a string');
            $caption->withTitle(H1::create()->appendText($text_default));
        } else {
            $transformer->transform($caption, $node);
        }

        invariant($container_of_caption instanceof Element, 'Error, $container_of_caption is not Element');
        return $container_of_caption;
    }
}
