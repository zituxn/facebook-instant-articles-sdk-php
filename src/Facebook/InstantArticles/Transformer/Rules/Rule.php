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
use Facebook\InstantArticles\Transformer\Transformer;

abstract class Rule
{
    /**
     * @deprecated Make sure you implement both methods @see #matchesContext() and #matchesNode()
     */
    public function matches(Element $context, \DOMNode $node): bool
    {
        $matches_context = $this->matchesContext($context);
        $matches_node = $this->matchesNode($node);
        return $matches_context && $matches_node;
    }

    abstract public function matchesContext(Element $context): bool;

    abstract public function matchesNode(\DOMNode $node): bool;

    abstract public function apply(Transformer $transformer, Element $container, \DOMNode $node): Element;

    abstract public function getContextClass(): vec<string>;

    public static function create(): Rule
    {
        throw new \Exception(
            'All Rule class extensions should implement the '.
            'Rule::create() method'
        );
    }

    public static function createFrom(dict<string, mixed> $configuration): Rule
    {
        throw new \Exception(
            'All Rule class extensions should implement the '.
            'Rule::createFrom($configuration) method'
        );
    }

    public static function retrieveProperty(dict<string, mixed> $properties, string $property_name): ?dict<string, mixed>
    {
        if (array_key_exists('properties', $properties)) {
            $mappedProperties = $properties['properties'];
            if (is_array($mappedProperties) && array_key_exists($property_name, $mappedProperties)) {
                return dict($mappedProperties[$property_name]);
            }
        }
        return null;
    }

    /**
     * Auxiliary method to extract full qualified class name.
     * @return string The full qualified name of class
     */
    public static function getClassName(): string
    {
        return get_called_class();
    }

    /**
     * Auxiliary method to extract all Elements full qualified class name.
     *
     * @return string The full qualified name of class.
     */
    public function getObjClassName(): string
    {
        return get_called_class();
    }
}
