<?php

error_reporting(0);
defined('_JEXEC') or die('Restricted access');
require_once (JPATH_ADMINISTRATOR.'/components/com_j2store/library/plugins/payment.php');
JLoader::register('MainFunction', JPATH_PLUGINS . '/j2store/payment_vantiv/MainFunction.php');
	
class plgJ2StorePayment_vantiv extends J2StorePaymentPlugin
{
	/**
	 * @var $_element  string  Should always correspond with the plugin's filename,
	 *                         forcing it to be unique
	 */
	var $_element    = 'payment_vantiv';

	/**
	 * Constructor
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function __construct(& $subject, $config) {
		parent::__construct($subject, $config);
		$this->loadLanguage( '', JPATH_ADMINISTRATOR );	
	}
	
	/**
	 * Prepares variables for the payment form. 
	 * Displayed when customer selects the method in Shipping and Payment step of Checkout
	 *
	 * @return unknown_type
	 */
	 /*
	function _renderForm($data)
	{
	
	} */

	/**
	 * Method to display a Place order button either to redirect the customer or process the credit card information.
	 * @param $data     array       form post data
	 * @return string   HTML to display
	 */
	
	function _prePayment( $data )
	{
	    // get component params
		$params = J2Store::config();
		$currency = J2Store::currency();
		
		// prepare the payment form
		$vars = new JObject();
		$vars->order_id = $data['order_id'];
		$o_id = $data['order_id'];
		$vars->orderpayment_id = $data['orderpayment_id'];

		F0FTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_j2store/tables');
		$order = F0FTable::getInstance('Order', 'J2StoreTable')->getClone();
		$order->load(array('order_id'=>$data['order_id']));
		//echo '<pre>'; 
		//print_r($order); die();		
		$currency_values= $this->getCurrency($order);

		$vars->currency_code =$currency_values['currency_code'];
		
		//$total_wo_tax = $order->order_total;
		
	/*	if($_COOKIE['service-value']=="Delivery"){
		    $ordertotal= (floatval($order->order_total) + floatval(4.00));
		}else{
		      $ordertotal= floatval($order->order_total) + round(floatval($order->order_total) * floatval(0.1175),2); //$order->order_total;
		}
	*/	  
		
		//$total_with_tax = $currency->format($ordertotal, $currency_values['currency_code'], $currency_values['currency_value'], false);

		
		if ($_COOKIE['tips'] != '')
    	{
    	    $tips = $_COOKIE['tips'];
    	    //$vars->amount = number_format((($tips * $total_wo_tax) + $total_wo_tax),2) ;
    	    $vars->amount= floatval($order->order_total) + round(floatval($order->order_total) * floatval(0.1175),2) + ($tips * $order->order_total) ;
    	    
    	}
    	elseif($_COOKIE['tipsTextInput'] != '')
    	{
    	    $tips = $_COOKIE['tipsTextInput'];
    	   // $vars->amount = number_format(( $tips + $total_wo_tax),2) ;
    	    $vars->amount= floatval($order->order_total) + round(floatval($order->order_total) * floatval(0.1175),2) + number_format($tips,2) ;
    	}
    	else
    	{
    	    $tips = 0;
    	    //$vars->amount = $tips + $total_wo_tax;
    	    $vars->amount= floatval($order->order_total) + round(floatval($order->order_total) * floatval(0.1175),2)  ;
    	}
		
		$vars->amount = $currency->format($vars->amount, $currency_values['currency_code'], $currency_values['currency_value'], false);
		
		$rootURL = rtrim(JURI::base(),'/');
		$subpathURL = JURI::base(true);
		if(!empty($subpathURL) && ($subpathURL != '/')) {
			$rootURL = substr($rootURL, 0, -1 * strlen($subpathURL));
		}
		
		$return_url = $rootURL.JRoute::_("index.php?option=com_j2store&view=checkout&task=confirmPayment&orderpayment_type=".$this->_element."&paction=display");
		$cancel_url = $rootURL.JRoute::_("index.php?option=com_j2store&view=checkout&task=confirmPayment&orderpayment_type=".$this->_element."&paction=cancel");
		$callback_url = JURI::root()."index.php?option=com_j2store&view=checkout&task=confirmPayment&orderpayment_type=".$this->_element."&paction=callback&tmpl=component";
		$orderinfo = $order->getOrderInformation();
    	$vars->invoice = $order->getInvoiceNumber();  
    	
    	
        $MerchantSettings = mainfunction::GetSettingsInDb();
        
       // echo '<pre>'; print_r($MerchantSettings); die();
       
       	//echo $_COOKIE['totalWithTips'] ; die();
       	
       //	echo '<pre>'; print_r($order); die();
    	
    
       
    
       $initPaymentRequest = array (
			"AVSAddress" => "7122 Wornall Kansas City",
    		"AVSAddressField" => "7122 Wornall Kansas City",
    		"AVSFields" => "Off",
    		"AVSFieldsField" => "Off",
    		"AVSZip" => "64114",
    		"AVSZipField" => "64114",
    		"BackgroundColor" => "#009acd",
    		"BackgroundColorField" => "#009acd",
    		"ButtonBackgroundColor" => "#009acd",
    		"ButtonBackgroundColorField" => "#009acd",
    		"ButtonTextColor" => "White",
    		"ButtonTextColorField" => "White",
    		"CVV" => "On",
    		"CVVField" => "On",
    		"CancelButton" => null,
    		"CancelButtonDefaultImageUrl" => null,
    		"CancelButtonDefaultImageUrlField" => null,
    		"CancelButtonField" => null,
    		"CancelButtonHoverImageUrl" => null,
    		"CancelButtonHoverImageUrlField" => null,
    		"CancelButtonText" => "Cancel",
    		"CancelButtonTextField" => "Cancel",
    		"CardEntryMethod" => null,
    		"CardEntryMethodField" => null,
    		"CardHolderName" => "",
    		"CardHolderNameField" => "",
    		"CustomerCode" => "",
    		"CustomerCodeField" => "",
    		"DefaultSwipe" => null,
    		"DefaultSwipeField" => null,
    		"Diners" => "On",
    		"DinersField" => "On",
    		"DisplayStyle" => "Custom",
    		"DisplayStyleField" => "Custom",
    		"ExtensionData" => null,
    		"FontColor" => "Black",
    		"FontColorField" => "Black",
    		"FontFamily"=> "Arial",
    		"FontFamilyField" => "Arial",
    		"FontSize"=> "Small",
    		"FontSizeField" => "Small",
    		"ForceManualTablet" => null,
    		"ForceManualTabletField" => null,
    		"Frequency" => "OneTime",
    		"FrequencyField" => "OneTime",
    		"Invoice" => $vars->invoice,
    		"InvoiceField" => $vars->invoice,
    		"JCB"=> "On",
    		"JCBField" => "On",
    		"Keypad" => null,
    		"KeypadField" => null,
    		"LaneID" => null,
    		"LaneIDField" => null,
    		"Memo" => "Districtpourhousekc",
    		"MemoField" => "Districtpourhousekc",
    		"MerchantID" =>$MerchantSettings[0]->MerchantID,
    		"MerchantIDField" => $MerchantSettings[0]->MerchantID,
    		"OperatorID" => "",
    		"OperatorIDField" => "",
    		"OrderTotal" => null,
    		"OrderTotalField" => null,
    		"PageTimeoutDuration" => "0",
    		"PageTimeoutDurationField" => "0",
    		"PageTimeoutIndicator" => "Off",
    		"PageTimeoutIndicatorField" => "Off",
    		"PageTitle" => "Districtpourhousekc",
    		"PageTitleField" => "Districtpourhousekc",
    		"PartialAuth" => null,
    		"PartialAuthField" => null,
    		"Password" => $MerchantSettings[0]->Password,
    		"PasswordField" => $MerchantSettings[0]->Password,
    		"ProcessCompleteUrl"	=>	$return_url,
    		"ProcessCompleteUrlField" => $return_url,
    		"ReturnUrl"	=> $return_url,
    		"ReturnUrlField" => $return_url,
    		"SecurityLogo" => "On",
    		"SecurityLogoField" => "On",
    		"SubmitButtonDefaultImageUrl" => null,
    		"SubmitButtonDefaultImageUrlField" => null,
    		"SubmitButtonHoverImageUrl" => null,
    		"SubmitButtonHoverImageUrlField" => null,
    		"SubmitButtonText" => "Submit",
    		"SubmitButtonTextField" => "Submit",
    		"TaxAmount" => 0,
    		"TaxAmountField" => 0,
    		"TerminalName" => null,
    		"TerminalNameField" => null,
    		"TotalAmount" => $vars->amount,
    		"TotalAmountBackgroundColor" => "#B81",
    		"TotalAmountBackgroundColorField" => "#ADBB81",
    		"TotalAmountField" => $vars->amount,
    		"TranType"=> "Sale",
    		"TranTypeField" => "Sale",
    		"VoiceAuthCode" => null,
    		"VoiceAuthCodeField" => null,
    		"extensionDataField" => null,
    	); 
    	
    
    	$initPaymentResponse = mainfunction::sendInitializePayment($initPaymentRequest);
    	
    	//echo '<pre>'; print_r($initPaymentResponse); die();
    	if($initPaymentResponse->InitializePaymentResult->ResponseCode == 0)
		{	
			$vars->pid = $initPaymentResponse->InitializePaymentResult->PaymentID; 
			$_SESSION['pid'] = $vars->pid;
            if($vars->pid == '')
			{
				echo 'Error connecting to server';die();
			}
			else
			{
				$html = $this->_getLayout('prepayment', $vars);
			}
			
			return $html;
		}
		else
		{
			$error['message'] = 'Problem Acquiring Payment ID';
			return $error;
		}
	
	} 
	

