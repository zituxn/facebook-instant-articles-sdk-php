<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Warnings;

use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Transformer\Rules\ConfigurationSelectorRule;

/**
 * Class InvalidSelector warning to show that an invalid selector for a required property was used
 */
class InvalidSelector extends TransformerWarning
{
    private ConfigurationSelectorRule $rule;

    /**
     * @param string $fields
     * @param Element $context
     * @param \DOMNode $node
     * @param Rule $rule
     */
    public function __construct(string $fields, Element $context, \DOMNode $node, ConfigurationSelectorRule $rule)
    {
        parent::__construct($fields, $context, $node, $rule);
        $this->rule = $rule;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if ($this->getContext()) {
            $reflection = new \ReflectionClass(get_class($this->getContext()));
            $class_name = $reflection->getShortName();
        } else {
            $class_name = 'no context provided';
        }

        if ($this->getRule()) {
            $reflection = new \ReflectionClass(get_class($this->getRule()));
            $rule_name = $reflection->getShortName();
        } else {
            $rule_name = 'no rule provided';
        }

        $str_properties = $this->getPropertiesString($this->rule);

        return "Invalid selector for fields ({$this->getMessage()}). ".
            "The node being transformed was <{$this->getNode()?->nodeName}> in the ".
            "context of $class_name within the Rule $rule_name with these ".
            "properties: {{$str_properties}}";
    }

    private function getPropertiesString(ConfigurationSelectorRule $rule): string
    {
        $properties = vec[];
        foreach ($rule->getProperties() as $name => $value) {
            $reflection = new \ReflectionClass(get_class($value));
            $value_name = $reflection->getShortName();
            $properties[] = $name.'='.$value_name;
        }
        return implode(',', $properties);
    }
}
