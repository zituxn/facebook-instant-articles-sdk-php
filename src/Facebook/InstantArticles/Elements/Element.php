<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Elements;

use Facebook\InstantArticles\Validators\Type;

/**
 * Class Element.
 * This class is the meta each tag element that contains rendering code for the
 * tags.
 *
 */
abstract class Element
{
    private bool $empty_validation = true;

    abstract public function toDOMElement(\DOMDocument $document): \DOMNode;

    /**
     * Renders the Element content
     *
     * @param string $doctype the doctype will be applied to document. I.e.: '<!doctype html>'.
     * @param bool $formatted Whether to format output.
     *
     * @return string with the content rendered.
     */
  public function render(
    string $doctype = '',
    bool $formatted = false,
    bool $validate = true,
  ): string    {
        $document = new \DOMDocument();
        $document->preserveWhiteSpace = !$formatted;
        $document->formatOutput = $formatted;
        if (!$validate || ($validate && $this->isValid())) {
            $element = $this->toDOMElement($document);
            $document->appendChild($element);
            $rendered = $doctype.$document->saveXML($element, LIBXML_NOEMPTYTAG);
        } else {
            $rendered = '';
        }

        // We can't currently use DOMDocument::saveHTML, because it doesn't produce proper HTML5 markup, so we have to strip CDATA enclosures
        // TODO Consider replacing this workaround with a parent class for elements that will be rendered and in this class use the `srcdoc` attribute to output the (escaped) markup
        $rendered = preg_replace('/<!\[CDATA\[(.*?)\]\]>/is', '$1', $rendered);
        // Fix void HTML5 elements (these can't be closed like in XML)
        $rendered = str_replace('></area>', '/>', $rendered);
        $rendered = str_replace('></base>', '/>', $rendered);
        $rendered = str_replace('></br>', '/>', $rendered);
        $rendered = str_replace('></col>', '/>', $rendered);
        $rendered = str_replace('></command>', '/>', $rendered);
        $rendered = str_replace('></embed>', '/>', $rendered);
        $rendered = str_replace('></hr>', '/>', $rendered);
        $rendered = str_replace('></img>', '/>', $rendered);
        $rendered = str_replace('></input>', '/>', $rendered);
        $rendered = str_replace('></keygen>', '/>', $rendered);
        $rendered = str_replace('></link>', '/>', $rendered);
        $rendered = str_replace('></meta>', '/>', $rendered);
        $rendered = str_replace('></param>', '/>', $rendered);
        $rendered = str_replace('></source>', '/>', $rendered);
        $rendered = str_replace('></track>', '/>', $rendered);
        $rendered = str_replace('></wbr>', '/>', $rendered);

        return $rendered;
    }

    /**
     * Auxiliary method to extract all Elements full qualified class name.
     *
     * @return string The full qualified name of class.
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

    /**
     * Method that checks if the element is valid, not empty. If !valid() it wont be rendered.
     * @since v1.0.7
     * @return boolean true for valid element, false otherwise.
     */
    public function isValid(): bool
    {
        return true;
    }

    /**
     * Method that checks if empty element will warn on InstantArticleValidator.
     * @since v1.1.1
     * @see InstantArticleValidator
     * @return boolean true for ignore, false otherwise.
     */
    public function isEmptyValidationEnabled(): bool
    {
        return $this->empty_validation;
    }

    /**
     * Marks this Paragraph to be ignored on isValid if it is empty.
     */
    public function enableEmptyValidation(): bool
    {
        return $this->empty_validation = true;
    }

    /**
     * Marks this Paragraph to *not* be ignored on isValid if it is empty.
     */
    public function disableEmptyValidation(): bool
    {
        return $this->empty_validation = false;
    }

  public static function appendChild(
    \DOMNode $element,
    ?Element $child,
    \DOMDocument $document,
  ): void    {
        if ($child !== null && $child->isValid()) {
            $element->appendChild($child->toDOMElement($document));
        }
    }
}
