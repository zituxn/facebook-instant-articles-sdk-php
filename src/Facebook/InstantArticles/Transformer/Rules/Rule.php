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

abstract class Rule
{
    public function matches(Element $context, \DOMNode $node): bool
    {
        $log = \Logger::getLogger('facebook-instantarticles-transformer');

        $matches_context = $this->matchesContext($context);
        $matches_node = $this->matchesNode($node);
        if ($matches_context && $matches_node) {
            $log->debug('context class: '.get_class($context));
            $log->debug('context matches: '.($matches_context ? 'MATCHES' : 'no match'));
            $log->debug('node name: <'.$node->nodeName.' />');
            $log->debug('node matches: '.($matches_node ? 'MATCHES' : 'no match'));
            $log->debug('rule: '.get_class($this));
            $log->debug('-------');
        }
        if ($node->nodeName === 'iframe') {
            $log->debug('context class: '.get_class($context));
            $log->debug('context matches: '.($matches_context ? 'MATCHES' : 'no match'));
            $log->debug('node name: <'.$node->nodeName.' />');
            $log->debug('node: '.$node->ownerDocument->saveXML($node).' />');
            $log->debug('node matches: '.($matches_node ? 'MATCHES' : 'no match'));
            $log->debug('rule: '.get_class($this));
            $log->debug('-------');
        }
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

    public static function createFrom(Map<string, mixed> $configuration): Rule
    {
        throw new \Exception(
            'All Rule class extensions should implement the '.
            'Rule::createFrom($configuration) method'
        );
    }

    public static function retrieveProperty(Map<string, mixed> $properties, string $property_name): mixed
    {
        if (array_key_exists($property_name, $properties)) {
            return $properties[$property_name];
        } elseif (array_key_exists('properties', $properties)) {
            $mappedProperties = $properties['properties'];
            if ($mappedProperties instanceof Map && array_key_exists($property_name, $mappedProperties)) {
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
