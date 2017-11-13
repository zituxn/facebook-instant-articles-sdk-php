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
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;
use Facebook\InstantArticles\Transformer\Transformer;

class HeaderImageRule extends ConfigurationSelectorRule
{
    const PROPERTY_IMAGE_URL = 'image.url';

    public function getContextClass(): Vector<string>
    {
        return Vector { Header::getClassName() };
    }

    public static function create(): HeaderImageRule
    {
        return new HeaderImageRule();
    }

    public static function createFrom(Map $configuration): HeaderImageRule
    {
        $image_rule = self::create();
        $image_rule->withSelector(Type::mapGetString($configuration, 'selector'));

        $image_rule->withProperties(
            Vector {
                self::PROPERTY_IMAGE_URL,
            },
            $configuration
        );

        return $image_rule;
    }

    public function apply(Transformer $transformer, Element $header, \DOMNode $node): Element
    {
        invariant($header instanceof Header, 'Error, $header is not Header');
        $image = Image::create();

        // Builds the image
        $url = $this->getPropertyString(self::PROPERTY_IMAGE_URL, $node);
        if ($url) {
            $image->withURL($url);
            $header->withCover($image);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_IMAGE_URL,
                    $header,
                    $node,
                    $this
                )
            );
        }

        $suppress_warnings = $transformer->suppress_warnings;
        $transformer->suppress_warnings = true;
        $transformer->transform($image, $node);
        $transformer->suppress_warnings = $suppress_warnings;

        return $header;
    }
}
