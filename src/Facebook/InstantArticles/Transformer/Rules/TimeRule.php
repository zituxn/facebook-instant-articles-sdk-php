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
use Facebook\InstantArticles\Elements\Time;
use Facebook\InstantArticles\Elements\Header;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;
use Facebook\InstantArticles\Transformer\Transformer;

/**
 * Rule to parse dates from the document.
 *
 * The time zone used will be the default time zone defined on the Transformer.
 *
 * @see Transformer::getDefaultDateTimeZone()
 */
class TimeRule extends ConfigurationSelectorRule
{
    const PROPERTY_TIME_TYPE_DEPRECATED = 'article.time_type';
    const PROPERTY_DATETIME_TYPE = 'article.datetype';
    const PROPERTY_TIME = 'article.time';

    private $type = \Facebook\InstantArticles\Elements\Time::PUBLISHED;

    public function getContextClass(): Vector<string>
    {
        return Vector { Header::getClassName() };
    }

    public static function create(): TimeRule
    {
        return new TimeRule();
    }

    public static function createFrom(Map $configuration): TimeRule
    {
        $time_rule = self::create();
        $time_rule->withSelector(Type::mapGetString($configuration, 'selector'));

        $time_rule->withProperties(
            Vector {
                self::PROPERTY_TIME,
                self::PROPERTY_DATETIME_TYPE,
            },
            $configuration
        );

        // Just for retrocompatibility - issue #172
        // if (array_key_exists(self::PROPERTY_TIME_TYPE_DEPRECATED, $configuration)) {
        //     $time_rule->type = $configuration[self::PROPERTY_TIME_TYPE_DEPRECATED];
        // }

        return $time_rule;
    }

    public function apply(Transformer $transformer, Element $header, \DOMNode $node): Element
    {
        invariant($header instanceof Header, 'Error, $header is not Header');
        $time_type = $this->getProperty(self::PROPERTY_DATETIME_TYPE, $node);
        if ($time_type) {
            $this->type = $time_type;
        }

        // Builds the image
        $time_string = $this->getProperty(self::PROPERTY_TIME, $node);
        if ($time_string) {
            $time = Time::create($this->type);
            $time->withDatetime(new \DateTime($time_string, $transformer->getDefaultDateTimeZone()));
            $header->withTime($time);
        } else {
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_TIME,
                    $header,
                    $node,
                    $this
                )
            );
        }

        return $header;
    }
}
