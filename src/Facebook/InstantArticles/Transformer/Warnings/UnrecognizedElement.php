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

class UnrecognizedElement extends TransformerWarning {
  /**
   * @param Element $context
   * @param \DOMNode $node
   */
  public function __construct(Element $context, \DOMNode $node) {
    parent::__construct(
      null,
      $context,
      $node->cloneNode(),
      null,
    );
  }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $reflection = new \ReflectionClass(get_class($this->getContext()));
        $className = $reflection->getShortName();
        if ($this->getNode()) {
            $nodeName = $this->getNode()?->nodeName;
        } else {
          $nodeName = null;
        }
        if (substr($nodeName, 0, 1) === '#') {
            $nodeDescription = '"'.mb_strimwidth($this->getNode()?->textContent, 0, 30, '...').'"';
        } else {
            $nodeDescription = '<';
            $nodeDescription .= $nodeName;
            $node = $this->getNode();
            if ($node instanceof \DOMElement) {
                $class = $node->getAttribute('class');
                if ($class) {
                    $nodeDescription .= ' class="'. $class .'"';
                }
            }
            $nodeDescription .= '>';
        }
        return
            "No rules defined for {$nodeDescription} in the context of $className";
    }
}
