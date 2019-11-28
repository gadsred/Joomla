<?php
/**
 * @version     1.0.0
 * @package     com_mtprice
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Price controller class.
 */
class MtpriceControllerPrice extends JControllerForm
{

    function __construct() {
        $this->view_list = 'prices';
        parent::__construct();
    }

}