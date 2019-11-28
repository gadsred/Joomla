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

jimport('joomla.application.component.controller');

class MtpriceController extends JControllerLegacy {

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
        require_once JPATH_COMPONENT . '/helpers/mtprice.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'prices');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }
	
	function holdstate()
	{
		$jinput = JFactory::getApplication()->input;
		$state=$jinput->get('state', '', '');
		
		$session =& JFactory::getSession();
		if(!empty($state))
		{
			
			$session->set('state',$state);
		}
		
		$state=$session->get('state','');
		print_r( $state);
	}
	
	function nopricestate()
	{
		$link_id=JFactory::getUser()->link_id;
		$jinput = JFactory::getApplication()->input;
		$price_type=$jinput->get('ptype', '', '');
		$key_type=$jinput->get('ktype', '', '');
		$state=$jinput->get('state', '', '');
		
		$db =  JFactory::getDbo();
		$query = "SELECT au_state FROM #__mt_price where price_type='$price_type' and keypoints_type='$key_type' and link_id='$link_id' and au_state like'$state'";
		$db->setQuery($query);
		$row = $db->loadObject();
		//$count=1;
		// foreach($row as $key => $val)
		// {
			// if($count==count($row))
				// {
					// $state.=$val->au_state;
				// }
				// else
				// {
					// $state.=$val->au_state.',';
				// }
			// $count++;
		// }
		
		print_r($row->au_state);
	}
	
	public function bfilter()
	{
		$jinput = JFactory::getApplication()->input;
		$state=$jinput->get('state', '', '');
		$unhide=$jinput->get('hide', '', '');
		$link_id=$jinput->get('link_id', '', '');
		$session =& JFactory::getSession();
		$search_result=$session->get( 'buyers','');
		
		if($unhide=='0')			
		{
			$db=JFactory::getDBO();
			$query = "SELECT * FROM #__mt_price Where price_type='b' and link_id='$link_id' and au_state not like'".$state."'";
			$db->setQuery($query);
			$row = $db->loadObjectList();
			print_r (json_encode($row));
		}else
		{
			print_r (json_encode($search_result));
			
		}
	}
	
	public function sfilter()
	{
		$jinput = JFactory::getApplication()->input;
		$state=$jinput->get('state', '', '');
		$unhide=$jinput->get('hide', '', '');
		$link_id=$jinput->get('link_id', '', '');
		$session =& JFactory::getSession();
		$search_result=$session->get( 'sellers','');
		
		if($unhide=='0')			
		{
			$db=JFactory::getDBO();
			$query = "SELECT * FROM #__mt_price Where price_type='s' and link_id='$link_id' and au_state not like'".$state."'";
			$db->setQuery($query);
			$row = $db->loadObjectList();
			print_r (json_encode($row));
		}else
		{
			print_r (json_encode($search_result));
			
		}
	}
	
	public function add_include_charges()
	{
		$id = JRequest::getVar('id');
		$included_charges = JRequest::getVar('included_charges');
		$db = JFactory::getDBO();
		$query="Update #__mt_price Set included_charges='$included_charges' Where id='$id'";
		$db->setQuery($query);
		$response=$db->execute();
		echo $response;
		
	}
	
	public function add_extra_charges()
	{
		$id = JRequest::getVar('id');
		$extra_charges = JRequest::getVar('extra_charges');
		$db = JFactory::getDBO();
		$query="Update #__mt_price Set extra_charges='$extra_charges' Where id='$id'";
		$db->setQuery($query);
		$response=$db->execute();
		echo $response;
		
	}

}
