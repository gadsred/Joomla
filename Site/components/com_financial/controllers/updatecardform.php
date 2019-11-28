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

require_once JPATH_COMPONENT . '/controller.php';

require_once( JPATH_LIBRARIES . '/vendor/chargebee/chargebee-php/lib/ChargeBee.php');

/**
 * Updatecard controller class.
 */
class FinancialControllerUpdatecardForm extends FinancialController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_financial.edit.updatecard.id');
        $editId = $app->input->getInt('id', null, 'array');

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_financial.edit.updatecard.id', $editId);

        // Get the model.
        $model = $this->getModel('UpdatecardForm', 'FinancialModel');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_financial&view=updatecardform&layout=edit', false));
    }

    public function updateChargebeeCard($sub_id,$data)
    {
        try {
            $subscription = ChargeBee_Subscription::retrieve($sub_id);
            $card_data = array(
                "gateway" => "stripe",
                "firstName" => $data['firstname'], 
                "lastName" => $data['lastname'], 
                "number" => $data['cardno'],
                "expiryMonth" => $data['expiremonth'], 
                "expiryYear" => $data['expireyear'],
                "cvv" => $data['cvv']
            );
            $result = ChargeBee_Card::updateCardForCustomer($subscription->customer()->id, $card_data);
            return true;
        } catch (Exception $e) {
            $this->setRedirect("index.php?option=com_financial&view=updatecardform&Itemid=228", 'Can\'t update card'.$e, '');
        }
    }

	public function updateCreditCard()
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('UpdatecardForm', 'FinancialModel');
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
        // Validate the posted data.
        $form = $model->getForm();
        if (!$form) {
            JError::raiseError(500, $model->getError());
            return false;
        }
        //get subscription 
        $user=JFactory::getUser()->id;
        $db =  JFactory::getDbo();
        $query = "Select * From #__mt_links Where user_id=".$user;
        $db->setQuery($query);
        $row = $db->loadObject();
        $sub_id=$row->sub_id;
        if ($this->updateChargebeeCard($sub_id,$data)) {
            $this->setRedirect("index.php?option=com_financial&view=updatecardform&Itemid=228", 'Credit Card updated successfully', '');
        }
    }
	
	//gads udpate card
	
	public function update() {

        $this->updateCreditCard();
        return;
		// Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('UpdatecardForm', 'FinancialModel');

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
		
        // Validate the posted data.
        $form = $model->getForm();
        if (!$form) {
            JError::raiseError(500, $model->getError());
            return false;
        }
		
		
		//get subscription 
		$user=JFactory::getUser()->id;
		$db =  JFactory::getDbo();
		$query = "Select * From #__mt_links Where user_id=".$user;
		$db->setQuery($query);
		$row = $db->loadObject();
		$sub_id=$row->sub_id;
		
		
		//update charggify
		$url = 'https://api.chargify.com/api/v2/subscriptions/'.$sub_id.'/card_update';
		$time = time();
		$redirect= urlencode('http://www.propertyconveyancingdirectory.com.au/index.php?option=com_financial&view=updatecardform&Itemid=228');
		$cdata= "subscription_id=".$sub_id."&amp;redirect_uri=".$redirect;
		$nonce="1234";
		$fields = array(
						'secure' => array(
							'api_id' => "1f5915a0-ff63-0132-1e97-0aa88d71309c",
							'timestamp'=>$time,
							'nonce'=> $nonce,
							'data' => $cdata,
							'signature' => hash_hmac('sha1', '1f5915a0-ff63-0132-1e97-0aa88d71309c'.$time.$nonce.$cdata, "ckicgjPYznq1Bduk2C")
						),
				'payment_profile' => array(
						'first_name' => $data['firstname'],
						'last_name' => $data['lastname'],
						'expiration_month '=> $data['expiremonth'],
						'expiration_year' => $data['expireyear'],
						'card_number'=> $data['cardno'],
						'cvv'=> $data['cvv']
				)
		);
			
		$qHTTP = http_build_query($fields);
		//open connection
		$ch = curl_init();
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_POST,true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $qHTTP);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CAINFO,getcwd()."/propertyconveyancingdirectory_com_au.crt");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER ,1);
		//execute post
		$result = curl_exec($ch);
		//close connection
		curl_close($ch);
		//var_dump($result);exit();
		
		//print_r($result);die();exit;
		//process Responce and extract the required params like call ID etc.
		$response=$result;
		
		$res = $response;
		$r = explode("?",$res);
		
		$items = explode("&",$r[1]);
		
		
		$result = new stdClass();

		foreach($items as $item){
			$it = explode("=",$item);
			$result->$it[0] = $it[1];
		}
		$result->status_code = substr($result->status_code,0,3);
		//print_r($result->call_id);exit;
		if($result->status_code==200){
			$msg = 'Credit Card updated successfully';
		}else{
			//return "There was error in chargify data insertion.";
			switch($result->result_code) {
				case '4001':
					$msg = 'Authentiction failed';
					break;
				
				case '4011':
					$msg = 'Authentication failed due to missing nonce value';
					break;
				
				case '4040':
					$msg = 'The requested object (e.g. Subscription) could not be found';
					break;
					
				case '4220':
					$msg = 'One or more validation errors on inputted card information';
					break;
					
				case '4221':
					$msg = 'Duplicate submission';
					break;
					
				case '4300':
					$msg = 'Card declined';
					break;
				
				case '5000':
					$msg = 'An error has occured';
					break;
					
				case '5001':
					$msg = 'The requested resource does not exist';
					break;
					
				default:
					$msg = 'An error has occured';
					break;
			}
		}
		//$msg = $msg . $result->status_code;
		$this->setRedirect("index.php?option=com_financial&view=updatecardform&Itemid=228", $msg, '');
	
		
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
        $model = $this->getModel('UpdatecardForm', 'FinancialModel');

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

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
            $app->setUserState('com_financial.edit.updatecard.data', $jform, array());

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_financial.edit.updatecard.id');
            $this->setRedirect(JRoute::_('index.php?option=com_financial&view=updatecardform&layout=edit&id=' . $id, false));
            return false;
        }

        // Attempt to save the data.
        $return = $model->save($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_financial.edit.updatecard.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_financial.edit.updatecard.id');
            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_financial&view=updatecardform&layout=edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_financial.edit.updatecard.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_FINANCIAL_ITEM_SAVED_SUCCESSFULLY'));
        $menu = JFactory::getApplication()->getMenu();
        $item = $menu->getActive();
        $url = (empty($item->link) ? 'index.php?option=com_financial&view=updatecards' : $item->link);
        $this->setRedirect(JRoute::_($url, false));

        // Flush the data from the session.
        $app->setUserState('com_financial.edit.updatecard.data', null);
    }

    function cancel() {
        
        $app = JFactory::getApplication();

        // Get the current edit id.
        $editId = (int) $app->getUserState('com_financial.edit.updatecard.id');

        // Get the model.
        $model = $this->getModel('UpdatecardForm', 'FinancialModel');

        // Check in the item
        if ($editId) {
            $model->checkin($editId);
        }
        
        $menu = JFactory::getApplication()->getMenu();
        $item = $menu->getActive();
        $url = (empty($item->link) ? 'index.php?option=com_financial&view=updatecards' : $item->link);
        $this->setRedirect(JRoute::_($url, false));
    }

    public function remove() {

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('UpdatecardForm', 'FinancialModel');

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
            $app->setUserState('com_financial.edit.updatecard.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_financial.edit.updatecard.id');
            $this->setRedirect(JRoute::_('index.php?option=com_financial&view=updatecard&layout=edit&id=' . $id, false));
            return false;
        }

        // Attempt to save the data.
        $return = $model->delete($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_financial.edit.updatecard.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_financial.edit.updatecard.id');
            $this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_financial&view=updatecard&layout=edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_financial.edit.updatecard.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_FINANCIAL_ITEM_DELETED_SUCCESSFULLY'));
        $menu = JFactory::getApplication()->getMenu();
        $item = $menu->getActive();
        $url = (empty($item->link) ? 'index.php?option=com_financial&view=updatecards' : $item->link);
        $this->setRedirect(JRoute::_($url, false));

        // Flush the data from the session.
        $app->setUserState('com_financial.edit.updatecard.data', null);
    }

}
