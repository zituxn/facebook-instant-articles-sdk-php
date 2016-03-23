<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Getters;

use Facebook\InstantArticles\Transformer\Validators\Type;

class GetterFactory
{
    const TYPE_STRING_GETTER = 'string';
    const TYPE_INTEGER_GETTER = 'int';
    const TYPE_CHILDREN_GETTER = 'children';
    const TYPE_ELEMENT_GETTER = 'element';
    const TYPE_NEXTSIBLING_GETTER = 'sibling';
    const TYPE_EXISTS_GETTER = 'exists';
    const TYPE_XPATH_GETTER = 'xpath';

    protected static $GETTERS = array(
        self::TYPE_STRING_GETTER => StringGetter::class,
        self::TYPE_INTEGER_GETTER => IntegerGetter::class,
        self::TYPE_CHILDREN_GETTER => ChildrenGetter::class,
        self::TYPE_ELEMENT_GETTER => ElementGetter::class,
        self::TYPE_NEXTSIBLING_GETTER => NextSiblingGetter::class,
        self::TYPE_EXISTS_GETTER => ExistsGetter::class,
        self::TYPE_XPATH_GETTER => XpathGetter::class
    );

    /**
     * Creates an Getter class.
     *
     *  array(
     *        type => 'string' | 'children',
     *        selector => 'img.cover',
     *        [attribute] => 'src'
     *    )
     * @see StringGetter
     * @see ChildrenGetter
     * @see IntegerGetter
     * @see ElementGetter
     * @see NextSiblingGetter
     * @see ExistsGetter
     * @see XpathGetter
     * @param array<string, string> $getter_configuration that maps the properties for getter
     * @throws InvalidArgumentException if the type is invalid
     */
    public static function create($getter_configuration)
    {
        $clazz = $getter_configuration['type'];
        if (array_key_exists($clazz, self::$GETTERS)) {
            $clazz = self::$GETTERS[$clazz];
        }
        $instance = new $clazz();
        $instance->createFrom($getter_configuration);
        return $instance;

        throw new \InvalidArgumentException(
            'Type not informed or unrecognized. The configuration must have'.
            ' a type of "StringGetter" or "ChildrenGetter"'
        );
    }
}
