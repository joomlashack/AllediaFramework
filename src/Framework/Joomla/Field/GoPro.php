<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');


class JFormFieldGoPro extends JFormField
{
    public function getInput() {
        JHtml::stylesheet( JURI::root() . 'libraries/allediaframework/Framework/assets/css/style.css' );
        $html = '<div class="ost-alert-gopro ' . $this->class . '">
            <a href="https://www.alledia.com/plans/" class="ost-alert-btn" target="_blank">
            <i class="icon-publish"></i> Go Pro to access more features</a>
            <img src="../libraries/allediaframework/Framework/assets/images/alledia_logo.png" style="width:120px;height:auto;" alt=""/>
        </div>';

		return $html;
	}

    public function getLabel(){
        return '';
    }
}
