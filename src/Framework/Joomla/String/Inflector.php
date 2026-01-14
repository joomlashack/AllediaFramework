<?php

/**
 * @package   OSCampus
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2015-2026 Joomlashack.com. All rights reserved
 * @license   https://www.gnu.org/licenses/gpl.html GNU/GPL
 *
 * This file is part of OSCampus.
 *
 * OSCampus is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * OSCampus is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OSCampus.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Alledia\Framework\Joomla\String;

use Doctrine\Inflector\Inflector as DoctrineInflector;
use Doctrine\Inflector\InflectorFactory;
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
     * @throws \Exception
     */
    public function __construct(array $customRules = [])
    {
        if (class_exists(DoctrineInflector::class)) {
            // @TODO: Implement custom rules for Doctrine Inflector
            $this->inflector = InflectorFactory::create()->build();

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
            return $this->inflector->toPlural($word);
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
            return $this->inflector->toSingular($word);
        }

        return $this->inflector->singularize($word);
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
