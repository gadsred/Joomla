<?php

/**
 * @version     1.0.0
 * @package     com_financial
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class FinancialController extends JControllerLegacy {

    /**
     * Method to display a view.
     *
     * @param	boolean			$cachable	If true, the view output will be cached
     * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false) {
        require_once JPATH_COMPONENT . '/helpers/financial.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'pastinvoices');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }
	
	public function invoice()
	{   
		$statement_id=JRequest::getVar('id');
		$app=JFactory::getApplication();
		
		$db=JFactory::getDBO();
		$query="SELECT id FROM  #__sh404sef_urls WHERE oldurl LIKE '%".$statement_id."/view,pastinvoice%'"; 
		$db->setQuery($query);
		$sef_id=$db->loadObject()->id;
		
			
		
		if($sef_id)
		{
			$db=JFactory::getDBO();
			$query="Update #__sh404sef_urls SET newurl='index.php?option=com_financial&view=pastinvoice&id=$statement_id' WHERE id=".$sef_id;
			$db->setQuery($query);
			$db->execute();
			
		}
		else
		{ 
			$oldurl="component/com_financial/id,$statement_id/view,pastinvoice/";
			$newurl="index.php?option=com_financial&view=pastinvoice&id=$statement_id";
			$db=JFactory::getDBO();
			$query="insert into `#__sh404sef_urls` (cpt,referrer_type,oldurl,newurl) values ('1','1','{$oldurl}','{$newurl}')";
			$db->setQuery($query);
			$db->execute();
		}
			$app->redirect('index.php?option=com_financial&view=pastinvoice&id='.$statement_id);
	}

}
