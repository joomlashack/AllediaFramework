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

namespace Alledia\Framework\Content;

use Alledia\Framework\Base;

defined('_JEXEC') or die();

class Text extends Base
{
    public $content = '';

    /**
     * Constructor method, that defines the internal content
     *
     * @param string $content
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Extract multiple {mytag} tags from the content
     *
     * @todo Recognize unclose tags like {dumbtag param1="12"}
     *
     * @param  string $tagName
     *
     * @return array  An array with all tags {tagName} found on the text
     */
    protected function extractPluginTags($tagName)
    {
        preg_match_all(Tag::getRegex($tagName), $this->content, $matches);

        return $matches[0];
    }

    /**
     * Extract multiple {mytag} tags from the content, returning
     * as Tag instances
     *
     * @param  string $tagName
     *
     * @return Tag[]  An array with all tags {tagName} found on the text
     */
    public function getPluginTags($tagName)
    {
        $unparsedTags = $this->extractPluginTags($tagName);

        $tags = [];
        foreach ($unparsedTags as $unparsedTag) {
            $tags[] = new Tag($tagName, $unparsedTag);
        }

        return $tags;
    }

    /**
     * Extract multiple {mytag} tags from the content, returning
     * as Tag instances
     *
     * @param  string $tagName
     *
     * @return array  An array with all tags {tagName} found on the text
     * @deprecated 1.3.1 Use getPluginsTags instead
     */
    public function getTags($tagName)
    {
        // Deprecated. Use getPluginTags instead
        return $this->getPluginTags($tagName);
    }
}
