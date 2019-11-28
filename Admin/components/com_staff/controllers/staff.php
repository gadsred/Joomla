<?php
/**
 * @version     1.0.0
 * @package     com_staff
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Staff controller class.
 */
class StaffControllerStaff extends JControllerForm
{

    function __construct() {
        $this->view_list = 'staffs';
        parent::__construct();
    }

}