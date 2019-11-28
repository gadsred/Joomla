<?php
/**
 * @version     1.0.0
 * @package     com_mtprice
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

/**
 * Mtprice model.
 */
class MtpriceModelPriceForm extends JModelForm
{
    
    var $_item = null;
    
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('com_mtprice');

		// Load state from the request userState on edit or from the passed variable on default
        if (JFactory::getApplication()->input->get('layout') == 'edit') {
            $id = JFactory::getApplication()->getUserState('com_mtprice.edit.price.id');
        } else {
            $id = JFactory::getApplication()->input->get('id');
            JFactory::getApplication()->setUserState('com_mtprice.edit.price.id', $id);
        }
		$this->setState('price.id', $id);

		// Load the parameters.
        $params = $app->getParams();
        $params_array = $params->toArray();
        if(isset($params_array['item_id'])){
            $this->setState('price.id', $params_array['item_id']);
        }
		$this->setState('params', $params);

	}
	
	//gads get previous key points
	public function getKpoints()
	{
		$jinput = JFactory::getApplication()->input;
		
		$filstate=$jinput->get('filstate', '', '');
		
		$price_type=$jinput->get('ptype', '', '');
		$key_type=$jinput->get('ktype', '', '');
		$state=$jinput->get('state', '', '');
		$sid=$jinput->get('sid', '', '');
		$user_id=JFactory::getUser()->id;
		
		//get current link id
		$db = JFactory::getDBO();
		$query = "Select link_id From #__users where id='$user_id'";
		$db->setQuery($query);
		$db->loadObject();
		$id = $db->loadObject();
		$link_id=$id->link_id;
		
		
		// if(!empty($ptype) and !empty($ktype) and !empty($state))
		// {
			// $db =  JFactory::getDbo();
			// $query = "SELECT * FROM #__mt_price where price_type='$ptype' and keypoints_type='$ktype' and user_id='$user' and au_state like'$state'";
			// $db->setQuery($query);
			// $row = $db->loadObjectList();
			// return $row;
		// }
		
			$db =  JFactory::getDbo();
			if(empty($filstate))
			{
				$query = "SELECT * FROM #__mt_price where link_id='{$link_id}' and keypoints_type !='3' and price_type='$price_type' and keypoints_type ='$key_type' and au_state!='$state' group by description";
			}
			else
			{
				$query = "SELECT * FROM #__mt_price where link_id='{$link_id}' and keypoints_type !='3' and price_type='$price_type' and keypoints_type ='$key_type' and au_state='$filstate' group by description";
			}
			//var_dump($query);exit();
			$db->setQuery($query);
			//var_dump($query);exit();
			return $db->loadObjectList();
			
		    // Redirect to the list screen.
					$this->setMessage(JText::_('COM_MTPRICE_ITEM_SAVED_SUCCESSFULLY'));
					$menu = JFactory::getApplication()->getMenu();
					$item = $menu->getActive();
					$url = (empty($item->link) ? 'index.php?option=com_mtprice&view=priceform&layout=edit&id='.$sid.'&state='.$state.'&itemid=229&ktype='.$key_type.'ptype='.$price_type : $item->link);
					$this->setRedirect(JRoute::_($url.'&state='.$state , false)); 
		
		
	}
	
	
        

	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function &getData($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id)) {
				$id = $this->getState('price.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($id))
			{
                
                $user = JFactory::getUser();
                $id = $table->id;
                $canEdit = $user->authorise('core.edit', 'com_mtprice') || $user->authorise('core.create', 'com_mtprice');
                if (!$canEdit && $user->authorise('core.edit.own', 'com_mtprice')) {
                    $canEdit = $user->id == $table->created_by;
                }

                if (!$canEdit) {
                    JError::raiseError('500', JText::_('JERROR_ALERTNOAUTHOR'));
                }
                
				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published) {
						return $this->_item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(1);
				$this->_item = JArrayHelper::toObject($properties, 'JObject');
			} elseif ($error = $table->getError()) {
				$this->setError($error);
			}
		}

		return $this->_item;
	}
    
	public function getTable($type = 'Price', $prefix = 'MtpriceTable', $config = array())
	{   
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR.'/tables');
        return JTable::getInstance($type, $prefix, $config);
	}     

    
	/**
	 * Method to check in an item.
	 *
	 * @param	integer		The id of the row to check out.
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int)$this->getState('price.id');

		if ($id) {
            
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
            if (method_exists($table, 'checkin')) {
                if (!$table->checkin($id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
		}

		return true;
	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param	integer		The id of the row to check out.
	 * @return	boolean		True on success, false on failure.
	 * @since	1.6
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int)$this->getState('price.id');

		if ($id) {
            
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = JFactory::getUser();

			// Attempt to check the row out.
            if (method_exists($table, 'checkout')) {
                if (!$table->checkout($user->get('id'), $id)) {
                    $this->setError($table->getError());
                    return false;
                }
            }
		}

		return true;
	}    
    
	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML 
     * 
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_mtprice.price', 'priceform', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_mtprice.edit.price.data', array());
        if (empty($data)) {
            $data = $this->getData();
        }
        
        return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array		The form data.
	 * @return	mixed		The user id on success, false on failure.
	 * @since	1.6
	 */
	public function save($data)
	{
		$id = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('price.id');
        $state = (!empty($data['state'])) ? 1 : 0;
        $user = JFactory::getUser();

		
        if($id) {
            //Check the user can edit this item
            $authorised = $user->authorise('core.edit', 'com_mtprice') || $authorised = $user->authorise('core.edit.own', 'com_mtprice');
            if($user->authorise('core.edit.state', 'com_mtprice') !== true && $state == 1){ //The user cannot edit the state of the item.
                $data['state'] = 0;
            }
        } else {
            //Check the user can create new items in this section
            $authorised = $user->authorise('core.create', 'com_mtprice');
            if($user->authorise('core.edit.state', 'com_mtprice') !== true && $state == 1){ //The user cannot edit the state of the item.
                $data['state'] = 0;
            }
        }

        if ($authorised !== true) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }
        
        $table = $this->getTable();
        if ($table->save($data) === true) {
            return $table->id;
        } else {
            return false;
        }
        
	}
    
     function delete($data)
    {
        $id = (!empty($data['id'])) ? $data['id'] : (int)$this->getState('price.id');
        if(JFactory::getUser()->authorise('core.delete', 'com_mtprice') !== true){
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }
        $table = $this->getTable();
        if ($table->delete($data['id']) === true) {
            return $id;
        } else {
            return false;
        }
        
        return true;
    }
    
}

	