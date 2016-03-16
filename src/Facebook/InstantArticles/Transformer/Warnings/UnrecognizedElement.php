<?php
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Warnings;

class UnrecognizedElement
{
    private $context;
    private $node;

    public function __construct($context, $node)
    {
        $this->context = $context;
        $this->node = $node;
    }

    public function __toString()
    {
        $reflection = new \ReflectionClass(get_class($this->context));
        $class_name = $reflection->getShortName();
        return "No rules defined for <{$this->node->nodeName}> in the context of $class_name";
    }

    public function getContext()
    {
        return $this->context;
    }

    public function getNode()
    {
        return $this->node;
    }
}
