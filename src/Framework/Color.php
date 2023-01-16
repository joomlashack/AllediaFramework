<?php
/**
 * @package   AllediaFramework
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2022-2023 Joomlashack.com. All rights reserved
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

namespace Alledia\Framework;

use Joomla\String\StringHelper;

defined('_JEXEC') or die();

abstract class Color
{
    /**
     * 140 color names recognized by browsers (Aug 2022)
     *
     * @var string[]
     */
    protected static $colorNames = [
        'aliceblue'            => '#F0F8FF',
        'antiquewhite'         => '#FAEBD7',
        'aqua'                 => '#00FFFF',
        'aquamarine'           => '#7FFFD4',
        'azure'                => '#F0FFFF',
        'beige'                => '#F5F5DC',
        'bisque'               => '#FFE4C4',
        'black'                => '#000000',
        'blanchedalmond'       => '#FFEBCD',
        'blue'                 => '#0000FF',
        'blueviolet'           => '#8A2BE2',
        'brown'                => '#A52A2A',
        'burlywood'            => '#DEB887',
        'cadetblue'            => '#5F9EA0',
        'chartreuse'           => '#7FFF00',
        'chocolate'            => '#D2691E',
        'coral'                => '#FF7F50',
        'cornflowerblue'       => '#6495ED',
        'cornsilk'             => '#FFF8DC',
        'crimson'              => '#DC143C',
        'cyan'                 => '#00FFFF',
        'darkblue'             => '#00008B',
        'darkcyan'             => '#008B8B',
        'darkgoldenrod'        => '#B8860B',
        'darkgray'             => '#A9A9A9',
        'darkgreen'            => '#006400',
        'darkkhaki'            => '#BDB76B',
        'darkmagenta'          => '#8B008B',
        'darkolivegreen'       => '#556B2F',
        'darkorange'           => '#FF8C00',
        'darkorchid'           => '#9932CC',
        'darkred'              => '#8B0000',
        'darksalmon'           => '#E9967A',
        'darkseagreen'         => '#8FBC8F',
        'darkslateblue'        => '#483D8B',
        'darkslategray'        => '#2F4F4F',
        'darkturquoise'        => '#00CED1',
        'darkviolet'           => '#9400D3',
        'deeppink'             => '#FF1493',
        'deepskyblue'          => '#00BFFF',
        'dimgray'              => '#696969',
        'dodgerblue'           => '#1E90FF',
        'firebrick'            => '#B22222',
        'floralwhite'          => '#FFFAF0',
        'forestgreen'          => '#228B22',
        'fuchsia'              => '#FF00FF',
        'gainsboro'            => '#DCDCDC',
        'ghostwhite'           => '#F8F8FF',
        'gold'                 => '#FFD700',
        'goldenrod'            => '#DAA520',
        'gray'                 => '#808080',
        'green'                => '#008000',
        'greenyellow'          => '#ADFF2F',
        'honeydew'             => '#F0FFF0',
        'hotpink'              => '#FF69B4',
        'indianred'            => '#CD5C5C',
        'indigo'               => '#4B0082',
        'ivory'                => '#FFFFF0',
        'khaki'                => '#F0E68C',
        'lavender'             => '#E6E6FA',
        'lavenderblush'        => '#FFF0F5',
        'lawngreen'            => '#7CFC00',
        'lemonchiffon'         => '#FFFACD',
        'lightblue'            => '#ADD8E6',
        'lightcoral'           => '#F08080',
        'lightcyan'            => '#E0FFFF',
        'lightgoldenrodyellow' => '#FAFAD2',
        'lightgrey'            => '#D3D3D3',
        'lightgreen'           => '#90EE90',
        'lightpink'            => '#FFB6C1',
        'lightsalmon'          => '#FFA07A',
        'lightseagreen'        => '#20B2AA',
        'lightskyblue'         => '#87CEFA',
        'lightslategray'       => '#778899',
        'lightsteelblue'       => '#B0C4DE',
        'lightyellow'          => '#FFFFE0',
        'lime'                 => '#00FF00',
        'limegreen'            => '#32CD32',
        'linen'                => '#FAF0E6',
        'magenta'              => '#FF00FF',
        'maroon'               => '#800000',
        'mediumaquamarine'     => '#66CDAA',
        'mediumblue'           => '#0000CD',
        'mediumorchid'         => '#BA55D3',
        'mediumpurple'         => '#9370D8',
        'mediumseagreen'       => '#3CB371',
        'mediumslateblue'      => '#7B68EE',
        'mediumspringgreen'    => '#00FA9A',
        'mediumturquoise'      => '#48D1CC',
        'mediumvioletred'      => '#C71585',
        'midnightblue'         => '#191970',
        'mintcream'            => '#F5FFFA',
        'mistyrose'            => '#FFE4E1',
        'moccasin'             => '#FFE4B5',
        'navajowhite'          => '#FFDEAD',
        'navy'                 => '#000080',
        'oldlace'              => '#FDF5E6',
        'olive'                => '#808000',
        'olivedrab'            => '#6B8E23',
        'orange'               => '#FFA500',
        'orangered'            => '#FF4500',
        'orchid'               => '#DA70D6',
        'palegoldenrod'        => '#EEE8AA',
        'palegreen'            => '#98FB98',
        'paleturquoise'        => '#AFEEEE',
        'palevioletred'        => '#D87093',
        'papayawhip'           => '#FFEFD5',
        'peachpuff'            => '#FFDAB9',
        'peru'                 => '#CD853F',
        'pink'                 => '#FFC0CB',
        'plum'                 => '#DDA0DD',
        'powderblue'           => '#B0E0E6',
        'purple'               => '#800080',
        'red'                  => '#FF0000',
        'rosybrown'            => '#BC8F8F',
        'royalblue'            => '#4169E1',
        'saddlebrown'          => '#8B4513',
        'salmon'               => '#FA8072',
        'sandybrown'           => '#F4A460',
        'seagreen'             => '#2E8B57',
        'seashell'             => '#FFF5EE',
        'sienna'               => '#A0522D',
        'silver'               => '#C0C0C0',
        'skyblue'              => '#87CEEB',
        'slateblue'            => '#6A5ACD',
        'slategray'            => '#708090',
        'snow'                 => '#FFFAFA',
        'springgreen'          => '#00FF7F',
        'steelblue'            => '#4682B4',
        'tan'                  => '#D2B48C',
        'teal'                 => '#008080',
        'thistle'              => '#D8BFD8',
        'tomato'               => '#FF6347',
        'turquoise'            => '#40E0D0',
        'violet'               => '#EE82EE',
        'wheat'                => '#F5DEB3',
        'white'                => '#FFFFFF',
        'whitesmoke'           => '#F5F5F5',
        'yellow'               => '#FFFF00',
        'yellowgreen'          => '#9ACD32',
    ];

    /**
     * Darken or lighten a color from a named or hex color string. Leading
     * hash mark is optional and the result will be returned in the
     * same format.
     *
     * $percent == 100 means no change.
     * $percent > 100 means lighten
     * $percent < 100 means darken
     *
     * Since the math will generate floating point numbers, and we need integers,
     * by default the result will be the next highest integer. More subtle results
     * may be obtained by using rounding.
     *
     * @see https://gist.github.com/stephenharris/5532899
     *
     * @param string $color
     * @param float  $percent
     * @param bool   $useRounding
     *
     * @return string
     */
    public static function adjust(string $color, float $percent, ?bool $useRounding = false): string
    {
        $hexColor = static::nameToHex($color);
        if (empty($hexColor)) {
            // Bad color string given
            return $color;
        }

        $hash   = strpos($color, '#') === 0 ? '#' : '';
        $rawHex = str_replace('#', '', $hexColor);

        switch (strlen($rawHex)) {
            case 3:
                $color = array_map(
                    function ($hex) {
                        return $hex . $hex;
                    },
                    str_split($rawHex)
                );
                break;

            case 6:
                $color = str_split($rawHex, 2);
                break;

            default:
                // It's just wrong, so just send it back
                return $color;
        }

        $color = array_map('hexdec', $color);

        foreach ($color as &$value) {
            $value = $value * ($percent / 100);
        }

        $color = array_map(
            function ($value) use ($useRounding) {
                $value = $useRounding ? round($value) : ceil($value);

                return str_pad(dechex($value), 2, '0', STR_PAD_LEFT);
            },
            $color
        );

        return $hash . join('', $color);
    }

    /**
     * Determine if a color is brighter than some midpoint. By default,
     * half of pure white (255/2) is used
     *
     * See: https://www.w3.org/TR/AERT/#color-contrast
     *
     * @param ?string $color
     * @param ?float  $midpoint
     *
     * @return bool
     */
    public static function isTooBright(?string $color, float $midpoint = 127.5): bool
    {
        $rgb = static::hexToRgb(static::nameToHex($color));
        if ($rgb) {
            $whiteValue = (($rgb['r'] * .299) + ($rgb['g'] * .587) + ($rgb['b'] * .114));

            return $whiteValue > $midpoint;
        }

        return false;
    }

    /**
     * Converts a 3 or 6 character hex color to an rgb array
     *
     * @param ?string $hex
     *
     * @return ?string[]
     */
    public static function hexToRgb(?string $hex): ?array
    {
        if ($hex) {
            // Strip hash if needed
            if (strpos($hex, '#') === 0) {
                $hex = trim($hex, '#');
            }

            $hexType = StringHelper::strlen($hex);
            if (in_array($hexType, [3, 6]) && preg_match('/[0-9a-fA-F]/', $hex)) {
                // Valid hex code in short or full form
                switch ($hexType) {
                    case 3:
                        $rgb = array_map(
                            function ($c) {
                                return str_repeat($c, 2);
                            },
                            StringHelper::str_split($hex, 1)
                        );
                        break;

                    case 6:
                        $rgb = StringHelper::str_split($hex, 2);
                        break;
                }

                return array_map(
                    'hexdec',
                    array_combine(
                        ['r', 'g', 'b'],
                        $rgb
                    )
                );
            }

            return null;
        }
    }

    /**
     * @param string $name
     *
     * @return ?string
     */
    public static function nameToHex(string $name): ?string
    {
        $hex = null;

        $hashtag = strpos($name, '#');
        if ($hashtag === false) {
            $hex = static::$colorNames[strtolower($name)] ?? $hex;

        } elseif ($hashtag === 0) {
            $hex = $name;

        } elseif (in_array(strlen($name), [3, 6])) {
            $hex = '#' . $name;
        }

        return $hex;
    }
}
