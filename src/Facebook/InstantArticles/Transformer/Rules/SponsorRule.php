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
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\Sponsor;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class SponsorRule extends ConfigurationSelectorRule
{
    const PROPERTY_SPONSOR_PAGE_URL = 'sponsor.page_url';

    public function getContextClass(): Vector<string>
    {
        return Vector { Header::getClassName() };
    }

    public static function create(): SponsorRule
    {
        return new SponsorRule();
    }

    public static function createFrom(array<string, mixed> $configuration): SponsorRule
    {
        $sponsor_rule = SponsorRule::create();

        $sponsor_rule->withSelector(Type::mixedToString($configuration['selector']));

        $sponsor_rule->withProperties(
            Vector {
                self::PROPERTY_SPONSOR_PAGE_URL,
            },
            $configuration
        );

        return $sponsor_rule;
    }

    public function apply(Transformer $transformer, Element $header, \DOMNode $node): Element
    {
        $page_url = $this->getPropertyString(self::PROPERTY_SPONSOR_PAGE_URL, $node);
        if ($page_url !== null && !Type::isTextEmpty($page_url)) {
            $sponsor = Sponsor::create();
            invariant($header instanceof Header, 'Error, $header is not Header.');
            $header->withSponsor($sponsor);
            $sponsor->withPageUrl($page_url);
        }
        return $header;
    }
}