	/**
	 * Processes the payment form
	 * and returns HTML to be displayed to the user
	 * generally with a success/failed message
	 *
	 * @param $data     array       form post data
	 * @return string   HTML to display
	 */
	function _postPayment( $data )
	{
		//echo  '<pre>'; print_r($data); die();
		// Process the payment
		$app = JFactory::getApplication();
		$paction = $app->input->getString('paction');

		$vars = new JObject();
		
		$order_id = $data['order_id'];
		$vars->orderpayment_id = $data['orderpayment_id'];
		
		//print_r($vars); die();

		switch ($paction)
		{
			case "display":
				$pid = $_SESSION['pid'];
			    $MerchantSettings = mainfunction::GetSettingsInDb();
				
				$verifyPaymentRequest = array(
				"ExtensionData" => null,
				"MerchantID"	=>	$MerchantSettings[0]->MerchantID,
				"MerchantField" => $MerchantSettings[0]->MerchantID,
				"Password"	=>	$MerchantSettings[0]->Password,
				"PasswordField" => $MerchantSettings[0]->Password,
				"PaymentID"	=>	$pid,
				"PaymentIDField"	=>	$pid,
				"ExtensionDataField" => null,
				);
                
                
                $merchantID = $MerchantSettings[0]->MerchantID;
				$VerifyPayment = mainfunction::sendVerifyPayment($verifyPaymentRequest);
				
				$responseCode = $VerifyPayment->VerifyPaymentResult->ResponseCode;
				
				//echo '<pre>'; print_r($VerifyPayment); die();
				
                if($responseCode == 0 &&  ($VerifyPayment->VerifyPaymentResult->Status == 'Approved'))
				{
				    $insert = mainfunction::insert_payment($VerifyPayment,$pid,$merchantID,$responseCode);
					//$insert = true;
					$_SESSION['is_payment_success'] = 1;
					if($insert)
					{
						$data1 = $_SESSION['data'];
						//echo  '<pre>'; echo 'here';  print_r($data1); die();
						$o_id = $data['order_id'];
						$call_API =  self::DO_API($data, $o_id);
						if($call_API)
						{
							$send_auto_email =  self::send_email($data1);
							
							//echo '<pre>'; print_r($data1); die();
							echo '<h3>'.'Your Payment was Successful'.'</h3>';
							echo '<p>'.'Thank you. Your transaction has been completed, and a receipt for your purchase has been emailed to you.'.'<p>';
							echo '<p>'.'If you have any comments or questions please do not hesitate to contact us:'.'<p>';
							echo '<p>'.'Phone: (816) 333 0799'.'<p>';
							$orders_model = F0FModel::getTmpInstance('Orders', 'J2StoreModel');
							$order = $orders_model->initOrder($order_id)->getOrder();
							$order->empty_cart();
							unset($_SESSION['pid']);
							unset($_SESSION['data']);
							die();
						}
						else
						{
						    
						    $api_data = $_SESSION['api_data'];
						    $send_failed_email =  self::send_failed_email($api_data);
						    echo '<h3>'.'Your Order is NOT Successful. Request Timeout Issue'.'</h3>';
							echo '<p>'.'Please call:(816) 333 0799'.'<p>';
							$orders_model = F0FModel::getTmpInstance('Orders', 'J2StoreModel');
							$order = $orders_model->initOrder($order_id)->getOrder();
							$order->empty_cart();
							unset($_SESSION['pid']);
							unset($_SESSION['data']);
							die();
						}
					}
					
					
				}
				else
				{
					echo '<h3>'.'Your Order is NOT Successful. Please try another credit card'.'</h3>';
					echo '<p>'.'Please call:'.'<p>';
					echo '<p>'.'Phone: (816) 333 0799'.'<p>';
					$orders_model = F0FModel::getTmpInstance('Orders', 'J2StoreModel');
					$order = $orders_model->initOrder($order_id)->getOrder();
					$order->empty_cart();
					unset($_SESSION['pid']);
					unset($_SESSION['data']);
					die();				
				}
				break;
			case "callback":
				//Its a call back. You can update the order based on the response from the payment gateway
				$vars->message = 'Some message to the gateway';
				$html = $this->_getLayout('message', $vars);
				echo $html; 
				$app->close();
				break;
			case "cancel":
				//cancel is called. 
				$vars->message = 'Sorry, you have cancelled the order';
				$html = $this->_getLayout('message', $vars);
				break;
			default:
				$vars->message = 'Seems an unknow request.';
				$html = $this->_getLayout('message', $vars);
				break;
		}

		return $html;
	} 
    function DO_API($data, $o_id)
    {
       
        //echo 'asdasdasd'; die();
        //$servicetype = mainfunction::get_servicetype($order_id);
		$session = JFactory::getSession();
		     
        $o_item = F0FModel::getTmpInstance('Carts', 'J2StoreModel')->initOrder()->getOrder();
        $m = $o_item->getItems();

        $orders_model = F0FModel::getTmpInstance('Orders', 'J2StoreModel');
        $order = $orders_model->initOrder($o_id)->getOrder();
        
       
        $payment_id = $_SESSION['pid'];
        $guest = $session->get('guest', array(), 'j2store');
		
        $register = $session->get('register', array(), 'j2store');
		
		$serviceOpt= mainfunction::get_servicetype($o_id); 
	
		$serviceOpt_select = $serviceOpt[0]->service_type;	
        if($serviceOpt[0]->service_type == 'Pickup') 
		{
			$pickup_time = $serviceOpt[0]->pickup_time;
		}
		else
		{
			$pickup_time = '';
		}
		
        $temp = array();
        if(empty($guest))
        {
            $user = JFactory::getUser();
            $address_info = F0FModel::getTmpInstance('Addresses', 'J2StoreModel')->user_id($user->id)->getFirstItem();
           
            $country_info = F0FModel::getTmpInstance('Countries', 'J2StoreModel')->getItem($address_info->country_id);
            $zone_info = F0FModel::getTmpInstance('Zones', 'J2StoreModel')->getItem($address_info->zone_id);
            
            $temp['first_name'] = $address_info->first_name;
            $temp['last_name'] = $address_info->last_name;
			$temp['email'] = $address_info->email;
            $temp['address1'] = $address_info->address_1;
            $temp['address2'] = $address_info->address_2;
			$temp['zip'] = $address_inf->zip;
            $temp['city'] = $address_info->city;
            $temp['zone_name'] = $zone_info->zone_name;
			$zone_code =  self::get_zonecode($temp['zone_name']);
            $temp['country_name'] = $country_info->country_name;
			$temp['phone1'] = $address_info->phone_1;
			$temp['phone2'] = $address_info->phone_2;
            //$temp['company'] = $address_info->company;

        }
        else
        {
           $temp['first_name'] = $guest['billing']['first_name'];
            $temp['last_name'] = $guest['billing']['last_name'];
			$temp['email'] = $guest['billing']['email'];
            $temp['address1'] = $guest['billing']['address_1'];
            $temp['address2'] = $guest['billing']['address_2'];
            $temp['city'] = $guest['billing']['city'];
			$temp['zip'] = $guest['billing']['zip'];
            $temp['zone_name'] = $guest['billing']['zone_name'];
            $zone_code =  self::get_zonecode($temp['zone_name']);
            $temp['country_name'] = $guest['billing']['country_name'];
            $temp['phone1'] = $guest['billing']['phone_1'];
			$temp['phone2'] = $guest['billing']['phone_2'];
			
		}
		
		/*if($_COOKIE['service-value']=="Delivery"){
		     $ordertotal= (floatval($order->order_total) + floatval(4.00));
		}else{
		     $ordertotal= floatval($order->order_total) + round(floatval($order->order_total) * floatval(0.1175),2); //$order->order_total;
		}*/
		
		if ($_COOKIE['tips'] != '')
    	{
    	    $tips = $_COOKIE['tips'];
    	    $tips_amount = number_format(($tips * $order->order_total),2);
    	    //$vars->amount = number_format((($tips * $total_wo_tax) + $total_wo_tax),2) ;
    	    $ordertotal= floatval($order->order_total) + round(floatval($order->order_total) * floatval(0.1175),2) + ($tips * $order->order_total) ;
    	    
    	}
    	elseif($_COOKIE['tipsTextInput'] != '')
    	{
    	    $tips = $_COOKIE['tipsTextInput'];
    	    $tips_amount = number_format($tips,2);
    	   // $vars->amount = number_format(( $tips + $total_wo_tax),2) ;
    	    $ordertotal= floatval($order->order_total) + round(floatval($order->order_total) * floatval(0.1175),2) + number_format($tips,2) ;
    	}
    	else
    	{
    	    $tips = 0;
    	    $tips_amount = 0.00;
    	    //$vars->amount = $tips + $total_wo_tax;
    	    $ordertotal= floatval($order->order_total) + round(floatval($order->order_total) * floatval(0.1175),2)  ;
    	}
    	
    	$ordertotal = number_format($ordertotal,2);
		
	//	$vars->amount = $currency->format($vars->amount, $currency_values['currency_code'], $currency_values['currency_value'], false);
		
		
        $temp['order_id'] = $order->order_id;
        $temp['invoice_no'] = $order->invoice_number;
        $temp['created_on'] = $order->created_on;
        $temp['order_total'] = $ordertotal;
        $temp['order_status'] = 'New';
        $temp['order_wo_tax'] = $order->order_subtotal_ex_tax;
        $temp['order_tax'] = $order->order_tax;
        $temp['user_email'] = $order->user_email;
        $temp['token'] = $order->token;
        $temp['item'] = $m;     

        $data1 = new stdClass();
        //$data = NULL;
        $data1->customer = (object) array(
            'userid'=> '12321',
            'firstname' => $temp['first_name'], // $guest['billing']['first_name'],
            'lastname' =>   $temp['last_name'], //$guest['billing']['last_name'],
            'address' =>   $temp['address1']." ". $temp['address2'], //$guest['billing']['address_1']." ".$guest['billing']['address_2'],
            'city' =>  $temp['city'], //$guest['billing']['city'],
            'zip' => $temp['zip'],
			'state' =>  $zone_code,
            'country' => $temp['country_name'],
			'email' => $temp['email'],
			'phone' => $temp['phone2'],
            'comments' => str_replace("'","",$order->customer_note)
            );
            
       /* if($_COOKIE['service-value']=="Delivery"){
		    $ordertotal= (floatval($order->order_total) + floatval(4.00));
		}else{
		      $ordertotal= floatval($order->order_total) + round(floatval($order->order_total) * floatval(0.1175),2); //$order->order_total;
		}*/
        
        $tax = $order->order_subtotal * .1175;
        $data1->info = (object) array(
            'orderid' => $order->order_id,
            'cartid' => $order->cart_id,
            'numberitems' => sizeof($m),
            'credit' =>0,
            'tax' => $tax,
            'subtotal' => $order->order_subtotal,
            'total' => $ordertotal,
            'paymenttype' => $order->orderpayment_type,
            'bankauthcode'=> $payment_id,
			'servicetype' => $serviceOpt_select,
            'pickuptime' => $pickup_time,
            'tip' => $tips_amount
            );

       // $order_array1 = array();   
        $object = array();//new stdClass(); 
		
	//	echo '<pre>'; print_r($m); die();
		if(!empty($m))
        {
           // $options = array();
            for($x = 0; $x < sizeof($m); $x++)
            {
                $product_id = $m[$x]->product_id;
                $guid = mainfunction::get_guid($product_id);
                if($guid == '')
                {
                    $guid = 'C3920FC5-A644-E811-BE32-BC5FF4F4E5C6';
                }
				
                if(!empty($m[$x]->options))
                {
                 // echo '<pre>';
				 // print_r($m[$x]->options);
				 // die();
				    $order_options = array();
				    $per_item_comments = "";
				    $mod_tax = 0;
				    for($y = 0; $y < sizeof($m[$x]->options); $y++)
                    { 
						
						    $mod_tax = $mod_tax + ($m[$x]->options[$y]['price'] * .1175);
                			$option_value_id = $m[$x]->options[$y]['optionvalue_id'];
                        	$product_option_id = $m[$x]->options[$y]['product_option_id'];
                        	$product_option_value_id = $m[$x]->options[$y]['product_optionvalue_id'];
                        	
                        	$guid_modifiers = mainfunction::get_modifiers_guid($product_id, $product_option_id,$product_option_value_id,$option_value_id);
						    if($guid_modifiers == '')
						    {
						        $guid_modifiers = 'C3920FC5-A644-E811-BE32-BC5FF4F4E5C6';
						    }
						    
						    $order_options[$y] = (object) array(
                        	'Item-Index'=>  $y,
                        	'name'=> str_replace("'","",$m[$x]->options[$y]['option_value']),
                        	'itemid'=> $guid_modifiers,
                        	'parentid'=>$guid,
                        	'quantity'=> $m[$x]->product_qty,
                        	'price'=> $m[$x]->options[$y]['price'],
                        	'comments'=> ""
                        	); 
                        	
                	} 
					
					
					$order_array1[$x] = (object) array(
                    'itemid' => $guid,//$m[$x]->product_id,
                    'name' => str_replace("'","",$m[$x]->sku),
                    'quantity' => $m[$x]->product_qty,
                    'weight' => $m[$x]->weight,
                    'category' => $m[$x]->product_options,
                    'discount' => 0,
                    'price' => $m[$x]->price,
                    'itemTax' => ($m[$x]->price * .1175) + $mod_tax,
                    'comments' => str_replace("'","",$per_item_comments),
                    'mods' => $order_options
                    ); 

               
                }
                else
                {
                     $order_array1[$x] = (object) array(
                    'itemid' => $guid,//$m[$x]->product_id,
                    'name' => str_replace("'","",$m[$x]->sku),
                    'quantity' => $m[$x]->product_qty,
                    'weight' => $m[$x]->weight,
                    'category' => $m[$x]->product_options,
                    'discount' => 0,
                    'price' => $m[$x]->price,
                     'itemTax' => ($m[$x]->price * .1175),
                    'comments' => str_replace("'","",$per_item_comments)
                    ); 

	            }
				
            }     
        }
        
		//die();
		//echo 'asdasdasd';
		//echo '<pre>'; print_r($order_array1); die();
		// setup special instructions
	/*	if(!empty($order_array1))
		{
			for($r = 0; $r < sizeof($order_array1); $r++)
			{
				if(!empty($order_array1[$r]->mods))
				{
					for($t = 0; $t < sizeof($order_array1[$r]->mods); $t++)
					{
						//echo '<pre>'; print_r($order_array1[$r]->mods); die();
						if($order_array1[$r]->mods[$t]->itemid == '')
						{
							$option_comments[$r] = $order_array1[$r]->mods[$t]->name;
							unset($order_array1[$r]->mods[$t]);
						}
					}
					
				}
				$order_array1[$r]->comments = str_replace("'","",$option_comments[$r]);

			}
		}
		
	*/	
        $data1->items = $order_array1;

        $json_order_data = json_encode($data1);
        
        $transaction_date = $order->created_on;
        
        //echo '<pre>'; print_r($data1); die();
        $pre_insert_api_transaction = mainfunction::pre_insert_api_transaction($json_order_data,$payment_id,$transaction_date);
        
        
        /*$request_url = 'http://73.8.50.71:8091/eCommerceConnect/api/order/v1/foodorder/';
        $ch = curl_init();
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: {"Bearer":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJTb3V0aDQwIiwibm9uY2UiOiIyODcwNSIsImp0aSI6ImFhMWQ3MDU1LWRmMGUtNDBmOC05OGNiLWE0MTRmODcwYTRlMCIsImlhdCI6IjcvMjcvMjAxOCA4OjE1OjMwIEFNIiwidHlwIjoiOTkiLCJuYmYiOjE1MzI2NzkzMzAsImV4cCI6MTUzMjY3OTkzMCwiaXNzIjoiUE9TQ2xvdWRTeW5jQDI4NzA1IiwiYXVkIjoiMTI3LjAuMC4xIn0.znarSvErLWjhc34eY8ItjRfVhAAQlyKIZnFRdA7G_jg"}' 
        ));
        */
	    
	   //POS Restaurant 
	   
	   $ch = curl_init();
	   $request_url = 'http://23.228.166.151:8888/POSCloudSync/api/order/v1/foodorder/';
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: {"Bearer":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJEUEgiLCJub25jZSI6IjI4NzA1IiwianRpIjoiYWExZDcwNTUtZGYwZS00MGY4LTk4Y2ItYTQxNGY4NzBhNGUwIiwiaWF0IjoiMTAvNC8yMDE4IDI6MjM6MzkgUE0iLCJ0eXAiOiI5OSIsIm5iZiI6MTUzODY2MzAxOSwiZXhwIjoxNTM4NjYzNjE5LCJpc3MiOiJQT1NDbG91ZFN5bmNAMjg3MDUiLCJhdWQiOiIxMjcuMC4wLjEifQ.cydgJHaUUHc5JSLJxLGy3zQkBlGQed6ssws2-4nyW0E"}' 
        )); 
       
	
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_URL, $request_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_order_data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $response = curl_exec($ch);
        
