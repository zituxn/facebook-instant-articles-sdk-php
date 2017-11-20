<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer;

use Facebook\InstantArticles\Transformer\Warnings\TransformerWarning;
use Facebook\InstantArticles\Transformer\Warnings\UnrecognizedElement;
use Facebook\InstantArticles\Transformer\Rules\Rule;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Validators\InstantArticleValidator;

class Transformer
{
    /**
     * @var array<Rule> This is the internal map for rules to be applied
     */
    private array<Rule> $rules = array();

    /**
     * @var Vector<TransformerWarning>
     */
    private Vector<TransformerWarning> $warnings = Vector {};

    /**
     * @var bool
     */
    public bool $suppress_warnings = false;

    /**
     * @var InstantArticle the initial context.
     */
    private ?InstantArticle $instantArticle;

    /**
     * @var DateTimeZone the timezone for parsing dates. It defaults to 'America/Los_Angeles', but can be customized.
     */
    private \DateTimeZone $defaultDateTimeZone;

    /**
     * Flag attribute added to elements processed by a getter, so they
     * are not processed again by other rules.
     */
    const INSTANT_ARTICLES_PARSED_FLAG = 'data-instant-articles-element-processed';

    /**
     * Initializes default values.
     */
    public function __construct()
    {
        $this->defaultDateTimeZone = new \DateTimeZone('America/Los_Angeles');
    }

    /**
     * Clones a node for appending to raw-html containing Elements like Interactive.
     *
     * @param DOMElement $node The node to clone
     * @return DOMElement The cloned node.
     */
    public static function cloneNode(\DOMNode $node): \DOMNode
    {
        $clone = $node->cloneNode(true);
        if ($clone instanceof \DOMElement) {
            if ($clone->hasAttribute(self::INSTANT_ARTICLES_PARSED_FLAG)) {
                $clone->removeAttribute(self::INSTANT_ARTICLES_PARSED_FLAG);
            }
        }
        return $clone;
    }

    /**
     * Marks a node as processed.
     *
     * @param DOMNode $node The node to clone
     */
    public static function markAsProcessed(\DOMNode $node): void
    {
        if ($node instanceof \DOMElement) {
            $node->setAttribute(self::INSTANT_ARTICLES_PARSED_FLAG, 'true');
        }
    }

    /**
     * Returns whether a node is processed
     *
     * @param DOMNode $node The node of interest
     * @return bool true if node processed already, false otherwise.
     */
    protected static function isProcessed(\DOMNode $node): bool
    {
        if ($node instanceof \DOMElement) {
            return $node->getAttribute(self::INSTANT_ARTICLES_PARSED_FLAG) == 'true';
        }
        return false;
    }

    /**
     * @return array
     */
    public function getWarnings(): Vector<TransformerWarning>
    {
        return $this->warnings;
    }

    /**
     * @param Rule $rule
     */
    public function addRule(Rule $rule): void
    {
        $this->rules[] = $rule;
    }

    /**
     * @param $warning
     */
    public function addWarning(TransformerWarning $warning): void
    {
        $this->warnings->add($warning);
    }

    /**
     * @return InstantArticle the initial context of this Transformer
     */
    public function getInstantArticle(): ?InstantArticle
    {
        return $this->instantArticle;
    }

    /**
     * @param InstantArticle $context
     * @param string $content
     *
     * @return mixed
     */
    public function transformString(Element $context, string $content, string $encoding = "utf-8"): Element
    {
        $libxml_previous_state = libxml_use_internal_errors(true);
        $document = new \DOMDocument('1.0');
        if (function_exists('mb_convert_encoding')) {
            $document->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', $encoding));
        } else {
            // wrap the content with charset meta tags
            $document->loadHTML(
                '<html><head>' .
                '<meta http-equiv="Content-Type" content="text/html; charset=' . $encoding . '">' .
                '</head><body>' . $content . '</body></html>'
            );
        }
        libxml_clear_errors();
        libxml_use_internal_errors($libxml_previous_state);
        return $this->transform($context, $document);
    }

    /**
     * @param InstantArticle $context
     * @param \DOMNode $node
     *
     * @return mixed
     */
    public function transform(Element $context, \DOMNode $node): Element
    {
        if ($context instanceof InstantArticle) {
            $context->addMetaProperty('op:generator:transformer', 'facebook-instant-articles-sdk-php');
            $context->addMetaProperty('op:generator:transformer:version', InstantArticle::CURRENT_VERSION);
            $this->instantArticle = $context;
        }

        if (!$node) {
            error_log(
                'Transformer::transform($context, $node) requires $node'.
                ' to be a valid one. Check on the stacktrace if this is '.
                'some nested transform operation and fix the selector.'
            );
        }
        $current_context = $context;
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                if (self::isProcessed($child)) {
                    continue;
                }
                $matched = false;

                // Process in reverse order
                $matchingContextRules = array_reverse($this->rules);
                foreach ($matchingContextRules as $rule) {

                    if ($rule->matches($current_context, $child)) {
                        self::markAsProcessed($child);
                        $current_context = $rule->apply($this, $current_context, $child);
                        $matched = true;

                        // Just a single rule for each node, so move on
                        break;
                    }
                }

                if (!$matched &&
                    !($child->nodeName === '#text' && trim($child->textContent) === '') &&
                    !($child->nodeName === '#comment') &&
                    !($child->nodeName === 'html' && $child instanceof \DOMDocumentType) &&
                    // !($child->nodeName === 'xml' && $child instanceof \DOMProcessingInstruction) &&
                    !$this->suppress_warnings
                    ) {
                    $tag_content = $child->ownerDocument->saveXML($child);
                    $tag_trimmed = trim($tag_content);

                    $this->addWarning(new UnrecognizedElement($current_context, $child));
                }
            }
        }

        return $context;
    }

    /**
     * @param string $json_file
     */
    public function loadRules(string $json_file): void
    {
        $configuration = json_decode($json_file, true);
        if ($configuration && array_key_exists('rules', $configuration)) {
            foreach ($configuration['rules'] as $configuration_rule) {
                $class = $configuration_rule['class'];
                try {
                    $factory_method = new \ReflectionMethod($class, 'createFrom');
                } catch (\ReflectionException $e) {
                    $factory_method =
                        new \ReflectionMethod(
                            'Facebook\\InstantArticles\\Transformer\\Rules\\'.$class,
                            'createFrom'
                        );
                }
                $this->addRule($factory_method->invoke(null, $configuration_rule));
            }
        }
    }

    /**
     * Removes all rules already set in this transformer instance.
     */
    public function resetRules(): void
    {
        $this->rules = array();
    }

    /**
     * Gets all rules already set in this transformer instance.
     *
     * @return array<Rule> List of configured rules.
     */
    public function getRules(): array<Rule>
    {
        return $this->rules;
    }

    /**
     * Overrides all rules already set in this transformer instance.
     *
     * @param array<Rule> $rules List of configured rules.
     */
    public function setRules(array<Rule> $rules): void
    {
        $this->resetRules();
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    /**
     * Sets the default timezone for parsing dates.
     *
     * @param DateTimeZone $dateTimeZone
     */
    public function setDefaultDateTimeZone(\DateTimeZone $dateTimeZone): void
    {
        $this->defaultDateTimeZone = $dateTimeZone;
    }

    /**
     * Gets the default timezone for parsing dates.
     *
     * @return DateTimeZone
     */
    public function getDefaultDateTimeZone(): \DateTimeZone
    {
        return $this->defaultDateTimeZone;
    }
}
