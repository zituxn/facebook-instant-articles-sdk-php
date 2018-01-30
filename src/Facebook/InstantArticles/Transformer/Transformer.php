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
use Facebook\InstantArticles\Transformer\Rules\ConfigurationSelectorRule;
use Facebook\InstantArticles\Elements\InstantArticle;
use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Validators\InstantArticleValidator;

class Transformer
{
    /**
     * @var vec<Rule> This is the internal map for rules to be applied
     */
    private vec<Rule> $rules = vec[];

    /**
     * This bucketized structure is holding the structure for optmizing the processing
     * of rules during the transformation. The string key is the context to which that Rule applies.
     * Fetching the Rules by Context, all the rules will be inserted into the value Map with the original
     * index, so this way we know the original order that rule had, and it will help on merging the Rules
     * in an applicable order.
     *
     * Map example structure, given this addition to the Rules:
     * $transformer->addRule(new ParagraphRule())
     * $transformer->addRule(new BoldRule())
     * $transformer->addRule(new ImageRule())
     *
     * Considering these getContextClass() for each Rule:
     * ParagraphRule::getContextClass() => vec[InstantArticle::class]
     * BoldRule::getContextClass() => vec[TextContainer::class]
     * ImageRule::getContextClass() => vec[InstantArticle::class, Paragraph::class]
     *
     * This is how the map would be filled in:
     * dict [ "InstantArticle" ==> dict [ 0 ==> ParagraphRule, 2 ==> ImageRule ],
     *       "TextContainer" ==> dict [ 1 ==> BoldRule ],
     *       "Paragraph" ==> dict [ 2 ==> ImageRule ]
     *     ]
     *
     * @var dict<string, dict<int, Rule>> These are the buckets for faster processing the Rules.
     */
    private dict<string, dict<int, Rule>> $bucketedRules = dict[];

    /**
     * Optmization strategy to keep track of the original order this rule was inserted.
     * @var int counter for rules
     */
    private int $ruleCount = 0;

    /**
     * Memoizes each Element class and its parent classes (+ interfaces it implements)
     * @var dict<string, keyset<string>> Each Element's class with its parent classes.
     */
    private static dict<string, keyset<string>> $elementsParents = dict[];

    /**
     * @var vec<TransformerWarning>
     */
    private vec<TransformerWarning> $warnings = vec[];

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
    public function getWarnings(): vec<TransformerWarning>
    {
        return $this->warnings;
    }

    /**
     * @param Rule $rule
     */
    public function addRule(Rule $rule): void
    {
        $this->rules[] = $rule;
        foreach ($rule->getContextClass() as $context) {
            if (!array_key_exists($context, $this->bucketedRules)) {
                $this->bucketedRules[$context] = dict[];
            }
            $contextBucket = $this->bucketedRules[$context];
            if ($contextBucket !== null) {
                $this->bucketedRules[$context][$this->ruleCount++] = $rule;
            }
        }
    }

    /**
     * @param $warning
     */
    public function addWarning(TransformerWarning $warning): void
    {
        $this->warnings[] = $warning;
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

                // Fetches all matching by context rules, so no need to check for context matching again
                $matchingContextRules = $this->filterMatchingContextRules($current_context);
                foreach ($matchingContextRules as $rule) {

                    if ($rule->matchesNode($child)) {
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
                $this->addRule($factory_method->invoke(null, dict($configuration_rule)));
            }
        }
    }

    /**
     * Removes all rules already set in this transformer instance.
     */
    public function resetRules(): void
    {
        $this->rules = vec[];
    }

    /**
     * Gets all rules already set in this transformer instance.
     *
     * @return vec<Rule> List of configured rules.
     */
    public function getRules(): vec<Rule>
    {
        return $this->rules;
    }

    /**
     * Overrides all rules already set in this transformer instance.
     *
     * @param vec<Rule> $rules List of configured rules.
     */
    public function setRules(vec<Rule> $rules): void
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

    /**
     * Gets all types a given class is, including itself, parent classes and interfaces.
     *
     * @param string $className - the name of the className
     *
     * @return array of class names the provided class name is
     */
    private static function getAllClassTypes(string $className): keyset<string>
    {
        // Memoizes
        if (array_key_exists($className, self::$elementsParents)) {
            return self::$elementsParents[$className];
        }

        $classParents = keyset(class_parents($className, true));
        $classInterfaces = keyset(class_implements($className, true));
        $classNames = keyset[$className];
        if ($classParents) {
            $classNames = Type::concatKeyset($classNames, $classParents);
        }
        if ($classInterfaces) {
            $classNames = Type::concatKeyset($classNames, $classInterfaces);
        }
        self::$elementsParents[$className] = $classNames;
        return $classNames;
    }

    /**
     * This method fetches all the rules matching the Element $context informed.
     * It brings all matching by context rules, considering the class name, its
     * parent classes or interfaces it implements
     *
     * @param Element $context The context class to be checked
     */
    private function filterMatchingContextRules(Element $context): vec<Rule>
    {
        // dict to hold the sparse indexed rules already matched
        $matching = dict[];

        // Fetches all parent class and interfaces so we have a correct matching
        $contextClasses = self::getAllClassTypes($context->getObjClassName());
        foreach ($contextClasses as $contextClass) {
            if (array_key_exists($contextClass, $this->bucketedRules)) {
                foreach($this->bucketedRules[$contextClass] as $index => $rule) {
                    $matching[$index] = $rule;
                }
            }
        }

        // Consider only the matched by context
        $rulesMatched = vec[];

        // Gets the $matching map keys, orders it into the reverse and generate
        // the final reverse order rules
        $keys = array_keys($matching);
        rsort(&$keys);
        foreach ($keys as $key) {
            $rulesMatched[] = $matching[$key];
        }

        return $rulesMatched;
    }

    private static function logMatchingRulesForContext(Element $context, \DOMNode $node, vec<Rule> $matchedRules): string
    {
        $logLine = '<'.$node->nodeName.'> '.$context->getObjClassName();
        foreach($matchedRules as $matchedRule) {
            $configRule = $matchedRule;
            invariant($configRule instanceof ConfigurationSelectorRule, 'Should be ConfigurationSelectorRule');
            $selector = $configRule->getSelector();
            $logLine = $logLine.PHP_EOL."    ".$matchedRule->getObjClassName().': '.$selector;
        }
        return $logLine;
    }
}