        /*Check for any errors*/
        //$errorMessage = curl_exec($ch);
        $status_response = json_decode($response);
        
        //echo '<pre>'; print_r($json_order_data); die();
        
        $status_code = $status_response->status_code;
        $insert_api_transaction = mainfunction::insert_api_transaction($json_order_data,$payment_id,$transaction_date,$status_code);     
		
		/*echo '<pre>';
		echo '<pre>'; print_r($json_order_data); 
		echo '<pre>';
		echo 'response';
		print_r($response);
		echo '<pre>';
		print_r($data1);
		die();
		*/
		
	   if($status_code == 201)
        {
            $_SESSION['data'] = $data1;
            //$order->empty_cart();
            return true;
        }
        else
        {
            $_SESSION['api_data'] = $data1;
            return false;
        }

        curl_close($ch);
    }
     function send_email($data1)
    {
        $data1 = $_SESSION['data'];
		
		$mailer = JFactory::getMailer();
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $config = JFactory::getConfig();
        $sender = array( 
        $config->get( 'mailfrom' ),
        $config->get( 'fromname' ) 
        );
		
		//echo '<pre>'; print_r($data1); die();
        //$mailer->setSender($sender);
        //$user = JFactory::getUser();
        //$recipient[] = 'RM-Team@pos.partners';
        //$recipient[] = 'Accounting@pos.partners';
        $recipient[] = 'jasonr.district@gmail.com';
        $recipient[] = 'pcorpuz@servingintel.com';
        $recipient[] = $data1->customer->email;

        //$recipient[] = $email3;
        //$recipient = 'pinkycorpuz@pos.partners';//$user->email;

        $mailer->addRecipient($recipient);
    
      
         $message = '<html><body>';
        $message = '<div style="width:85%;margin:auto;">';
        $message .= '<table width="100%" style=" border-collapse:collapse; background-color:#ffffff;font: 12px/16px Roboto,Helvetica Neue,sans-serif; color:#58585b; margin-bottom:-45px;">';
        $message .= '<tr style="background-color:#58585b;color:#fff;text-align:center;font:25px/21px Roboto,Helvetica Neue,sans-serif;color:#ffffff;">';
        $message .= '<td colspan="3" style="padding:20px;line-height:1.5;font-size:20px;" >';
        $message .= 'NEW ORDER CUSTOMER FROM '.'<b style="color:#f49232;">'. 'DISTRICT POUR HOUSE + KITCHEN'.'</b>';
        $message .= '</td>';
        $message .= '</tr>';
        $message .= '<tr style="color:#333;text-align:center;">';
        $message .= '<td colspan="3" style="padding:10px;"><br>';
        $message .= 'You have received an order from '.'<span style="font-weight:700;">'.$data1->customer->firstname.' '.$data1->customer->lastname.'</span>';
        $message .= '<br>';
        $message .= 'The order is as follows:';
        $message .= '</td>';
        $message .= '</tr>';
        $message .= '<tr>';
        $message .= '<td colspan="3">';
            $message .= '<table width="100%" style="margin-top:-50px;border-collapse:collapse; background-color:#ffffff;font: 12px/16px Roboto,Helvetica Neue,sans-serif; color:#58585b; ">';
        
            $message .= '<tr>';
            $message .= '<td width="40%" style="border:solid 2px #ccc;padding:10px; ">';
            $message .= '<b>ITEM</b>';
            $message .= '</td>';
            $message .= '<td width="30%" style="border:solid 2px #ccc;padding:10px; ">';
            $message .= '<b>QUANTITY</b>';
            $message .= '</td>';
            $message .= '<td width="30%" style="border:solid 2px #ccc;padding:10px; ">';
            $message .= '<b>PRICE</b>';
            $message .= '</td>';  
            $message .= '</tr>';
        
            

            for($x = 0; $x < sizeof($data1->items); $x++)
            {
                $message .= '<tr>';
                $message .= '<td style="border:solid 2px #ccc;padding:10px; ">';
                $message .= $data1->items[$x]->name;
                $message .= '</td>';
                $message .= '<td style="border:solid 2px #ccc;padding:10px; ">';
                $message .= number_format($data1->items[$x]->quantity,0);
                $message .= '</td>';
                $message .= '<td style="border:solid 2px #ccc;padding:10px; ">';
                $message .= '$'.number_format(($data1->items[$x]->quantity * $data1->items[$x]->price),2);
                $message .= '</td>';
                $message .= '</tr>';
                $message .= '<br>';
                if(!empty($data1->items[$x]->mods))
                {
                    for($y = 0; $y < sizeof($data1->items[$x]->mods); $y++)
                    {
                        $message .= '<tr >';
                        $message .= '<td style="border:solid 2px #ccc;padding:10px; ">';
                        $message .= '<div style=" font-style:italic;margin-left:20px;">'.$data1->items[$x]->mods[$y]->name.'</div>';
                        $message .= '</td>';
                        $message .= '<td style="border:solid 2px #ccc;padding:10px; ">';
                        $message .= number_format($data1->items[$x]->mods[$y]->quantity,0);
                        $message .= '</td>';
                        $message .= '<td style="border:solid 2px #ccc;padding:10px; ">';
                        $message .= '$'.number_format(($data1->items[$x]->mods[$y]->quantity * $data1->items[$x]->mods[$y]->price),2);
                        $message .= '</td>';
                        $message .= '</tr>';
                        //$message .= '<br>';
                    }
                }
                $message .= '<tr>';
                //$message .= '<td colspan="3">';
                $message .= '<br>';
                $message .= '</td>';
                $message .= '</tr>';
            }
        
        $message .= '<tr>';
        $message .= '<td colspan="2" align="left" style="border:solid 2px #ccc;padding:10px;"> ';
        $message .= '<b>TIPS</b>';
        $message .= '</td>';
        $message .= '<td style="border:solid 2px #ccc;padding:10px;">';
        $message .= $data1->info->tip;
        $message .= '</td>';
        $message .= '</tr>';    

       if($data1->info->servicetype == 'Delivery')
        {
            $message .= '<tr>';
            $message .= '<td colspan="2" align="left" style="border:solid 2px #ccc;padding:10px;">';
            $message .= '<b>DELIVERY FEE';
            $message .= '</td>';
            $message .= '<td style="border:solid 2px #ccc;padding:10px;">';
            $message .= '$4.00';
            $message .= '</td>';
            $message .= '</tr>';
            
            $total = $data1->info->total;
            $sub_total = $data1->info->sub_total;
            $message .= '<tr>';
            $message .= '<td colspan="2" align="left" style="border:solid 2px #ccc;padding:10px;">';
            $message .= '<b>TOTAL';
            $message .= '</td>';
            $message .= '<td style="border:solid 2px #ccc;padding:10px;">';
            $message .= '$'.number_format($total,2).' with 11.75% tax';
            $message .= '</td>';
            $message .= '</tr>';

            
            
        }
        else
        {
            $sub_total = $data1->info->sub_total;
            $message .= '<tr>';
            $message .= '<td colspan="2" align="left" style="border:solid 2px #ccc;padding:10px;">';
            $message .= '<b>SUB TOTAL</b>';
            $message .= '</td>';
            $message .= '<td style="border:solid 2px #ccc;padding:10px;">';
            $message .= '$'.number_format($data1->info->subtotal,2);
            $message .= '</td>';
            $message .= '</tr>';
            
            $sub_total = $data1->info->sub_total;
            $message .= '<tr>';
            $message .= '<td colspan="2" align="left" style="border:solid 2px #ccc;padding:10px;">';
            $message .= '<b>TAX (11.75%)</b>';
            $message .= '</td>';
            $message .= '<td style="border:solid 2px #ccc;padding:10px;">';
            $message .= '$'.number_format($data1->info->tax,2);
            $message .= '</td>';
            $message .= '</tr>';
            
            $message .= '<tr>';
            $message .= '<td colspan="2" align="left" style="border:solid 2px #ccc;padding:10px;">';
            $message .= '<b>TOTAL';
            $message .= '</td>';
            $message .= '<td style="border:solid 2px #ccc;padding:10px;">';
            $message .= '$'.number_format($data1->info->total,2);
            $message .= '</td>';
            $message .= '</tr>';
        }
        
      

        $message .= '<tr>';
        $message .= '<td colspan="2" align="left" style="border:solid 2px #ccc;padding:10px;"> ';
        $message .= '<b>PAYMENT METHOD</b>';
        $message .= '</td>';
        $message .= '<td style="border:solid 2px #ccc;padding:10px;">';
        $message .= 'Credit Card';
        $message .= '</td>';
        $message .= '</tr>';

        $message .= '<tr>';
        $message .= '<td colspan="3">';
        $message .= '<br>';
        $message .= '</td>';
        $message .= '</tr>';

        //$message .= '<tr style="height:35px;">';
        //$message .= '<td colspan="3" style="text-align:center;font-size:14px;background-color:#EFEFEF;">';
        //$message .= '<b>CUSTOMER DETAILS</b>';
        //$message .= '</td>';
        //$message .= '</tr>';

        $message .= '<tr>';
        $message .= '<td colspan="3">';
        $message .= '<br>';
        $message .= '</td>';
        $message .= '</tr>';
        
        $message .= '</table>';
        
        $message .= '</td>';
        $message .= '</tr>';
        
        $message .= '<tr>';
        $message .= '<td colspan="3" >';
        $message .= '<table width="100%" style="color:#58585b;">';

        $message .= '<tr>';
        $message .= '<td colspan="1"align="left" style="padding:10px; "> ';
         $message .= '<b style="font:16px/21px Roboto,Helvetica Neue,sans-serif;color:#f49232; font-weight:bold;">ORDER INFORMATION</b><br><br>';
        $message .= '</td>';
        $message .= '<td style="padding:10px;">';
         $message .= '<b style="font:16px/21px Roboto,Helvetica Neue,sans-serif;color:#f49232; font-weight:bold; ">CUSTOMER INFORMATION</b><br><br>';
        $message .= '</td>';
        $message .= '</tr>';

        $message .= '<tr>';
        $message .= '<td valign="top" width="50%" style="border:solid 2px #ccc;padding:10px; font: 12px/16px Roboto,Helvetica Neue,sans-serif; font-style:italic; ">';
        $message .= 'Order Id: '.$data1->info->orderid.'<br>';
        $message .= 'Service Options: '.$data1->info->servicetype.'<br>';
        if($data1->info->servicetype == 'Pick Up')
        {
            $message .= 'Pick-Up Time: '.$data1->info->pickuptime.'<br>';
        }
        $message .= 'Number of Items: '.$data1->info->numberitems.'<br>';
        $message .= 'Order Amount: '.'$'.number_format($data1->info->total,2).' with 11.75% tax'.'<br>';
        $message .= 'Order Status: New ';
        $message .= '</td>';
        $message .= '<td valign="top" width="50%" style="border:solid 2px #ccc;padding:10px; font:12px/16px Roboto,Helvetica Neue,sans-serif; font-style:italic; ">';
        $message .= $data1->customer->firstname.' '.$data1->customer->lastname .'<br>';
        $message .= 'Email: '.$data1->customer->email.'<br>';
        $message .= 'Phone: '.$data1->customer->phone .'<br>';
        
        if($data1->info->servicetype == 'Delivery')
        {
            $message .= $data1->customer->address.'<br>';
            $message .= $data1->customer->zip .'<br>';
            $message .= $data1->customer->city .'<br>';
            $message .= $data1->customer->state .'<br>';
            $message .= $data1->customer->country .'<br>';
        }
        $message .= '</td>';
        $message .= '</table>';
        $message .= '</td>';
        $message .= '</tr>';



		$message .= '<tr>';
        $message .= '<td colspan="3">';
        $message .= '<br>';
		$message .= '</td>';
        $message .= '</tr>';
		
		
		
        $message .= '<tr>';
        $message .= '<td>';
        $message .= '<br>';
        $message .= '</td>';
        $message .= '</tr>';


        $message .= '<tr>';
        $message .= '<td colspan="3">';
        //$message .= '<hr>';
        $message .= '</td>';
        $message .= '</tr>';
        
        $message .= '<tr >';
        $message .= '<td colspan="3" style="text-align:center;  ">';
        $message .= 'For any queries and details please get in touch with us. We will be glad to be of service.'.'<br>';
        $message .= 'Phone: (816) 333 0799'.'<br>';
        $message .= '</td>';
        $message .= '</tr>';
        $message .= '</table>';
		
		$message .= "</div>";
		$message .= "</div>";
        $message .= "</body></html>";
		
        //echo '<pre>'; echo $message; die();
        //$body   = "Your body string\nin double quotes if you want to parse the \nnewlines etc";
        $mailer->setSubject('Your order has been placed with District Pour House + Kitchen');
        $mailer->setBody($message);

        $send = $mailer->Send();
        return true;	
      
    }
    
    function send_failed_email($data1)
    {
    	$mailer = JFactory::getMailer();
        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $config = JFactory::getConfig();
        $sender = array( 
        $config->get( 'mailfrom' ),
        $config->get( 'fromname' ) 
        );
	
	    $recipient[] = 'jasonr.district@gmail.com';
        $recipient[] = 'pcorpuz@servingintel.com';
        $recipient[] = 'ebeltran@servingintel.com';
        $mailer->addRecipient($recipient);
    
      
        $message = '<html><body>';
        $message .= '<div style="width:85%;margin:auto;">';
        $message .= 'NEW ORDER FAILED FROM CUSTOMER'.'</b>'.'<br>';
        $message .= $data1->customer->firstname.' '.$data1->customer->lastname .'<br>';
        $message .= 'Email: '.$data1->customer->email.'<br>';
        $message .= 'Phone: '.$data1->customer->phone .'<br>';
        $message .= "</div>";
        $message .= "</body></html>";
		
        //echo '<pre>'; echo $message; die();
        //$body   = "Your body string\nin double quotes if you want to parse the \nnewlines etc";
        $mailer->setSubject('Failed order has been placed with District Pour House + Kitchen');
        $mailer->setBody($message);

        $send = $mailer->Send();
        return true;	
    	
    }

    function get_zonecode($zone_name)
    {
        if($zone_name=='Alabama'){ $zc = 'AL';}
        elseif($zone_name=='Alaska'){ $zc = 'AK';}
        elseif($zone_name=='Arizona'){ $zc = 'AZ';}
        elseif($zone_name=='Arkansas'){ $zc = 'AR';}
        elseif($zone_name=='California'){ $zc = 'CA';}
        elseif($zone_name=='Colorado'){ $zc = 'CO';}
        elseif($zone_name=='Connecticut'){ $zc = 'CT';}
        elseif($zone_name=='Delaware'){ $zc = 'DE';}
        elseif($zone_name=='Florida'){ $zc = 'FL';}
        elseif($zone_name=='Georgia'){ $zc = 'GA';}
        elseif($zone_name=='Hawaii'){ $zc = 'HI';}
        elseif($zone_name=='Idaho'){ $zc = 'ID';}
        elseif($zone_name=='Illinois'){ $zc = 'IL';}
        elseif($zone_name=='Indiana'){ $zc = 'IN';}
        elseif($zone_name=='Iowa'){ $zc = 'IA';}
        elseif($zone_name=='Kansas'){ $zc = 'KS';}
        elseif($zone_name=='Kentucky'){ $zc = 'KY';}
        elseif($zone_name=='Louisiana'){ $zc = 'LA';}
        elseif($zone_name=='Maine'){ $zc = 'ME';}
        elseif($zone_name=='Maryland'){ $zc = 'MD';}
        elseif($zone_name=='Massachusetts'){ $zc = 'MA';}
        elseif($zone_name=='Michigan'){ $zc = 'MI';}
        elseif($zone_name=='Minnesota'){ $zc = 'MN';}
        elseif($zone_name=='Mississippi'){ $zc = 'MS';}
        elseif($zone_name=='Missouri'){ $zc = 'MO';}
        elseif($zone_name=='Montana'){ $zc = 'MT';}
        elseif($zone_name=='Nebraska'){ $zc = 'NE';}
        elseif($zone_name=='Nevada'){ $zc = 'NV';}
        elseif($zone_name=='New Hampshire'){ $zc = 'NH';}
        elseif($zone_name=='New Jersey'){ $zc = 'NJ';}
        elseif($zone_name=='New Mexico'){ $zc = 'NM';}
        elseif($zone_name=='New York'){ $zc = 'NY';}
        elseif($zone_name=='North Carolina'){ $zc = 'NC';}
        elseif($zone_name=='North Dakota'){ $zc = 'ND';}
        elseif($zone_name=='Ohio'){ $zc = 'OH';}
        elseif($zone_name=='Oklahoma'){ $zc = 'OK';}
        elseif($zone_name=='Oregon'){ $zc = 'OR';}
        elseif($zone_name=='Pennsylvania'){ $zc = 'PA';}
        elseif($zone_name=='Rhode Island'){ $zc = 'RI';}
        elseif($zone_name=='South Carolina'){ $zc = 'SC';}
        elseif($zone_name=='South Dakota'){ $zc = 'SD';}
        elseif($zone_name=='Tennessee'){ $zc = 'TN';}
        elseif($zone_name=='Texas'){ $zc = 'TX';}
        elseif($zone_name=='Utah'){ $zc = 'UT';}
        elseif($zone_name=='Vermont'){ $zc = 'VT';}
        elseif($zone_name=='Virginia'){ $zc = 'VA';}
        elseif($zone_name=='Washington'){ $zc = 'WA';}
        elseif($zone_name=='West Virginia'){ $zc = 'WV';}
        elseif($zone_name=='Wisconsin'){ $zc = 'WI';}
        elseif($zone_name=='Wyoming'){ $zc = 'WY';}
        else { $zc = 'CA';}
        return $zc;
    }

}