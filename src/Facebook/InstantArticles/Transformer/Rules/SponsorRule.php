<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Elements\Sponsor;

class SponsorRule extends ConfigurationSelectorRule
{
    public function getContextClass()
    {
        return Header::getClassName();
    }

    public static function create()
    {
        return new SponsorRule();
    }

    public static function createFrom($configuration)
    {
        return self::create()->withSelector($configuration['selector']);
    }

    public function apply($transformer, $header, $element)
    {
        $sponsor = Sponsor::create();
        $header->withSponsor($sponsor);
        $transformer->transform($sponsor, $element);
        return $header;
    }

    public function loadFrom($configuration)
    {
        $this->selector = $configuration['selector'];
    }
}
