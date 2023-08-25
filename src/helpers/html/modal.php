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

use Alledia\Framework\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;
use Joomla\Utilities\ArrayHelper;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die();
// phpcs:enable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

abstract class AllediaModal
{
    /**
     * @var string[]
     */
    protected static $modalFunctions = [];

    /**
     * Joomla version agnostic modal select field rendering
     *
     * @param array $options = [
     *                       'id'         => required: unique Field ID,
     *                       'name'       => required: Field name,
     *                       'link'       => required: button action,
     *                       'function'   => required: parent window close modal function,
     *                       'itemType'   => required: Unique item identifier
     *                       'title'      => Modal window title - default: JSELECT,
     *                       'hint'       => Input field value text - default: JGLOBAL_SELECT_AN_OPTION,
     *                       'button'     => Button text - default: JSELECT,
     *                       'close'      => Footer Close button text - default: JLIB_HTML_BEHAVIOR_CLOSE,
     *                       'value'      => Currently selected value,
     *                       'height'     => Modal height in pixels - default: 400,
     *                       'width'      => Modal width in pixels - default: 800,
     *                       'bodyHeight' => 70,
     *                       'modalWidth' => 80,
     *                       ]
     *
     * @return string
     */
    public static function renderSelectField(array $options): string
    {
        $required = [
            'id'       => null,
            'name'     => null,
            'link'     => null,
            'function' => null,
            'itemType' => null,
        ];

        $options = array_replace_recursive(
            $required,
            [
                'title'      => Text::_('JSELECT'),
                'hint'       => Text::_('JGLOBAL_SELECT_AN_OPTION'),
                'button'     => [
                    'select' => Text::_('JSELECT'),
                    'clear'  => Text::_('JCLEAR')
                ],
                'close'      => Text::_('JLIB_HTML_BEHAVIOR_CLOSE'),
                'value'      => null,
                'required'   => false,
                'height'     => '400px',
                'width'      => '100%',
                'bodyHeight' => 70,
                'modalWidth' => 80,
            ],
            $options
        );

        $requiredOptions = array_filter(array_intersect_key($options, $required));
        $missing         = array_diff_key($required, $requiredOptions);
        if ($missing) {
            return 'Missing Required options: ' . join(', ', array_keys($missing));
        }

        /**
         * @var string $id
         * @var string $name
         * @var string $hint
         * @var string $link
         * @var string $function
         * @var string $itemType
         */
        extract($requiredOptions);

        HTMLHelper::_('jquery.framework');

        if (Version::MAJOR_VERSION < 4) {
            HTMLHelper::_('script', 'system/modal-fields.js', ['version' => 'auto', 'relative' => true]);

        } elseif ($doc = Factory::getDocument()) {
            if (is_callable([$doc, 'getWebAssetManager'])) {
                $wa = $doc->getWebAssetManager();
                $wa->useScript('field.modal-fields');
            }

        } else {
            return 'Unable to initialize Modal window';
        }

        if (empty(static::$modalFunctions[$function])) {
            $script = <<<JSCRIPT
window.{$function} = function(id, name) {
    window.processModalSelect('{$itemType}', '{$id}', id, name);
};
JSCRIPT;
            Factory::getDocument()->addScriptDeclaration($script);

            static::$modalFunctions[$function] = true;
        }

        $title   = htmlspecialchars($options['title'], ENT_QUOTES);
        $modalId = 'ModalSelect' . $itemType . '_' . $id;

        // Begin field output
        $html = '<span class="input-append input-group">';

        // Read-only name field
        $nameAttribs = [
            'type'     => 'text',
            'id'       => $id . '_name',
            'value'    => $options['hint'],
            'class'    => 'input-medium form-control',
            'readonly' => 'readonly',
            'size'     => 35
        ];
        $html        .= sprintf('<input %s/>', ArrayHelper::toString($nameAttribs));

        // Create read-only ID field
        $idAttribs = [
            'type'          => 'hidden',
            'id'            => $id . '_id',
            'name'          => $name,
            'value'         => $options['value'],
            'data-required' => (int)(bool)$options['required'],
            'data-text'     => $title
        ];
        if ($options['required']) {
            $idAttribs['class'] = 'required modal-value';
        }

        $html .= sprintf('<input  %s/>', ArrayHelper::toString($idAttribs));

        $html .= static::createSelectButton(
            $options['value'],
            $id,
            $modalId,
            $options['button']['select'],
            $options['required']
        );

        $html .= static::createClearButton(
            $options['value'],
            $id,
            $modalId,
            $options['button']['clear'],
            $options['required']
        );

        // Modal window
        $html .= HTMLHelper::_(
            'bootstrap.renderModal',
            $modalId,
            [
                'title'      => $title,
                'url'        => $link,
                'height'     => '400px',
                'width'      => '800px',
                'bodyHeight' => '70',
                'modalWidth' => '80',
                'footer'     => '<a role="button" class="btn" data-dismiss="modal" data-bs-dismiss="modal" aria-hidden="true">'
                    . Text::_('JLIB_HTML_BEHAVIOR_CLOSE')
                    . '</a>'
                ,
            ]
        );

        return $html;
    }

