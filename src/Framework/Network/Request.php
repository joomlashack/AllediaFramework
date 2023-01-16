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

namespace Alledia\Framework\Network;

defined('_JEXEC') or die();

class Request
{
    /**
     * post
     * POST request
     *
     * @access public
     *
     * @param string $url
     * @param array  $data
     *
     * @return string
     */
    public function post($url, $data = [])
    {
        if ($this->hasCURL()) {
            return $this->postCURL($url, $data);

        } else {
            return $this->postFOpen($url, $data);
        }
    }

    /**
     * hasCURL
     * Does the server have the curl extension ?
     *
     * @access protected
     * @return bool
     */
    protected function hasCURL()
    {
        return function_exists('curl_init');
    }

    /**
     * postCURL
     * POST request with curl
     *
     * @access protected
     *
     * @param string $url
     * @param array  $data
     *
     * @return string
     */
    protected function postCURL($url, $data = [])
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $contents = curl_exec($ch);
        curl_close($ch);

        return $contents;
    }

    /**
     * postFOpen
     * POST request with fopen
     *
     * @access protected
     *
     * @param string $url
     * @param array  $data
     *
     * @return string
     */
    protected function postFOpen($url, $data = [])
    {
        $stream = fopen($url, 'r', false, stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query(
                    $data
                )
            ]
        ]));

        $contents = stream_get_contents($stream);
        fclose($stream);

        return $contents;
    }
}
