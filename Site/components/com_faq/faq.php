<?php
/**
 * @version     1.0.0
 * @package     com_faq
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */

defined('_JEXEC') or die;

		$doc = JFactory::getDocument();
		$doc->addCustomTag("<meta name=\"robots\" content=\"noindex,nofollow\">");
// Include dependancies
jimport('joomla.application.component.controller');

// Execute the task.
$controller	= JControllerLegacy::getInstance('Faq');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
