<?php

/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2025 Joomlashack.com. All rights reserved
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

use Doctrine\Inflector\InflectorFactory;
use Doctrine\Inflector\Rules\Patterns;
use Doctrine\Inflector\Rules\Ruleset;
use Doctrine\Inflector\Rules\Substitution;
use Doctrine\Inflector\Rules\Substitutions;
use Doctrine\Inflector\Rules\Transformations;
use Doctrine\Inflector\Rules\Word;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

// phpcs:enable PSR1.Files.SideEffects

/**
 * Provide Joomla version agnostic Inflector
 */
class Inflector
{
    /**
     * @var string[]
     */
    protected array $methods = [
        'pluralize'   => 'toPlural',
        'singularize' => 'toSingular',
    ];

    /**
     * @var \Doctrine\Inflector\Inflector|\Joomla\String\Inflector
     */
    protected $coreInflector;

    public function __construct(array $customWords = [])
    {
        if (class_exists(InflectorFactory::class)) {
            $inflector = InflectorFactory::create();
            if ($customWords) {
                $singles = $this->buildSubstitutions($customWords);
                $plurals = $this->buildSubstitutions(array_flip($customWords));
                $inflector
                    ->withSingularRules(
                        new Ruleset(
                            new Transformations(),
                            new Patterns(),
                            new Substitutions(...$singles)
                        )
                    );
            }
            $this->coreInflector = $inflector->build();

        } else {
            $this->coreInflector = \Joomla\String\Inflector::getInstance(true);
            foreach ($customWords as $singular => $plural) {
                $this->coreInflector->addWord($singular, $plural);
            }
        }
    }

    /**
     * @param string[] $words
     *
     * @return Substitution[]
     */
    protected function buildSubstitutions(array $words): array
    {
        $substitutions = [];
        foreach ($words as $source => $target) {
            $substitutions[] = new Substitution(new Word($source), new Word($target));
        }

        return $substitutions;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $method = is_callable([$this->coreInflector, $name])
            ? $name
            : ($this->methods[$name] ?? null);

        if (is_callable([$this->coreInflector, $method])) {
            return call_user_func_array([$this->coreInflector, $method], $arguments);
        }

        throw new \Error(
            sprintf('Call to undefined method %s::%s()', get_class($this), $name)
        );
    }

    /**
     * @param string $word
     *
     * @return bool
     */
    public function isPlural(string $word): bool
    {
        if (is_callable([$this->coreInflector, 'isPlural'])) {
            return call_user_func([$this->coreInflector, 'isPlural'], $word);
        }

        $pluralWord = $this->coreInflector->pluralize($word);

        return $pluralWord === $word;
    }

    /**
     * @param string $word
     *
     * @return bool
     */
    public function isSingular(string $word): bool
    {
        if (is_callable([$this->coreInflector, 'isSingular'])) {
            return call_user_func([$this->coreInflector, 'isSingular'], $word);
        }

        $singularWord = $this->coreInflector->singularize($word);

        return $singularWord === $word;
    }
}
