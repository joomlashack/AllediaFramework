<?php
/**
 * @package   OSCampus
 * @contact   www.joomlashack.com, help@joomlashack.com
 * @copyright 2015-2023 Joomlashack.com. All rights reserved
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
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
 * along with OSCampus.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Alledia\Framework\Joomla\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Response\JsonResponse;

defined('_JEXEC') or die();

abstract class Json extends Base
{
    /**
     * @inheritDoc
     * @throws \Exception`
     */
    public function checkToken($method = 'post', $redirect = false)
    {
        $valid = parent::checkToken($method, $redirect);

        if (!$valid) {
            throw new \Exception(Text::_('JINVALID_TOKEN'), 403);
        }

        return true;
    }

    /**
     * Sends a json package to output. All php processing ended to prevent any
     * plugin processing that might slow things down or waste memory.
     *
     * @param ?string|\Throwable $message
     *
     * @return void
     */
    protected function returnJson($message = null)
    {
        $result = new JsonResponse();

        if ($message) {
            if ($message instanceof \Throwable) {
                $result->success = false;
                $result->message = $message->getMessage();

                $result->data = [
                    'file' => $message->getFile(),
                    'line' => $message->getLine()
                ];
            } else {
                $result->message = $message;
            }
        }

        header('Content-Type: application/json');
        echo $result;

        jexit();
    }
}
