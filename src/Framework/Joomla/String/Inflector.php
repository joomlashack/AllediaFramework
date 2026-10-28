<?php

/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2025-2026 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of AllediaFramework.
 *
 * AllediaFramework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * AllediaFramework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AllediaFramework.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Alledia\Framework\Joomla\String;

use Doctrine\Inflector\Inflector as DoctrineInflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\Inflector\LanguageInflectorFactory;
use Doctrine\Inflector\Rules\Pattern;
use Doctrine\Inflector\Rules\Patterns;
use Doctrine\Inflector\Rules\Ruleset;
use Doctrine\Inflector\Rules\Substitution;
use Doctrine\Inflector\Rules\Substitutions;
use Doctrine\Inflector\Rules\Transformations;
use Doctrine\Inflector\Rules\Word;
use Joomla\String\Inflector as JoomlaInflector;

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
defined('_JEXEC') or die();

// phpcs:enable PSR1.Files.SideEffects.FoundWithSymbols

class Inflector
{
    /**
     * @var DoctrineInflector|JoomlaInflector
     */
    protected $inflector;

    /**
     * @var Inflector
     */
    protected Inflector $instance;

    /**
     * JoomlaInflector's default countable words
     *
     * @var string[]
     */
    protected array $countable = [
        'id',
        'hits',
        'clicks',
    ];

    /**
     * @param array $customRules Pairs of singular => plural. A null plural marks an uncountable word
     *
     * @throws \Exception
     */
    public function __construct(array $customRules = [])
    {
        if (class_exists(DoctrineInflector::class)) {
            $factory = InflectorFactory::create();

            if ($customRules) {
                $this->addDoctrineRules($factory, $customRules);
            }

            $this->inflector = $factory->build();
        } elseif (class_exists(JoomlaInflector::class)) {
            $this->inflector = JoomlaInflector::getInstance();

            foreach ($customRules as $singular => $plural) {
                $this->inflector->addWord($singular, $plural);
            }
        } else {
            throw new \Exception('Missing String Inflector class');
        }
    }

    /**
     * Imitate JoomlaInflector::addWord() for DoctrineInflector. Each word is also
     * left as is when it's already in the target form, so isPlural()/isSingular()
     * recognise both forms the way JoomlaInflector does
     *
     * @param LanguageInflectorFactory $factory
     * @param array                    $customRules
     *
     * @return void
     */
    protected function addDoctrineRules(LanguageInflectorFactory $factory, array $customRules): void
    {
        $irregular        = [];
        $flipped          = [];
        $pluralWords      = [];
        $singularWords    = [];
        $uncountableWords = [];

        foreach ($customRules as $singular => $plural) {
            $singular = strtolower((string)$singular);
            $plural   = strtolower((string)$plural);

            if ($plural === '' || $plural === $singular) {
                $uncountableWords[] = new Pattern(preg_quote($singular, '/'));
            } else {
                $irregular[]     = new Substitution(new Word($singular), new Word($plural));
                $flipped[]       = new Substitution(new Word($plural), new Word($singular));
                $pluralWords[]   = new Pattern(preg_quote($plural, '/'));
                $singularWords[] = new Pattern(preg_quote($singular, '/'));
            }
        }

        // Substitutions::getFlippedSubstitutions() fails on an empty set, so flip here instead
        $factory
            ->withPluralRules(
                new Ruleset(
                    new Transformations(),
                    new Patterns(...$pluralWords, ...$uncountableWords),
                    new Substitutions(...$irregular)
                )
            )
            ->withSingularRules(
                new Ruleset(
                    new Transformations(),
                    new Patterns(...$singularWords, ...$uncountableWords),
                    new Substitutions(...$flipped)
                )
            );
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     * @throws \Exception
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->inflector, $name)) {
            return call_user_func_array([$this->inflector, $name], $arguments);
        }

        throw new \Exception('Call to undefined method ' . __CLASS__ . '::' . $name . '()');
    }

    /**
     * Imitate DoctrineInflector method for JoomlaInflector
     *
     * @param string $word
     *
     * @return string
     */
    public function pluralize(string $word): string
    {
        if (is_callable([$this->inflector, 'toPlural'])) {
            // JoomlaInflector returns false when no rule applies or the word is already plural
            return $this->inflector->toPlural($word) ?: $word;
        }

        return $this->inflector->pluralize($word);
    }

    /**
     * Imitate DoctrineInflector method for JoomlaInflector
     *
     * @param string $word
     *
     * @return string
     */
    public function singularize(string $word): string
    {
        if (is_callable([$this->inflector, 'toSingular'])) {
            // JoomlaInflector returns false when no rule applies or the word is already singular
            return $this->inflector->toSingular($word) ?: $word;
        }

        return $this->inflector->singularize($word);
    }

    /**
     * Imitate JoomlaInflector method for DoctrineInflector
     *
     * @param string $word
     *
     * @return string
     */
    public function toPlural(string $word): string
    {
        return $this->pluralize($word);
    }

    /**
     * Imitate JoomlaInflector method for DoctrineInflector
     *
     * @param string $word
     *
     * @return string
     */
    public function toSingular(string $word): string
    {
        return $this->singularize($word);
    }

    /**
     * Imitate JoomlaInflector method for DoctrineInflector
     *
     * @param string $word
     *
     * @return bool
     */
    public function isCountable(string $word): bool
    {
        if (is_callable([$this->inflector, 'isCountable'])) {
            return $this->inflector->isCountable($word);
        }

        return in_array($word, $this->countable);
    }

    /**
     * Imitate JoomlaInflector method for DoctrineInflector
     *
     * @param string $word
     *
     * @return bool
     */
    public function isPlural(string $word): bool
    {
        if (is_callable([$this->inflector, 'isPlural'])) {
            return $this->inflector->isPlural($word);
        }

        return $this->inflector->pluralize($word) == $word;
    }

    /**
     * Imitate JoomlaInflector method for DoctrineInflector
     *
     * @param string $word
     *
     * @return bool
     */
    public function isSingular(string $word): bool
    {
        if (is_callable([$this->inflector, 'isSingular'])) {
            return $this->inflector->isSingular($word);
        }

        return $this->inflector->singularize($word) == $word;
    }
}
