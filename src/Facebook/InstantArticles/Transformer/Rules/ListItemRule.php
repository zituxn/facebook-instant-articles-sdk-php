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
use Facebook\InstantArticles\Elements\ListElement;
use Facebook\InstantArticles\Elements\ListItem;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class ListItemRule extends ConfigurationSelectorRule
{
    public function getContextClass(): Vector<string>
    {
        return Vector { ListElement::getClassName() };
    }

    public static function create(): ListItemRule
    {
        return new ListItemRule();
    }

    public static function createFrom(Map $configuration): ListItemRule
    {
        $listItemRule = self::create();
        $listItemRule->withSelector(Type::mapGetString($configuration, 'selector'));
        return $listItemRule;
    }

    public function apply(Transformer $transformer, Element $list, \DOMNode $element): Element
    {
        $li = ListItem::create();
        invariant($list instanceof ListElement, 'Error, $list is not ListElement');
        $list->addItem($li);
        $transformer->transform($li, $element);

        return $list;
    }
}
