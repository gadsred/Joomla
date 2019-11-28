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

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Price controller class.
 */
class MtpriceControllerPriceForm extends MtpriceController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$price_type=$jinput->get('ptype', '', '');
		$key_type=$jinput->get('ktype', '', '');
		$state=$jinput->get('state', '', '');
		$filstate=$jinput->get('filstate', '', '');
		

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_mtprice.edit.price.id');
        $editId = $app->input->getInt('id', null, 'array');

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_mtprice.edit.price.id', $editId);

        // Get the model.
        $model = $this->getModel('PriceForm', 'MtpriceModel');
		
		
        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
	$this->setRedirect(JRoute::_('index.php?option=com_mtprice&view=priceform&layout=edit&Itemid=224&ptype='.$price_type.'&ktype='.$key_type.'&state='.$state.'&filstate='.$filstate.'&id=0'));
    }
	
	//gads get prev keypoints
	public function prevpoints()
	{   
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$price_type=$jinput->get('ptype', '', '');
		$key_type=$jinput->get('ktype', '', '');
		$state=$jinput->get('state', '', '');
		
		 // Get the model.
        $model = $this->getModel('PriceForm', 'MtpriceModel');
		
		if(!empty($price_type) and !empty($key_type) and !empty($state))
		{
			$model->getKpoints($price_type,$key_type,$state);
		}
		else{
			return false;
		}
		
				
	}
	
	//gads copy keypoints
	public function copy() {
       

        // Initialise variables.
        $app = JFactory::getApplication();
        $state=JRequest::getVar('state');

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');
		
			$id=implode(",",$data['prevpoints']);
		
			// $db =  JFactory::getDbo();
			// $query = "SELECT * FROM #__mt_price where id in({$id})";
			// $db->setQuery($query);
			// $row=$db->loadObjectList();
			
			$tcount=count($row);
			
			$count=1;
			
			// foreach($row as $key => $val)
			// {
				// if($count==$tcount)
				// {
					// $value.="('','$val->ordering','$val->state','$val->user_id','$val->au_state','$val->description','$val->price_type','$val->keypoints_type','$val->price')";
				// }else{
					// $value.="('','$val->ordering','$val->state','$val->user_id','$val->au_state','$val->description','$val->price_type','$val->keypoints_type','$val->price'),";
				// }
				
				// $count++;
				
			// }
			
				$db =  JFactory::getDbo();
				$query = "INSERT INTO #__mt_price (id, ordering, state, user_id, au_state, description, price_type, keypoints_type, price) SELECT '', ordering, state, user_id, '$state', description, price_type, keypoints_type, price FROM #__mt_price where id in({$id})";
				$db->setQuery($query);
				$db->execute();
				
				 // Redirect to the list screen.
					$this->setMessage(JText::_('COM_MTPRICE_ITEM_SAVED_SUCCESSFULLY'));
					$menu = JFactory::getApplication()->getMenu();
					$item = $menu->getActive();
					$url = (empty($item->link) ? 'index.php?option=com_mtprice&view=prices' : $item->link);
					$this->setRedirect(JRoute::_($url.'&state='.$state , false));
      
       


     
    }

    /**
     * Method to save a user's profile data.
     *
     * @return	void
     * @since	1.6
     */
    public function save() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('PriceForm', 'MtpriceModel');

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');
		
		
		
		//---gads redirect list with us---//
			$user=JFactory::getUser();
			$db =  JFactory::getDbo();
			$query= "Select link_published,sub_id,invite,invite_code,invite_open From #__mt_links Where link_id='{$data['link_id']}'";
			$db->setQuery($query);
			$link_status = $db->loadObject();
			if($link_status->link_published !='1' || !$link_status->sub_id)
			{
				$app = JFactory::getApplication();
				$jcookie = $app->input->cookie;
				if(!$user->name)
				{
					$data['link_id']=$jcookie->get('link_id', null);
				}
				$app->redirect('index.php?option=com_chargify&view=registers&link_id='.$data['link_id']);
			}
		
		$sid=$data['sid'];
		$state=$data['au_state'];
		$ptype=$data['price_type'];
		$ktype=$data['keypoints_type'];
		
		
		
        // Validate the posted data.
        $form = $model->getForm();
        if (!$form) {
            JError::raiseError(500, $model->getError());
            return false;
        }

        // Validate the posted data.
        $data = $model->validate($form, $data);

        // Check for errors.
        if ($data === false) {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                if ($errors[$i] instanceof Exception) {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            $input = $app->input;
            $jform = $input->get('jform', array(), 'ARRAY');

            // Save the data in the session.
            $app->setUserState('com_mtprice.edit.price.data', $jform, array());

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_mtprice.edit.price.id');
            $this->setRedirect(JRoute::_('index.php?option=com_mtprice&view=priceform&layout=edit&id=' . $id, false));
            return false;
        }

        // Attempt to save the data.
        $return = $model->save($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_mtprice.edit.price.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_mtprice.edit.price.id');
            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_mtprice&view=priceform&layout=edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_mtprice.edit.price.id', null);

        // Redirect to the list screen.
		 
        $menu = JFactory::getApplication()->getMenu();
        $item = $menu->getActive();
		if(!empty($sid) and $sid!='0')
		{
			$this->setMessage(JText::_('Changes Successfully Saved!'));
			$url="index.php?option=com_mtprice&task=priceform.edit&ptype=$ptype&ktype=3&id=" . $sid;
		}
		else
		{
			$this->setMessage(JText::_('COM_MTPRICE_ITEM_SAVED_SUCCESSFULLY'));
			$url="index.php?option=com_mtprice&task=priceform.edit&ptype=$ptype&ktype=3&id=" . $return;
		}
		
        //$url = (empty($item->link) ? 'index.php?option=com_mtprice&view=priceform&layout=edit&id=' : $item->link);
        //$this->setRedirect(JRoute::_($url.'&state='.$state , false));
		// if(!empty($sid) and $sid!='0')
		// {
			// $url = 'index.php?option=com_mtprice&task=priceform.edit&ktype=3&ptype='.$ptype.'&id='.$sid;
			// $this->setRedirect(JRoute::_($url, false));
	
			$this->setRedirect(JRoute::_($url, false));

	
        // Flush the data from the session.
        $app->setUserState('com_mtprice.edit.price.data', null);
    }

    function cancel() {
        
        $app = JFactory::getApplication();

        // Get the current edit id.
        $editId = (int) $app->getUserState('com_mtprice.edit.price.id');

        // Get the model.
        $model = $this->getModel('PriceForm', 'MtpriceModel');

        // Check in the item
        if ($editId) {
            $model->checkin($editId);
        }
        
        $menu = JFactory::getApplication()->getMenu();
        $item = $menu->getActive();
        $url = (empty($item->link) ? 'index.php?option=com_mtprice&view=prices' : $item->link);
        $this->setRedirect(JRoute::_($url, false));
    }

    public function remove() {

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('PriceForm', 'MtpriceModel');

        // Get the user data.
        $data = array();
        $data['id'] = $app->input->getInt('id');

        // Check for errors.
        if (empty($data['id'])) {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                if ($errors[$i] instanceof Exception) {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState('com_mtprice.edit.price.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_mtprice.edit.price.id');
            $this->setRedirect(JRoute::_('index.php?option=com_mtprice&view=price&layout=edit&id=' . $id, false));
            return false;
        }

        // Attempt to save the data.
        $return = $model->delete($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_mtprice.edit.price.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_mtprice.edit.price.id');
            $this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_mtprice&view=price&layout=edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_mtprice.edit.price.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_MTPRICE_ITEM_DELETED_SUCCESSFULLY'));
        $menu = JFactory::getApplication()->getMenu();
        $item = $menu->getActive();
        $url = (empty($item->link) ? 'index.php?option=com_mtprice&view=prices' : $item->link);
        $this->setRedirect(JRoute::_($url, false));

        // Flush the data from the session.
        $app->setUserState('com_mtprice.edit.price.data', null);
    }

}
