<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Mtlinked_listings
 * @author     gadiel_Rojo <gadsred@gmail.com>
 * @copyright  Copyright (C) 2016. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

		$doc = JFactory::getDocument();
		$doc->addCustomTag("<meta name=\"robots\" content=\"noindex,nofollow\">");
// Include dependancies
jimport('joomla.application.component.controller');

JLoader::register('Mtlinked_listingsFrontendHelper', JPATH_COMPONENT . '/helpers/mtlinked_listings.php');

// Execute the task.
$controller = JControllerLegacy::getInstance('Mtlinked_listings');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
