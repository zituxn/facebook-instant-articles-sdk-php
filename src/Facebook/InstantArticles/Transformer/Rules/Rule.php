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
use Facebook\InstantArticles\Transformer\Transformer;

abstract class Rule
{
    public function matches(Element $context, \DOMNode $node): bool
    {
        $matches_context = $this->matchesContext($context);
        $matches_node = $this->matchesNode($node);
        // if ($matches_context && $matches_node) {
        //     var_dump('context class: '.get_class($context));
        //     var_dump('context matches: '.($matches_context ? 'MATCHES' : 'no match'));
        //     var_dump('node name: <'.$node->nodeName.' />');
        //     var_dump('node matches: '.($matches_node ? 'MATCHES' : 'no match'));
        //     var_dump('rule: '.get_class($this));
        //     var_dump('-------');
        // }
        // if ($node->nodeName === 'iframe') {
        //     var_dump('context class: '.get_class($context));
        //     var_dump('context matches: '.($matches_context ? 'MATCHES' : 'no match'));
        //     var_dump('node name: <'.$node->nodeName.' />');
        //     var_dump('node: '.$node->ownerDocument->saveXML($node));
        //     var_dump('node matches: '.($matches_node ? 'MATCHES' : 'no match'));
        //     var_dump('rule: '.get_class($this));
        //     var_dump('-------');
        // }
        return $matches_context && $matches_node;
    }

    abstract public function matchesContext(Element $context): bool;

    abstract public function matchesNode(\DOMNode $node): bool;

    abstract public function apply(Transformer $transformer, Element $container, \DOMNode $node): Element;

    abstract public function getContextClass(): Vector<string>;

    public static function create(): Rule
    {
        throw new \Exception(
            'All Rule class extensions should implement the '.
            'Rule::create() method'
        );
    }

    public static function createFrom(array $configuration): Rule
    {
        throw new \Exception(
            'All Rule class extensions should implement the '.
            'Rule::createFrom($configuration) method'
        );
    }

    public static function retrieveProperty(array<string, mixed> $properties, string $property_name): mixed
    {
        if (array_key_exists($property_name, $properties)) {
            return $properties[$property_name];
        } elseif (array_key_exists('properties', $properties)) {
            $mappedProperties = $properties['properties'];
            if (is_array($mappedProperties) && array_key_exists($property_name, $mappedProperties)) {
                return $mappedProperties[$property_name];
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
}
