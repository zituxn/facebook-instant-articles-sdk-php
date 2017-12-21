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
use Facebook\InstantArticles\Elements\Paragraph;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;
use Facebook\InstantArticles\Transformer\Warnings\NoRootInstantArticleFoundWarning;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class InteractiveInsideParagraphRule extends ConfigurationSelectorRule
{
    const PROPERTY_IFRAME = 'interactive.iframe';
    const PROPERTY_URL = 'interactive.url';
    const PROPERTY_WIDTH_NO_MARGIN = 'no-margin';
    const PROPERTY_WIDTH_COLUMN_WIDTH = 'column-width';
    const PROPERTY_HEIGHT = 'interactive.height';
    const PROPERTY_WIDTH = 'interactive.width';

    public function getContextClass(): vec<string>
    {
        return
            vec[
                InstantArticle::getClassName(),
                Paragraph::getClassName(),
            ];
    }

    public static function create(): InteractiveInsideParagraphRule
    {
        return new self();
    }

    public static function createFrom(dict<string, mixed> $configuration): InteractiveInsideParagraphRule
    {
        $interactive_rule = self::create();
        $interactive_rule->withSelector(Type::mixedToString($configuration['selector']));

        $interactive_rule->withProperties(
            vec[
                self::PROPERTY_IFRAME,
                self::PROPERTY_URL,
                self::PROPERTY_WIDTH_NO_MARGIN,
                self::PROPERTY_WIDTH_COLUMN_WIDTH,
                self::PROPERTY_WIDTH,
                self::PROPERTY_HEIGHT,
            ],
            $configuration
        );

        return $interactive_rule;
    }

    public function apply(Transformer $transformer, Element $context, \DOMNode $node): Element
    {
        $interactive = Interactive::create();

        if ($context instanceof InstantArticle) {
            $instant_article = $context;
        } elseif ($transformer->getInstantArticle()) {
            $instant_article = $transformer->getInstantArticle();
            $context->disableEmptyValidation();
            $context = Paragraph::create();
            $context->disableEmptyValidation();
        } else {
            $transformer->addWarning(
                // This new error message should be something like:
                // Could not transform Image, as no root InstantArticle was provided.
                new NoRootInstantArticleFoundWarning($interactive, $node)
            );
            return $context;
        }

        // Builds the interactive
        $iframe = $this->getPropertyNode(self::PROPERTY_IFRAME, $node);

        $url = $this->getPropertyString(self::PROPERTY_URL, $node);
        if ($iframe !== null) {
            $interactive->withHTML($iframe);
        }
        if ($url !== null) {
            $interactive->withSource($url);
        }
        invariant(!is_null($instant_article), 'Error, $instant_article should not be null.');
        if ($iframe !== null || $url !== null) {
            $instant_article->addChild($interactive);
            if ($instant_article !== $context) {
                $instant_article->addChild($context);
            }
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_IFRAME,
                    $instant_article,
                    $node,
                    $this
                )
            );
        }

        if ($this->getPropertyBoolean(self::PROPERTY_WIDTH_COLUMN_WIDTH, $node)) {
            $interactive->withMargin(Interactive::COLUMN_WIDTH);
        } else {
            $interactive->withMargin(Interactive::NO_MARGIN);
        }

        $width = $this->getPropertyInt(self::PROPERTY_WIDTH, $node);
        if ($width !== null) {
            $interactive->withWidth($width);
        }

        $height = $this->getPropertyInt(self::PROPERTY_HEIGHT, $node);
        if ($height !== null) {
            $interactive->withHeight($height);
        }

        $suppress_warnings = $transformer->suppress_warnings;
        $transformer->suppress_warnings = true;
        $transformer->transform($interactive, $node);
        $transformer->suppress_warnings = $suppress_warnings;

        return $context;
    }
}
