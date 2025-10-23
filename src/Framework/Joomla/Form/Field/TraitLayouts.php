<?php

/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2021-2023 Joomlashack.com. All rights reserved
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

namespace Alledia\Framework\Joomla\Form\Field;

use Joomla\CMS\Version;
use Joomla\Filesystem\Path;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

// phpcs:enable PSR1.Files.SideEffects

/**
 * Intended for use by form field classes to
 * implement Joomla version targeted layout files
 */
trait TraitLayouts
{
    /**
     * @inheritDoc
     */
    protected function getLayoutPaths()
    {
        if (is_callable(parent::class . '::getLayoutPaths')) {
            $paths = parent::getLayoutPaths();

            $fieldClass    = (new \ReflectionClass($this));
            $baseDirectory = dirname($fieldClass->getFileName());

            array_unshift($paths, $baseDirectory . '/layouts');

            return $paths;
        }

        return [];
    }

    /**
     * @inheritDoc
     */
    protected function getRenderer($layoutId = 'default')
    {
        if (is_callable(parent::class . '::getRenderer')) {
            $paths = $this->getLayoutPaths();

            if (Version::MAJOR_VERSION < 4) {
                if (Path::find($paths, str_replace('.', '/', $layoutId) . '_j3.php')) {
                    $layoutId .= '_j3';
                }
            }

            $renderer = parent::getRenderer($layoutId);

            $renderer->setIncludePaths($paths);

            return $renderer;
        }

        return null;
    }

    /**
     * @return void
     * @deprecated v3.3.1
     */
    protected function setListLayout()
    {
        if (Version::MAJOR_VERSION >= 4) {
            $this->layout = 'joomla.form.field.list-fancy-select';
        }
    }
}
