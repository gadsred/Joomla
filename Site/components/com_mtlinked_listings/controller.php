<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Mtlinked_listings
 * @author     gadiel_Rojo <gadsred@gmail.com>
 * @copyright  Copyright (C) 2016. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
require_once(  JPATH_LIBRARIES . '/vendor/chargebee/chargebee-php/lib/ChargeBee.php');


/**
 * Class Mtlinked_listingsController
 *
 * @since  1.6
 */
class Mtlinked_listingsController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   mixed    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/mtlinked_listings.php';

		$view = JFactory::getApplication()->input->getCmd('view', 'listings');
		JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}

	public function getAddonId($membership_combination_string)
	{	
		$subscription_id = '';

		switch ($membership_combination_string) {
			case '11':
			    $subscription_id = 'monthly-additional-location-standard-listing';
				break;
			case '12':
				$subscription_id = 'yearly-additional-location-standard-listing';
				break;
			case '21':
				$subscription_id = 'monthly-additional-location-premium-listing';
				break;
			case '22':
				$subscription_id = 'yearly-additional-location-premium-listing';
				break;
		}
		return $subscription_id;
	}

	public function getLinkWithSubscription()
	{
		$user_id= JFactory::getUser()->id; 
		$db = JFactory::getDBO();
		$query = "Select link_id From #__users where id='$user_id'";
		$db->setQuery($query);
		$db->loadObject();
		$id = $db->loadObject();
		$link_id=$id->link_id;
		$db 	= JFactory::getDBO();
		$query 	= "Select link_id, sub_id, link_name from #__mt_links where link_id='$link_id'";
		$db->setQuery($query);
		$link = $db->loadObject();
		return $link;
	}

	public function linkListing()
	{
		// Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        // Initialise variables.
        $app = JFactory::getApplication();
        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

        $link = $this->getLinkWithSubscription();
        try {
				if($link->sub_id !='BANK INVOICE')
				{
					$result = ChargeBee_Subscription::retrieve($link->sub_id);
					
					$subscription = $result->subscription();
					$addon_id = $this->getAddonId($data['membership'].''.$data['billing_plan']);

					$total_add_on_quantity = 0;
					foreach ($subscription->addons as $key => $value) {
						if ($value->id == $addon_id) {
							$total_add_on_quantity = $value->quantity + 1;
						}
					}
					if ($total_add_on_quantity===0) {
						$total_add_on_quantity = 1;
					}
					$result = ChargeBee_Subscription::update($link->sub_id, array(
					"addons" => array(array(
						"id" => $addon_id,
						"quantity" => $total_add_on_quantity
					))));
				}
			
		  	$link_id = $link->link_id;
		  	if(empty($data['sub_link']))
			{
				$db = JFactory::getDBO();
				$query 	= "Select user_id,link_name,email,telephone,fax,logo,subscription,website,link_desc from #__mt_links where link_id='$link_id'";
				$db->setQuery($query);
				$main_link	= $db->loadObject();
				//insert new listing location
				$db =  JFactory::getDbo();
				$query = "Insert Into #__mt_links(user_id,link_name,email,telephone,fax,logo,link_published,link_approved,link_created,subscription,sub_id,website,link_desc) 
							Values('".$main_link->user_id."',
								   '".$main_link->link_name."',
								   '".$main_link->email."',
								   '".$main_link->telephone."',
								   '".$main_link->fax."',
								   '".$main_link->logo."',
								   '1',
								   '0',
								   '".date("Y-m-d")."',
								   '".$data['membership']."',
								   '".$link->sub_id."',
								   '".$main_link->website."',
								   '".$main_link->link_desc."')"; 
				$db->setQuery($query);
				$new_id=$db->execute();
				//get new linked listing
				$db 	= JFactory::getDBO();
				$query 	= "Select link_id from #__mt_links where user_id='$main_link->user_id' and link_approved='0'";
				$db->setQuery($query);
				$new_link	= $db->loadObject();
				//insertlinked table
				$db =  JFactory::getDbo();
				$query = "INSERT INTO #__mt_linked_listings
								(`id`, `asset_id`, `ordering`, `state`, `main_link`, `sub_link`, `subs_id`, `subs_type`, `user_id`, `link_created`, `link_updated`) 
							VALUES
								('','','','1','$link_id','".$new_link->link_id."','$link->sub_id','$addon_id',$main_link->user_id,'".date("Y-m-d")."','')";
				$db->setQuery($query);
				$db->execute();
				//update moset main table
				$db =  JFactory::getDbo();
				$query = "Update #__mt_links Set 
								link_approved='1'
							Where link_id=".$new_link->link_id;
				$db->setQuery($query);
				$db->execute();
				//enable editlisting
				$db =  JFactory::getDbo();
				$query = "INSERT INTO #__mt_cl (`cl_id`, `link_id`, `cat_id`, `main`) 
								VALUES ('','$new_link->link_id', '0', '1')";
				$db->setQuery($query);
				$db->execute();
				//copy required fields
				$db =  JFactory::getDbo();
				$query = "INSERT INTO #__mt_cfvalues
								(cf_id, link_id, value, attachment, counter) 
							Select cf_id, $new_link->link_id, value, attachment, counter 
								from #__mt_cfvalues where 
									link_id='$link_id' && cf_id='30' 
												|| 
									link_id='$link_id' && cf_id='31' 
												|| 
									link_id='$link_id' && cf_id='32' 
												|| 
									link_id='$link_id' && cf_id='34' 
												|| 
									link_id='$link_id' && cf_id='36' 
												|| 
									link_id='$link_id' && cf_id='37'
												||
									link_id='$link_id' && cf_id='38'
												|| 
									link_id='$link_id' && cf_id='40' 
												|| 
									link_id='$link_id' && cf_id='41' 
												|| 
									link_id='$link_id' 	&& cf_id='42' 
												|| 
									link_id='$link_id' && cf_id='43'";

				$db->setQuery($query);
				$db->execute();
				
				//send email notification for new addons
				$body = "<p>ETF New Added Location</p>";
				$body .= "<p>".$this->getPlanId($data['membership'].''.$data['billing_plan'])."</p>";
				$body .= "<p>Plz. follow this link to see the listing who added the new location www.propertyconveyancingdirectory.com.au".JRoute::_('index.php?option=com_mtree&task=viewlink&link_id='.$link_id)."</p>";

				$mail = JFactory::getMailer();
				$mail->isHTML( true );
				$mail->Encoding = 'base64';
				$mail->addRecipient('info@propertyconveyancingdirectory.com.au');
				$mail->setSubject("New ETF Addons");
				$mail->setBody( $body );
				$mail->AltBody =JMailHelper::cleanText( strip_tags( $body));
				$sento = $mail->Send();
				
				$app->redirect('index.php?option=com_mtlinked_listings&Itemid=298&lang=en&view=listings&old_id=1','Listing was successfully linked!');
			}
			else
			{
				$user_id= JFactory::getUser()->id; 
				//update moset main table
				$db =  JFactory::getDbo();
				$query = "Update #__mt_links Set 
								subscription='".$data['membership']."',
								sub_id='$link->sub_id',
								user_id='$user_id'
							Where link_id=".$data['sub_link'];
				$db->setQuery($query);
				$db->execute();
				//update linked listing table
				$db =  JFactory::getDbo();
				$query = "INSERT INTO #__mt_linked_listings
								(`id`, `asset_id`, `ordering`, `state`, `main_link`, `sub_link`, `subs_id`, `subs_type`, `user_id`, `link_created`, `link_updated`) 
							VALUES 
								('','','','1','$link_id','".$data['sub_link']."','$link->sub_id','$addon_id',$user_id,'".date("Y-m-d")."','')";
				$db->setQuery($query);
				$db->execute();
				$app->redirect('index.php?option=com_mtlinked_listings&Itemid=298&lang=en&view=listings&old_id=1'.$data['sub_link'],'Listing was successfully linked!');
				//copy required fields
				$db =  JFactory::getDbo();
				$query = "INSERT INTO #__mt_cfvalues (cf_id, link_id, value, attachment, counter) Select cf_id,".$data['sub_link'].", value, attachment, counter from #__mt_cfvalues where 
									link_id='$link_id' && cf_id='30' 
												|| 
									link_id='$link_id' && cf_id='31' 
												|| 
									link_id='$link_id' && cf_id='32' 
												|| 
									link_id='$link_id' && cf_id='34' 
												|| 
									link_id='$link_id' && cf_id='36' 
												|| 
									link_id='$link_id' && cf_id='37'
												||
									link_id='$link_id' && cf_id='38'
												|| 
									link_id='$link_id' && cf_id='40' 
												|| 
									link_id='$link_id' && cf_id='41' 
												|| 
									link_id='$link_id' 	&& cf_id='42' 
												|| 
									link_id='$link_id' && cf_id='43'";

				$db->setQuery($query);
				$db->execute();
				
				//send email notification for new addons
				$body = "<p>ETF Added Location from DB</p>";
				$body .= "<p>".$this->getPlanId($data['membership'].''.$data['billing_plan'])."</p>";
				$body .= "<p>Plz. follow this link to see the listing who added the new location, www.propertyconveyancingdirectory.com.au".JRoute::_('index.php?option=com_mtree&task=viewlink&link_id='.$link_id)."</p>";

				$mail = JFactory::getMailer();
				$mail->isHTML( true );
				$mail->Encoding = 'base64';
				$mail->addRecipient('info@propertyconveyancingdirectory.com.au');
				$mail->setSubject("New ETF Addons from DB");
				$mail->setBody( $body );
				$mail->AltBody =JMailHelper::cleanText( strip_tags( $body));
				$sento = $mail->Send();
			}
        } catch (Exception $e) {
        	$app->redirect('index.php?option=com_mtlinked_listings&Itemid=298&lang=en&view=listings','Invalid Subscription. Plz.. update your credit card.',"warning");
        }
	}
	
	public function getPlanId($membership_combination_string)
	{
		$subscription_id = '';

		switch ($membership_combination_string) {
			case '11':
			    $subscription_id = 'standard-monthly-subscription';
				break;
			case '12':
				$subscription_id = 'yearly-standard-subscription-(1-month-free)';
				break;
			case '21':
				$subscription_id = 'premium-monthly-subscription';
				break;
			case '22':
				$subscription_id = 'yearly-premium-subscription-(1-month-free)';
				break;
		}
		return $subscription_id;
	}
	//gads upgrade subs
	public function linked()
	{
		$this->linkListing();
		return;
		// Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');
		
		//---subcription and billing logic---//
			if($data['membership']=='1' && $data['billing_plan']=='1')
			{
				$pid='148861';
				$phandle='standard-monthly-subscription';
			}
				if($data['membership']=='1' && $data['billing_plan']=='2')
				{
					$pid='148862';
					$phandle='standard-yearly-subscription';
				}
				
			if($data['membership']=='2' && $data['billing_plan']=='1')
			{
				$pid='148866';
				$phandle='premium-monthly-subscription';
			}
			
				if($data['membership']=='2' && $data['billing_plan']=='2')
				{
					$pid='148865';
					$phandle='premium-yearly-subscription';
				}
		//---subcription and billing logic---//	
		
		//get subscription id
		$user_id= JFactory::getUser()->id; 
			$db = JFactory::getDBO();
			$query = "Select link_id From #__users where id='$user_id'";
			$db->setQuery($query);
			$db->loadObject();
			$id = $db->loadObject();
			$link_id=$id->link_id;
					
		$db 	= JFactory::getDBO();
		$query 	= "Select sub_id, link_name from #__mt_links where link_id='$link_id'";
		$db->setQuery($query);
		$sub_id	= $db->loadObject();
		
		//---Quantity allocation---// 				

			$url='https://property-conveyancers-directory.chargify.com/subscriptions/'.$sub_id->sub_id.'/components/'.$pid.'.json';
			
			$headers = array(
								"Content-Type:application/json",
								"Authorization: Basic ". base64_encode("wm8JLGxlrKaKgcZyXDd:x")
							);
							
			$ch = curl_init($url);                                                                      
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
			//curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                             
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,50);
			curl_setopt($ch,CURLOPT_TIMEOUT, 50);

			$curl_response = curl_exec ($ch); // execute
			
			//error handling
			$code=curl_getinfo($ch) . '<br/>';
			echo curl_errno($ch) . '<br/>';
			echo curl_error($ch) . '<br/>';
			curl_close ($ch);
			
			$res=json_decode($curl_response);
			
		    $qty=$res->component->allocated_quantity +1;
		//---End Quantity allocation---// 		
		
		//Create allocation
			if(empty($data['address']))
			{
				$data['address']=$sub_id->link_name.' - New Listing - '.date('Y-m-d');
			}
		 $fields ='{
					  "allocation":{
						"quantity":'.$qty.',
						"memo":"'.$data['address'].'"
					  }
					}';
		
			$url='https://property-conveyancers-directory.chargify.com/subscriptions/'.$sub_id->sub_id.'/components/'.$pid.'/allocations.json';
			
			$headers = array(
								"Content-Type:application/json",
								"Authorization: Basic ". base64_encode("wm8JLGxlrKaKgcZyXDd:x")
							);
							
			$ch = curl_init($url);                                                                      
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
			curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                             
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,50);
			curl_setopt($ch,CURLOPT_TIMEOUT, 50);

			$curl_response = curl_exec ($ch); // execute
			
			//error handling
			$code=curl_getinfo($ch,CURLINFO_HTTP_CODE) . '<br/>';
			echo curl_errno($ch) . '<br/>';
			echo curl_error($ch) . '<br/>';
			curl_close ($ch);
			$res2=json_decode($curl_response);
			
		
			
			//--------------Update DB for new linked Subcription------------------//
				if($code==201)
				{	
		
					if(empty($data['sub_link']))
					{
						$db 	= JFactory::getDBO();
						$query 	= "Select user_id,link_name,email,telephone,fax,logo,subscription,website,link_desc from #__mt_links where link_id='$link_id'";
						$db->setQuery($query);
						$main_link	= $db->loadObject();
						
						//insert new listing location
						$db =  JFactory::getDbo();
						$query = "Insert Into #__mt_links(user_id,link_name,email,telephone,fax,logo,link_published,link_approved,link_created,subscription,sub_id,website,link_desc) 
									Values('".$main_link->user_id."',
										   '".$main_link->link_name."',
										   '".$main_link->email."',
										   '".$main_link->telephone."',
										   '".$main_link->fax."',
										   '".$main_link->logo."',
										   '1',
										   '0',
										   '".date("Y-m-d")."',
										   '".$data['membership']."',
										   '".$sub_id->sub_id."',
										   '".$main_link->website."',
										   '".$main_link->link_desc."')"; 
						$db->setQuery($query);
						$new_id=$db->execute();
						
						//get new linked listing
						$db 	= JFactory::getDBO();
						$query 	= "Select link_id from #__mt_links where user_id='$main_link->user_id' and link_approved='0'";
						$db->setQuery($query);
						$new_link	= $db->loadObject();

						//insertlinked table
						$db =  JFactory::getDbo();
						$query = "INSERT INTO #__mt_linked_listings
										(`id`, `asset_id`, `ordering`, `state`, `main_link`, `sub_link`, `subs_id`, `subs_type`, `user_id`, `link_created`, `link_updated`) 
									VALUES 
										('','','','1','$link_id','".$new_link->link_id."','$sub_id->sub_id','$pid',$main_link->user_id,'".date("Y-m-d")."','')";
						$db->setQuery($query);
						$db->execute();
						
						//update moset main table
						$db =  JFactory::getDbo();
						$query = "Update #__mt_links Set 
										link_approved='1'
									Where link_id=".$new_link->link_id;
						$db->setQuery($query);
						$db->execute();
						
						//enable editlisting
						$db =  JFactory::getDbo();
						$query = "INSERT INTO #__mt_cl (`cl_id`, `link_id`, `cat_id`, `main`) 
										VALUES ('','$new_link->link_id', '0', '1')";
						$db->setQuery($query);
						$db->execute();
						
						//copy required fields
						$db =  JFactory::getDbo();
						$query = "INSERT INTO #__mt_cfvalues
										(cf_id, link_id, value, attachment, counter) 
									Select cf_id, $new_link->link_id, value, attachment, counter 
										from #__mt_cfvalues where 
											link_id='$link_id' && cf_id='30' 
														|| 
											link_id='$link_id' && cf_id='31' 
														|| 
											link_id='$link_id' && cf_id='32' 
														|| 
											link_id='$link_id' && cf_id='34' 
														|| 
											link_id='$link_id' && cf_id='36' 
														|| 
											link_id='$link_id' && cf_id='37'
														||
											link_id='$link_id' && cf_id='38'
														|| 
											link_id='$link_id' && cf_id='40' 
														|| 
											link_id='$link_id' && cf_id='41' 
														|| 
											link_id='$link_id' 	&& cf_id='42' 
														|| 
											link_id='$link_id' && cf_id='43'";

						$db->setQuery($query);
						$db->execute();
						
						
						$app->redirect('index.php?option=com_mtlinked_listings&Itemid=298&lang=en&view=listings&old_id=1','Listing was successfully linked!');
					}
					else
					{
						$user_id= JFactory::getUser()->id; 
						//update moset main table
						$db =  JFactory::getDbo();
						$query = "Update #__mt_links Set 
										subscription='".$data['membership']."',
										sub_id='$sub_id->sub_id',
										user_id='$user_id'
									Where link_id=".$data['sub_link'];
						$db->setQuery($query);
						$db->execute();
						
						//update linked listing table
						
						$db =  JFactory::getDbo();
						$query = "INSERT INTO #__mt_linked_listings
										(`id`, `asset_id`, `ordering`, `state`, `main_link`, `sub_link`, `subs_id`, `subs_type`, `user_id`, `link_created`, `link_updated`) 
									VALUES 
										('','','','1','$link_id','".$data['sub_link']."','$sub_id->sub_id','$pid',$user_id,'".date("Y-m-d")."','')";
						$db->setQuery($query);
						$db->execute();
						$app->redirect('index.php?option=com_mtlinked_listings&Itemid=298&lang=en&view=listings&old_id=1'.$data['sub_link'],'Listing was successfully linked!');
						
						//copy required fields
						$db =  JFactory::getDbo();
						$query = "INSERT INTO #__mt_cfvalues (cf_id, link_id, value, attachment, counter) Select cf_id,".$data['sub_link'].", value, attachment, counter from #__mt_cfvalues where 
											link_id='$link_id' && cf_id='30' 
														|| 
											link_id='$link_id' && cf_id='31' 
														|| 
											link_id='$link_id' && cf_id='32' 
														|| 
											link_id='$link_id' && cf_id='34' 
														|| 
											link_id='$link_id' && cf_id='36' 
														|| 
											link_id='$link_id' && cf_id='37'
														||
											link_id='$link_id' && cf_id='38'
														|| 
											link_id='$link_id' && cf_id='40' 
														|| 
											link_id='$link_id' && cf_id='41' 
														|| 
											link_id='$link_id' 	&& cf_id='42' 
														|| 
											link_id='$link_id' && cf_id='43'";

						$db->setQuery($query);
						$db->execute();
					}
				}
				else
				{
					$app->redirect('index.php?option=com_mtlinked_listings&Itemid=298&lang=en&view=listings','Invalid Subscription. Plz.. update your credit card.',"warning");
				}
		
	}

	public function newAddon($chargify_id,$addon_id)
	{
		$result = ChargeBee_Subscription::update($chargify_id, array(
	  	"addons" => array(array(
	    	"id" => $addon_id,
	    	"quantity" => 1
	  	))));
	}

	public function removeAddon($chargify_subs_id,$addon = [])
	{
		if (!$addon) {
			$addon = array();
		}else{
			$addon = array(array(
				'id' =>$addon->id,
				'quantity' =>$addon->quantity
			));
		}
		$result = ChargeBee_Subscription::update($chargify_subs_id, array(
		  	"replaceAddonList" => true,
		  	"addons" => $addon));
	}

	public function getAddon($addons,$addon_id)
	{
		foreach ($addons as $key => $value) {
			if ($value->id === $addon_id) {
				return $value;
			}
		}
		return false;
	}

	public function checkRemainingAddon($addons,$addon_id)
	{
		foreach ($addons as $key => $value) {
			if ($value->id === $addon_id) {
				if ($value->quantity == 1) {
					return false;
				}
				return true;
			}
		}
	}

	public function getRemainingSubscripionAddons($addons, $except_addon_id)
	{
		$addons = array();
		foreach ($addons as $key => $value) {
			if ($value->id !== $except_addon_id) {
				$addons[] = $value;
			}
		}
	}

	public function addOn($subscription,$addon_id,$subscription_payment_type,$link_id)
	{
		$db = JFactory::getDBO();
		$query = "Select subs_type From #__mt_linked_listings Where sub_link='".$link_id."'";
		$db->setQuery($query);
		$old_addon_id = $db->loadObject()->subs_type;

		$db = JFactory::getDBO();
		$query = "Select subscription From #__mt_links Where link_id='".$link_id."'";
		$db->setQuery($query);
		$subscription_payment = $db->loadObject()->subscription;
		// $result = ChargeBee_Subscription::update($subscription->id, array(
		//   	"replaceAddonList" => true,
		//   	"addons" => array(
		//   		"id" => 'yearly-additional-location-premium-listing',
		// 	    "quantity" => 1
		//   	)
	 //  	));
	 //  	exit();
	 //  	return false;
		// echo $addon_id.'-'.$old_addon_id;exit();
		if (($addon_id == $old_addon_id) && ($subscription_payment == $subscription_payment_type)) {
			return false;
		}
		try {
			if ($this->checkRemainingAddon($subscription->addons,$old_addon_id)) {
				$old_addon = $this->getAddon($subscription->addons,$old_addon_id);
				$result = ChargeBee_Subscription::update($subscription->id, array(
			  	"addons" => array(array(
			    	"id" => $old_addon_id,
			    	"quantity" => $old_addon->quantity - 1
			  	))));
			}else{
				$remaining_subscription_addons = $this->getRemainingSubscripionAddons($subscription->addons,$old_addon_id);
				$result = ChargeBee_Subscription::update($subscription->id, array(
				  	"replaceAddonList" => true,
				  	"addons" => $remaining_subscription_addons
			  	));
			}
			$addon = $this->getAddon($subscription->addons,$addon_id);
			$result = ChargeBee_Subscription::update($subscription->id, array(
		  	"addons" => array(array(
		    	"id" => $addon_id,
		    	"quantity" => ($addon)?($addon->quantity+1):1
		  	))));
			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function upgradeAdd_on()
	{
		// Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        // Initialise variables.
        $app = JFactory::getApplication();
        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');
        // Retrieve user subscription
	
		try {
			$result = ChargeBee_Subscription::retrieve($data['sub_id']);
		}					
		catch(Exception $e) {
		 // echo 'Message: ' .$e->getMessage();
		}
		if ($result) {
			// Get subscription from result
			$subscription = $result->subscription();
			// Get selected addon
			$addon_id = $this->getAddonId($data['membership'].''.$data['billing_plan']);
			// Update user subscription add on.
			$update_addon = $this->addOn($subscription,$addon_id,$data['billing_plan'],$data['link_id']);
			// Check if addon update is true
			if ($update_addon) {
				$db =  JFactory::getDbo();
				$query = "Update #__mt_links Set subscription = '2' Where link_id=".$data['link_id'];
				$db->setQuery($query);
				$db->execute();
				$db =  JFactory::getDbo();
				$query = "Update #__mt_linked_listings Set subs_type='".$addon_id."' Where sub_link=".$data['link_id'];
				$db->setQuery($query);
				$db->execute();
				
				$app->redirect('index.php?option=com_mtree&task=editlisting&Itemid=218','Your listing has been successfully upgraded to a Premium listing');
				return;
			}
		}
		$app->redirect('index.php?option=com_mtree&task=editlisting&Itemid=218','Can\'t update subscription');
	}
	
	public function upgradelinked()
	{
		$this->upgradeAdd_on();
		return;
		//---functions for allocations---//
		function create_allocation($qty,$memo,$sub_id,$pid)
		{
			$fields ='{
						  "allocation":{
							"quantity":'.$qty.',
							"memo":"'.$memo.'"
						  }
						}';
			
				$url='https://property-conveyancers-directory.chargify.com/subscriptions/'.$sub_id.'/components/'.$pid.'/allocations.json';
				
				$headers = array(
									"Content-Type:application/json",
									"Authorization: Basic ". base64_encode("wm8JLGxlrKaKgcZyXDd:x")
								);
								
				$ch = curl_init($url);                                                                      
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); 
				curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                             
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,50);
				curl_setopt($ch,CURLOPT_TIMEOUT, 50);

				$curl_response = curl_exec ($ch); // execute
				
				//error handling
				$code=curl_getinfo($ch,CURLINFO_HTTP_CODE) . '<br/>';
				echo curl_errno($ch) . '<br/>';
				echo curl_error($ch) . '<br/>';
				curl_close ($ch);
				$res2=json_decode($curl_response);
				return $code;
		}
		
		function get_allocation_qty($sub_id,$pid)
		{
			$url='https://property-conveyancers-directory.chargify.com/subscriptions/'.$sub_id.'/components/'.$pid.'.json';
				
				$headers = array(
									"Content-Type:application/json",
									"Authorization: Basic ". base64_encode("wm8JLGxlrKaKgcZyXDd:x")
								);
								
				$ch = curl_init($url);                                                                      
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET"); 
				//curl_setopt($ch, CURLOPT_POSTFIELDS,$fields);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                             
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,50);
				curl_setopt($ch,CURLOPT_TIMEOUT, 50);

				$curl_response = curl_exec ($ch); // execute
				
				//error handling
				$code=curl_getinfo($ch) . '<br/>';
				echo curl_errno($ch) . '<br/>';
				echo curl_error($ch) . '<br/>';
				curl_close ($ch);
				
				$res=json_decode($curl_response);
				
				$qty=$res->component->allocated_quantity;
				return $qty;
		}
		//---End functions for allocations---//
		// Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');
		
		//---subcription and billing logic---//
			
			if($data['billing_plan']=='1')
			{
				$pid='148866';
				$phandle='premium-monthly-subscription';
			}
			
				if($data['billing_plan']=='2')
				{
					$pid='148865';
					$phandle='premium-yearly-subscription';
				}
		//---end subcription and billing logic---//	
		
		//get old product id
		$db = JFactory::getDBO();
		$query = "Select subs_type From #__mt_linked_listings Where sub_link='".$data['link_id']."'";
		$db->setQuery($query);
		$old_pid = $db->loadObject()->subs_type;
		
			//--Update old allocation--//
			$qty = get_allocation_qty($data['sub_id'],$old_pid);
			$qty = $qty-1;
			$memo="Setting quantity to $qty at customer request";
			$code = create_allocation($qty,$memo,$data['sub_id'],$old_pid);				
			
			if($code==201)
			{
				//--Create new allocation--//
		
					//get allocation qty			
					$qty = get_allocation_qty($data['sub_id'],$pid);
					$qty=$qty+1;
			
					//get address
					$db = JFactory::getDBO();
					$query = "Select address,city,state,postcode From #__mt_links Where link_id='".$data['link_id']."'";
					$db->setQuery($query);
					$add = $db->loadObject();
					
					$address=$add->address.', '.$add->city.', '.$add->state.', '.$add->postcode;
					
					$memo="Upgrade of Listing: ".$address;
					
					$code=create_allocation($qty,$memo,$data['sub_id'],$pid);
					
					if($code==201)
					{
						//update moset main table
						$db =  JFactory::getDbo();
						$query = "Update #__mt_links Set subscription='2' Where link_id=".$data['link_id'];
						$db->setQuery($query);
						$db->execute();
						
						//update mt_linked table
						$db =  JFactory::getDbo();
						$query = "Update #__mt_linked_listings Set subs_type='$pid' Where sub_link=".$data['link_id'];
						$db->setQuery($query);
						$db->execute();
						
						$app->redirect('index.php?option=com_mtree&task=editlisting&Itemid=218','Your listing has been successfully upgraded to a Premium listing');
					}
					else
					{
						exit();
					}
				//--End create new allocation--//	
			}
			else
			{
				exit();
			}
			
		
			
	}
	
	public function changelisting()
	{
		$link_id = JRequest::getVar('id');
		$user_id= JFactory::getUser()->id; 
		$db =  JFactory::getDbo();
		$query = "Update #__users Set 
						link_id='$link_id'
					Where id='$user_id'";
		$db->setQuery($query);
		$db->execute();
		$app = JFactory::getApplication();
		$app->redirect('index.php?option=com_mtree&task=editlisting&Itemid=218&link_id='.$link_id);
		exit();
	}
	
	public function copydata()
	{
		
		$link_id = JRequest::getVar('id');
		
		$user_id=JFactory::getUser()->id;
		$db =  JFactory::getDbo();
		$query = "Select link_id From #__users Where id ='$user_id'";
		$db->setQuery($query);
		$main_link = $db->loadObject()->link_id;
		
		$app = JFactory::getApplication(); 
		
		//---copy pricing---//
		$db =  JFactory::getDbo();
		$query = "Select * From #__mt_price Where link_id ='$main_link'";
		$db->setQuery($query);
		$pricing = $db->loadObjectList();
		
		if($pricing)
		{
			 foreach($pricing as $price)
			 {
				$db =  JFactory::getDbo();
				$query = "Insert into  #__mt_price 
							(`id`, `ordering`, `state`, `user_id`, `link_id`, `au_state`, `description`, `price_type`, `keypoints_type`, `price`, `included_charges`, `extra_charges`)
							VALUES ('','$price->ordering',
									  '$price->state',
									  '$price->user_id',
									  '$link_id',
									  '$price->au_state',
									  '$price->description',
									  '$price->price_type',
									  '$price->keypoints_type',
									  '$price->price',
									  '$price->included_charges',
									  '$price->extra_charges')";
				$db->setQuery($query);
				$db->execute();
				
			 }
		}
		 
		 //---copy about us and keypoints---//
			$db =  JFactory::getDbo();
			$query = "Select aboutus,keypoints From #__mt_links Where link_id ='$main_link'";
			$db->setQuery($query);
			$aboutus = $db->loadObject();
			
			if($aboutus)
			{
				$db =  JFactory::getDbo();
				$query = 'Update #__mt_links Set 
								aboutus="'.str_replace('"',"'",$aboutus->aboutus).'",
								keypoints="'.str_replace('"',"'",$aboutus->keypoints).'"
							Where link_id="'.$link_id.'"';
				$db->setQuery($query);
				$db->execute();
			}
		
		//---copy FAQ---//
		$db =  JFactory::getDbo();
		$query = "Select question, answer From #__mt_faq Where link_id ='$main_link'";
		$db->setQuery($query);
		$faqs = $db->loadObjectList();
	
		if($faqs)
		{
			 foreach($faqs as $faq)
			 {
				$db =  JFactory::getDbo();
				$query = 'Insert into  #__mt_faq
							(`id`, `ordering`, `link_id`, `question`, `answer`)
							VALUES ("","'.$faq->ordering.'",
									  "'.$link_id.'",
									  "'.str_replace('"',"'",$faq->question).'",
									  "'.str_replace('"',"'",$faq->answer).'")';
				$db->setQuery($query);
				$db->execute();
				
			 }
		}
		
		
		
		$app->redirect('index.php?option=com_mtlinked_listings&task=changelisting&id='.$link_id);
		
	}
}