    /**
     * @param string $value
     * @param string $id
     * @param string $modalId
     * @param string $text
     * @param bool   $required
     *
     * @return string
     */
    protected static function createSelectButton(
        string $value,
        string $id,
        string $modalId,
        string $text,
        bool $required
    ): string {
        $selectAttribs = [
            'class'          => 'btn btn-primary hasTooltip',
            'id'             => $id . ($required ? '_change' : '_select'),
            'data-toggle'    => 'modal',
            'data-bs-toggle' => 'modal',
            'data-bs-target' => '#' . $modalId,
            'role'           => 'button',
        ];
        if (empty($required) && $value) {
            $selectAttribs['class'] .= ' hidden';
        }

        return HTMLHelper::_(
            'link',
            '#' . $modalId,
            '<span class="icon-list" aria-hidden="true"></span> ' . $text,
            $selectAttribs
        );
    }

    /**
     * @param string $value
     * @param string $id
     * @param string $modalId
     * @param string $text
     * @param bool   $required
     *
     * @return string
     */
    protected static function createClearButton(
        string $value,
        string $id,
        string $modalId,
        string $text,
        bool $required
    ): string {
        if ($required == false) {
            $clearAttribs = [
                'class'   => 'btn btn-secondary hasTooltip',
                'id'      => $id . '_clear',
                'role'    => 'button',
                'onclick' => "window.processModalParent('{$id}');return false;",
            ];
            if (empty($value)) {
                $clearAttribs['class'] .= ' hidden';
            }

            return HTMLHelper::_(
                'link',
                '#' . $modalId,
                '<span class="icon-remove" aria-hidden="true"></span> ' . $text,
                $clearAttribs
            );
        }

        return '';
    }

    /**
     * @param string $text
     * @param string $modalId
     * @param array  $params
     *
     * @return string
     */
    public static function renderLink(string $text, string $modalId, array $params): string
    {
        $params = array_merge(
            [
                'title'      => 'MODAL!',
                'height'     => '400px',
                'width'      => '800px',
                'bodyHeight' => '70',
                'modalWidth' => '80'
            ],
            $params
        );

        $html = HTMLHelper::_('bootstrap.renderModal', $modalId, $params);

        $html .= HTMLHelper::_(
            'link',
            '#' . $modalId,
            $text,
            [
                'data-toggle'    => 'modal',
                'data-bs-toggle' => 'modal'
            ]
        );

        return $html;
    }

    /**
     * @param ?string[] $attribs
     * @param ?string   $title
     *
     * @return string
     */
    public static function footerSaveButton(array $attribs = [], ?string $title = null): string
    {
        $attribs = array_merge(
            [
                'class'           => 'btn btn-primary',
                'data-dismiss'    => 'modal',
                'data-bs-dismiss' => 'modal'
            ],
            $attribs
        );

        return static::footerButton($title ?: Text::_('JSAVE'), $attribs);
    }

    /**
     * @param ?string[] $attribs
     * @param ?string   $title
     *
     * @return string
     */
    public static function footerApplyButton(array $attribs = [], ?string $title = null): string
    {
        $attribs = array_merge(
            [
                'class' => 'btn btn-success'
            ],
            $attribs
        );

        return static::footerButton($title ?: Text::_('JAPPLY'), $attribs);
    }

    /**
     * @param ?string[] $attribs
     * @param ?string   $title
     *
     * @return string
     */
    public static function footerCloseButton(array $attribs = [], ?string $title = null): string
    {
        $attribs = array_merge(
            [
                'data-dismiss'    => 'modal',
                'data-bs-dismiss' => 'modal'
            ],
            $attribs
        );

        return static::footerButton($title ?: Text::_('JLIB_HTML_BEHAVIOR_CLOSE'), $attribs);
    }

    /**
     * @param string $title
     * @param array  $attribs
     *
     * @return string
     */
    public static function footerButton(string $title, array $attribs = []): string
    {
        $attribs = array_merge(
            [
                'role'        => 'button',
                'class'       => 'btn',
                'aria-hidden' => 'true'
            ],
            $attribs
        );

        return sprintf(
            '<a %s>%s</a>',
            ArrayHelper::toString($attribs),
            $title
        );
    }
}
