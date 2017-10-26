<?hh

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

class NoRootInstantArticleFoundWarning extends TransformerWarning
{
    /**
     * @param Element $element
     * @param DOMNode $node
     */
    public function __construct(Element $element, \DOMNode $node)
    {
        parent::__construct(null, $element, $node ? $node->cloneNode() : null, null);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->formatWarningMessage();
    }

    private function formatWarningMessage(): string
    {
        return "No instant article was informed in the context for Transformer. This element will be lost during transformation: ".$this->getNodeString();
    }

    private function getNodeString(): string
    {
        $node = $this->getNode();
        if ($node) {
            return $node->ownerDocument->saveHTML($node);
        }
        return "";
    }
}
