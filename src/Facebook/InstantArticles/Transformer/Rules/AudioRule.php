<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Transformer\Rules;

use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Elements\Audio;
use Facebook\InstantArticles\Elements\Audible;
use Facebook\InstantArticles\Transformer\Warnings\InvalidSelector;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Transformer\Transformer;

class AudioRule extends ConfigurationSelectorRule
{
    const PROPERTY_AUDIO_URL = 'audio.url';
    const PROPERTY_AUDIO_TITLE = 'audio.title';
    const PROPERTY_AUDIO_AUTOPLAY = 'audio.autoplay';
    const PROPERTY_AUDIO_MUTED = 'audio.muted';

    public function getContextClass(): vec<string>
    {
        return vec[Audible::getClassName()];
    }

    public static function create(): AudioRule
    {
        return new AudioRule();
    }

    public static function createFrom(dict<string, mixed> $configuration): AudioRule
    {
        $audio_rule = self::create();
        $audio_rule->withSelector(Type::mixedToString($configuration['selector']));

        $audio_rule->withProperties(
            vec[
                self::PROPERTY_AUDIO_URL,
                self::PROPERTY_AUDIO_TITLE,
                self::PROPERTY_AUDIO_AUTOPLAY,
                self::PROPERTY_AUDIO_MUTED,
            ],
            $configuration
        );

        return $audio_rule;
    }

    public function apply(Transformer $transformer, Element $audible, \DOMNode $node): Element
    {
        $audio = Audio::create();

        // Builds the image
        $url = $this->getPropertyString(self::PROPERTY_AUDIO_URL, $node);
        $title = $this->getPropertyString(self::PROPERTY_AUDIO_TITLE, $node);
        $autoplay = $this->getPropertyString(self::PROPERTY_AUDIO_AUTOPLAY, $node);
        $muted = $this->getPropertyString(self::PROPERTY_AUDIO_MUTED, $node);

        if ($url !== null) {
            $audio->withURL($url);
            invariant($audible instanceof Audible, 'Error, $audible is not Audible.');
            $audible->withAudio($audio);
        } else {
            // URL is a required field for Audio
            $transformer->addWarning(
                new InvalidSelector(
                    self::PROPERTY_AUDIO_URL,
                    $audible,
                    $node,
                    $this
                )
            );
        }

        if ($title !== null) {
            $audio->withTitle($title);
        }
        if ($autoplay === "" || $autoplay === "true" || $autoplay === "autoplay") {
            $audio->enableAutoplay();
        }
        if ($muted === "" || $muted === "true" || $muted === "muted") {
            $audio->enableMuted();
        }

        return $audible;
    }
}
