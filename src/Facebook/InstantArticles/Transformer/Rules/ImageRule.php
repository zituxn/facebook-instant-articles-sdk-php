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
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\Cite;
use Facebook\InstantArticles\Elements\Paragraph;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;
use Facebook\InstantArticles\Transformer\Warnings\NoRootInstantArticleFoundWarning;
use Facebook\InstantArticles\Transformer\Transformer;

class ImageRule extends ConfigurationSelectorRule
{
    const PROPERTY_IMAGE_URL = 'image.url';
    const PROPERTY_LIKE = 'image.like';
    const PROPERTY_COMMENTS = 'image.comments';
    const PROPERTY_CREDIT = 'image.credit';
    const PROPERTY_CAPTION = 'image.caption';

    const ASPECT_FIT = 'aspect-fit';
    const ASPECT_FIT_ONLY = 'aspect-fit-only';
    const FULLSCREEN = 'fullscreen';
    const NON_INTERACTIVE = 'non-interactive';


    public function getContextClass(): Vector<string>
    {
        return
            Vector {
                InstantArticle::getClassName(),
                Paragraph::getClassName(),
            };
    }

    public static function create(): ImageRule
    {
        return new self();
    }

    public static function createFrom(array<string, mixed> $configuration): ImageRule
    {
        $image_rule = self::create();
        $image_rule->withSelector(Type::mixedToString($configuration['selector']));

        $image_rule->withProperties(
            Vector {
                self::PROPERTY_IMAGE_URL,
                self::PROPERTY_LIKE,
                self::PROPERTY_COMMENTS,
                self::PROPERTY_CREDIT,
                self::PROPERTY_CAPTION,
                self::ASPECT_FIT,
                self::ASPECT_FIT_ONLY,
                self::FULLSCREEN,
                self::NON_INTERACTIVE,
            },
            $configuration
        );

        return $image_rule;
    }

    public function apply(Transformer $transformer, Element $context, \DOMNode $node): Element
    {
        $image = Image::create();

        if ($context instanceof InstantArticle) {
            $instant_article = $context;
        } elseif ($transformer->getInstantArticle() !== null) {
            $instant_article = $transformer->getInstantArticle();
            $context->disableEmptyValidation();
            $context = Paragraph::create();
            $context->disableEmptyValidation();
        } else {
            $transformer->addWarning(
                // This new error message should be something like:
                // Could not transform Image, as no root InstantArticle was provided.
                new NoRootInstantArticleFoundWarning($image, $node)
            );
            return $context;
        }

        invariant(!is_null($instant_article), 'Error, $instant_article should not be null.');
        // Builds the image
        $url = $this->getPropertyString(self::PROPERTY_IMAGE_URL, $node);
        if ($url !== null) {
            $image->withURL($url);
            $instant_article->addChild($image);
            if ($instant_article !== $context) {
                $instant_article->addChild($context);
            }
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_IMAGE_URL,
                    $instant_article,
                    $node,
                    $this
                )
            );
        }

        if ($this->getPropertyBoolean(Image::ASPECT_FIT, $node)) {
            $image->withPresentation(Image::ASPECT_FIT);
        } elseif ($this->getPropertyBoolean(Image::ASPECT_FIT_ONLY, $node)) {
            $image->withPresentation(Image::ASPECT_FIT_ONLY);
        } elseif ($this->getPropertyBoolean(Image::FULLSCREEN, $node)) {
            $image->withPresentation(Image::FULLSCREEN);
        } elseif ($this->getPropertyBoolean(Image::NON_INTERACTIVE, $node)) {
            $image->withPresentation(Image::NON_INTERACTIVE);
        }

        if ($this->getPropertyBoolean(self::PROPERTY_LIKE, $node)) {
            $image->enableLike();
        }

        if ($this->getPropertyBoolean(self::PROPERTY_COMMENTS, $node)) {
            $image->enableComments();
        }

        $caption = null;
        $captionElement = $this->getPropertyNode(self::PROPERTY_CAPTION, $node);
        if ($captionElement !== null) {
            $caption = Caption::create();
            $transformer->transform($caption, $captionElement);
        }

        $creditElement = $this->getPropertyNode(self::PROPERTY_CREDIT, $node);
        if ($creditElement !== null) {
            if ($caption === null) {
                $caption = Caption::create();
            }
            $credit = Cite::create();
            $transformer->transform($credit, $creditElement);
            $caption->withCredit($credit);
        }

        if ($caption !== null) {
            $image->withCaption($caption);
        }

        $suppress_warnings = $transformer->suppress_warnings;
        $transformer->suppress_warnings = true;
        $transformer->transform($image, $node);
        $transformer->suppress_warnings = $suppress_warnings;

        return $context;
    }
}
