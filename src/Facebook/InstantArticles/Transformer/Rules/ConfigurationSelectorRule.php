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
use Facebook\InstantArticles\Transformer\Getters\AbstractGetter;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Facebook\InstantArticles\Transformer\Getters\GetterFactory;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

abstract class ConfigurationSelectorRule extends Rule
{
    /**
     * @var string
     */
    protected ?string $selector;

    /**
     * @var AbstractGetter[]
     */
    protected array<string, AbstractGetter> $properties = array();

    /**
     * @param string $selector
     *
     * @return $this
     */
    public function withSelector(string $selector): ConfigurationSelectorRule
    {
        $this->selector = $selector;

        return $this;
    }

    /**
     * @param $property
     * @param array $value
     *
     * @return $this
     */
    public function withProperty(string $property, array $value): ConfigurationSelectorRule
    {
        if ($value) {
            $this->properties[$property] = GetterFactory::create($value);
        }
        return $this;
    }

    public function withProperties(Vector<string> $properties, array<string, mixed> $configuration): ConfigurationSelectorRule
    {
        foreach ($properties as $property) {
            $retrievedProperty = self::retrieveProperty($configuration, $property);
            if ($retrievedProperty !== null) {
                $this->withProperty(
                    $property,
                    $retrievedProperty
                );
            }
        }
        return $this;
    }

    public function matchesContext(Element $context): bool
    {
        foreach($this->getContextClass() as $contextClass) {
            if (strcmp($context::getClassName(), $contextClass) === 0 || is_subclass_of ($context, $contextClass)) {
                return true;
            }
        }
        return false;
    }

    public function matchesNode(\DOMNode $node): bool
    {
        // Only matches DOMElements (ignore text and comments)
        if (!$node instanceof \DOMNode || Type::isTextEmpty($this->selector)) {
            return false;
        }

        // Handles selector = tag
        if (strcmp($node->nodeName, $this->selector) === 0) {
            return true;
        }

        if ($node instanceof \DOMElement) {
            // Handles selector = .class
            if (preg_match('/^\.[a-zA-Z][a-zA-Z0-9-]*$/', $this->selector) === 1) {
                // Tries every class
                $classNames = explode(' ', $node->getAttribute('class'));
                foreach ($classNames as $className) {
                    if ('.' . $className === $this->selector) {
                        return true;
                    }
                }

                // No match!
                return false;
            }

            // Handles selector = tag.class
            if (preg_match('/^[a-zA-Z][a-zA-Z0-9-]*(\.[a-zA-Z][a-zA-Z0-9-]*)?$/', $this->selector) === 1) {
                // Tries every class
                $classNames = explode(' ', $node->getAttribute('class'));
                foreach ($classNames as $className) {
                    if ($node->nodeName . '.' . $className === $this->selector) {
                        return true;
                    }
                }

                // No match!
                return false;
            }
        }

        // Proceed with the more expensive XPath query
        $document = $node->ownerDocument;
        $domXPath = new \DOMXPath($document);

        if (substr($this->selector, 0, 1) === '/') {
            $xpath = $this->selector;
        } else {
            $converter = new CssSelectorConverter();
            $xpath = $converter->toXPath($this->selector);
        }

        $results = $domXPath->query($xpath);

        if (false !== $results) {
            foreach ($results as $result) {
                if ($result === $node) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \DOMNode $node
     * @param string $selector
     *
     * @return \DOMNodeList
     */
    public function findAll(\DOMNode $node, string $selector): \DOMNodeList
    {
        $domXPath = new \DOMXPath($node->ownerDocument);
        $converter = new CssSelectorConverter();
        $xpath = $converter->toXPath($selector);
        return $domXPath->query($xpath, $node);
    }

    /**
     * @param $property_name
     * @param $node
     * @return null
     */
    public function getProperty(string $property_name, \DOMNode $node): mixed
    {
        $value = null;
        if (isset($this->properties[$property_name])) {
            $value = $this->properties[$property_name]->get($node);
        }
        return $value;
    }

    /**
     * @param $property_name
     * @param $node
     * @return the string
     */
    public function getPropertyString(string $property_name, \DOMNode $node): string
    {
        $value = $this->getProperty($property_name, $node);
        if ($value && is_string($value)) {
            return $value;
        }
        return '';
    }

    public function getPropertyInt(string $property_name, \DOMNode $node): int
    {
        $value = $this->getProperty($property_name, $node);
        if ($value && is_int($value)) {
            return $value;
        }
        return -1;
    }

    public function getPropertyElement(string $property_name, \DOMNode $node): ?\DOMElement
    {
        $value = $this->getProperty($property_name, $node);
        if ($value && $value instanceof \DOMElement) {
            return $value;
        }
        return null;
    }

    public function getPropertyNode(string $property_name, \DOMNode $node): ?\DOMNode
    {
        $value = $this->getProperty($property_name, $node);
        if ($value && $value instanceof \DOMNode) {
            return $value;
        }
        return null;
    }

    public function getPropertyArray(string $property_name, \DOMNode $node): array
    {
        $value = $this->getProperty($property_name, $node);
        if ($value && is_array($value)) {
            return $value;
        }
        return array();
    }

    public function getPropertyDateTime(string $property_name, \DOMNode $node): ?\DateTime
    {
        $value = $this->getProperty($property_name, $node);
        if ($value && $value instanceof \DateTime) {
            return $value;
        }
        return null;
    }

    public function getPropertyBoolean(string $property_name, \DOMNode $node): bool
    {
        $value = $this->getProperty($property_name, $node);
        if ($value !== null && is_bool($value)) {
            return $value;
        }
        return false;
    }

    public function getPropertyFragment(string $property_name, \DOMNode $node): ?\DOMDocumentFragment
    {
        $value = $this->getProperty($property_name, $node);
        if ($value && $value instanceof \DOMDocumentFragment) {
            return $value;
        }
        return null;
    }

    public function getProperties(): array<string, AbstractGetter>
    {
        return $this->properties;
    }
}
