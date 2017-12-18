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

class ValidatorWarning extends TransformerWarning
{
    /**
     * @var dict the configuration content
     */
    private dict<string, dict<string, string>> $configuration;

    /**
     * @param Element $element
     */
    public function __construct(Element $element)
    {
        parent::__construct(null, $element, null, null);
        $this->configuration = dict[];
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
        if (!$this->configuration) {
            $this->configuration = dict(parse_ini_file("validator_warning_messages.ini", true));
        }

        $simple_class_name = substr(strrchr($this->getContext()?->getObjClassName(), '\\'), 1);

        if (!array_key_exists('warning_messages', $this->configuration) ||
            !array_key_exists($simple_class_name, $this->configuration['warning_messages'])) {
            $message = 'Invalid content on the object.';
        } else {
            $message = $this->configuration['warning_messages'][$simple_class_name];
        }
        return $message;
    }
}
