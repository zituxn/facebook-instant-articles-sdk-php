<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules\Compat;

use Facebook\InstantArticles\Transformer\Rules\ConfigurationSelectorRule;
use Facebook\InstantArticles\Transformer\Transformer;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Slideshow;

class JetpackSlideshowRule extends ConfigurationSelectorRule
{
    const PROPERTY_JETPACK_DATA_GALLERY = 'jetpack.data-gallery';

    public function getContextClass(): Vector<string>
    {
        return Vector { InstantArticle::getClassName() };
    }

    public static function create(): JetpackSlideshowRule
    {
        return new JetpackSlideshowRule();
    }

    public static function createFrom(array<string, mixed> $configuration): JetpackSlideshowRule
    {
        $slideshow_rule = self::create();
        $slideshow_rule->withSelector(Type::mixedToString($configuration['selector']));

        $slideshow_rule->withProperties(
            Vector {
                self::PROPERTY_JETPACK_DATA_GALLERY
            },
            $configuration
        );

        return $slideshow_rule;
    }

    public function apply(Transformer $transformer, Element $instant_article, \DOMNode $node): Element
    {
        // Builds the slideshow
        $slideshow = Slideshow::create();
        invariant($instant_article instanceof InstantArticle, 'Error, $instant_article is not InstantArticle');
        $instant_article->addChild($slideshow);

        $gallery = $this->getProperty(self::PROPERTY_JETPACK_DATA_GALLERY, $node);

        if ($gallery !== null && is_array($gallery)) {
            foreach ($gallery as $gallery_image) {
                // Constructs Image if it contains URL
                if (!Type::isTextEmpty($gallery_image['src'])) {
                    $image = Image::create();
                    $image->withURL($gallery_image['src']);

                    // Constructs Caption element when present in the JSON
                    if (!Type::isTextEmpty($gallery_image['caption'])) {
                        $caption = Caption::create();
                        $caption->appendText($gallery_image['caption']);
                        $image->withCaption($caption);
                    }
                    $slideshow->addImage($image);
                }
            }
        }

        $transformer->transform($slideshow, $node);

        return $instant_article;
    }
}
