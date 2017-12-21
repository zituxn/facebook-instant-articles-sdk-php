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
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Rules\Rule;

abstract class TransformerWarning
{
    /**
     * @var string
     */
    private ?string $message;

    /**
     * @var Element
     */
    private ?Element $context;

    /**
     * @var \DOMNode
     */
    private ?\DOMNode $node;

    /**
     * @var ConfigurationSelectorRule
     */
    private ?Rule $rule;

    /**
     * @param string $message
     * @param Element $context
     * @param \DOMNode $node
     * @param ConfigurationSelectorRule $rule
     */
    public function __construct(?string $message, ?Element $context, ?\DOMNode $node, ?Rule $rule)
    {
        $this->message = $message;
        $this->context = $context;
        $this->node = $node;
        $this->rule = $rule;
    }

    /**
     * @return string
     */
    public abstract function __toString(): string;

    /**
     * @return Element
     */
    public function getContext(): ?Element
    {
        return $this->context;
    }

    /**
     * @return \DOMNode
     */
    public function getNode(): ?\DOMNode
    {
        return $this->node;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return \Rule
     */
    public function getRule(): ?Rule
    {
        return $this->rule;
    }
}
