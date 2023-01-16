<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2016-2023 Joomlashack.com. All rights reserved
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

namespace Alledia\Framework\Joomla\Extension;

defined('_JEXEC') or die();

use Alledia\Framework\AutoLoader;


/**
 * Licensed class, for extensions with Free and Pro versions
 */
class Licensed extends Generic
{
    /**
     * License type: free or pro
     *
     * @var string
     */
    protected $license = null;

    /**
     * The path for the pro library
     *
     * @var string
     */
    protected $proLibraryPath = null;

    /**
     * The path for the free library
     *
     * @var string
     */
    protected $libraryPath = null;

    /**
     * @inheritDoc
     */
    public function __construct($namespace, $type, $folder = '', $basePath = JPATH_SITE)
    {
        parent::__construct($namespace, $type, $folder, $basePath);

        $this->license   = strtolower($this->manifest->alledia->license ?? '');
        $this->namespace = $this->manifest->alledia->namespace ?? '';

        $this->getLibraryPath();
        $this->getProLibraryPath();
    }

    /**
     * Check if the license is pro
     *
     * @return bool
     */
    public function isPro(): bool
    {
        return $this->license === 'pro';
    }

    /**
     * Check if the license is free
     *
     * @return bool
     */
    public function isFree(): bool
    {
        return !$this->isPro();
    }

    /**
     * Get the include path for the free library, based on the extension type
     *
     * @return string The path for pro
     */
    public function getLibraryPath()
    {
        if ($this->libraryPath === null) {
            $basePath = $this->getExtensionPath();

            $this->libraryPath = $basePath . '/library';
        }

        return $this->libraryPath;
    }

    /**
     * Get the include path the pro library, based on the extension type
     *
     * @return string The path for pro
     */
    public function getProLibraryPath()
    {
        if (empty($this->proLibraryPath)) {
            $basePath = $this->getLibraryPath();

            $this->proLibraryPath = $basePath . '/Pro';
        }

        return $this->proLibraryPath;
    }

    /**
     * Loads the library, if existent (including the Pro Library)
     *
     * @return bool
     */
    public function loadLibrary()
    {
        if ($this->namespace) {
            $libraryPath = $this->getLibraryPath();

            if (is_dir($libraryPath)) {
                AutoLoader::register('Alledia\\' . $this->namespace, $libraryPath);

                return true;
            }
        }

        return false;
    }
}
