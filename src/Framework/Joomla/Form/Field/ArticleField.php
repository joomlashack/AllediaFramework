<?php

/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2026 Joomlashack.com. All rights reserved
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

use Joomla\Component\Content\Administrator\Field\Modal\ArticleField as JoomlaArticleField;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();

if (class_exists(JoomlaArticleField::class) == false) {
    @include_once JPATH_ADMINISTRATOR . '/components/com_content/models/fields/modal/article.php';
    if (class_exists(\JFormFieldModal_Article::class)) {
        class_alias(\JFormFieldModal_Article::class, JoomlaArticleField::class);
    }
}
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

class ArticleField extends JoomlaArticleField
{
    use TraitLayouts;
}
