<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Loan_investment
 * @author     gadiel_Rojo <gadsred@gmail.com>
 * @copyright  2016 gadiel_Rojo
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class Loan_investmentFrontendHelper
 *
 * @since  1.6
 */
class Loan_investmentHelpersLoan_investment
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_loan_investment/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_loan_investment/models/' . strtolower($name) . '.php';
			$model = JModelLegacy::getInstance($name, 'Loan_investmentModel');
		}

		return $model;
	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}

    /**
     * Gets the edit permission for an user
     *
     * @param   mixed  $item  The item
     *
     * @return  bool
     */
    public static function canUserEdit($item)
    {
        $permission = false;
        $user       = JFactory::getUser();

        if ($user->authorise('core.edit', 'com_loan_investment'))
        {
            $permission = true;
        }
        else
        {
            if (isset($item->created_by))
            {
                if ($user->authorise('core.edit.own', 'com_loan_investment') && $item->created_by == $user->id)
                {
                    $permission = true;
                }
            }
            else
            {
                $permission = true;
            }
        }

        return $permission;
    }
    public function getRepayment($borrow_amount,$paid_back_in_months,$advertised_rate,$type,$loan) {
		if(!$borrow_amount)
		{
			$borrow_amount=350000;
		}
    	if($type=='weekly')	{
			if($loan=='1')	{
				$adout = number_format(($borrow_amount*(float)$advertised_rate)/100/52,2);
			} else {
				$ir_permonth= ((float)$advertised_rate/100)/12;						
				$principal_interest =  $borrow_amount * ($ir_permonth)/(1-pow((1+$ir_permonth),-$paid_back_in_months));		
				$principal_interest = ($principal_interest*12)/52;	
				$adout =  number_format($principal_interest,2);
				
			}	
		} else {
			if($loan=='1')			{
				$adout = number_format(($borrow_amount*(float)$advertised_rate)/100/12,2);
			} else	{ 
				$ir_permonth= ((float)$advertised_rate/100)/12;
				$principal_interest =  $borrow_amount * ($ir_permonth)/(1-pow((1+$ir_permonth),-$paid_back_in_months));
				$principal_interest = ($principal_interest*12)/12;	
				$adout = number_format($principal_interest,2);
			}
		}
		return $adout;
    }
	//create a url friendly term from the loan_display_name
	public function getUrlLoanDisplayName($name) {
		//remove all non alphanumeric character
		$name = preg_replace("/[^A-Za-z0-9 ]/", '',$name);
		//replace spaces with -
		$name = preg_replace("/ /i", '-',$name);
		$name = $name.'/';
		return $name;	
	}

	//gads loan investment functions
	public function getOtherLoans($provider_id,$id)
	{  
		$db =  JFactory::getDbo();
		$query = "SELECT id,user_id,loan_display_name,advertised_rate,comparison_rate,minimum_deposit,CHAR_LENGTH(loan_display_name) as l From #__loan_investment_info 
					Where user_id ='".$provider_id."' AND id!='".$id."' AND loan_display_name!='' order by l";
		$db->setQuery($query);
		$other_loans = $db->loadObjectList();
		
		return $other_loans;
	}
		
	public function getSimilarLoans($principal_interest,$borrowing_amount_range_min,$borrowing_amount_range_max,$lvr,$minimum_deposit,$id)
	{  
		$db =  JFactory::getDbo();
		if ($id > 0 && $principal_interest > 0) {
		
		$query = "SELECT id,user_id,loan_display_name,advertised_rate,comparison_rate,minimum_deposit,CHAR_LENGTH(loan_display_name) as l From #__loan_investment_info 
					Where principal_interest='".$principal_interest."' 
					AND (borrowing_amount_range_min >= ".$borrowing_amount_range_min." and borrowing_amount_range_max <= ".$borrowing_amount_range_max.") 
					and maximum_lvr <= ".$lvr."
					and minimum_deposit >= ".$minimum_deposit." and user_id!='".$id."' AND loan_display_name!='' 
					order by loan_display_name";
		//echo $query;
		$db->setQuery($query);
		$similar_loans = $db->loadObjectList();
		
		return $similar_loans;
		}
	}
	
	public function getSimilarLoansWidget($au_state,$borrow_amount,$lvr,$deposit,$term,$id,$prop_type)
	{  
		
		if($prop_type=='Developer')
		{
			$total_config = ipropertyHTML::getTotalNumberofConfigs($id);
			$display = ipropertyHTML::getPropertyOpen($id);
			$no = $total_config+3;
			if($display->suite_located){$no=$no+1;}
			if($display->opening_hrs){$no=$no+2;}
			$limit = "limit $no";
		}
		else
		{
			$limit = "limit 3";
		}
		
		
		$db =  JFactory::getDbo();
		$query = "SELECT id,user_id,loan_display_name,advertised_rate,comparison_rate,minimum_deposit,interest_rate_structure,is_sponsor,date_modified,CHAR_LENGTH(loan_display_name) as l From #__loan_investment_info 
					Where  states_applicable like'%".$au_state."%' AND interest_only='true' 
					and (".$borrow_amount." between borrowing_amount_range_min and borrowing_amount_range_max)
					AND (loan_term_max <= $term)
					and maximum_lvr <= $lvr
					and minimum_deposit >= $deposit AND loan_display_name!='' AND user_id!='0' 
					order by (is_sponsor*1) desc, id asc $limit";	
	
		$db->setQuery($query);
		$similar_loans = $db->loadObjectList();
		
		return $similar_loans;
	}

	
	public function getLoanRecords()
	{  
		$db =  JFactory::getDbo();
		$query = "SELECT id From #__loan_investment_info 
					Where loan_display_name !=''";
		$db->setQuery($query);
		$db->query();
		
		$num_rows = $db->getNumRows();
		$result = $db->loadRowList();

		return number_format($num_rows);
	}
	
	public function getProviderLoanRecords($id)
	{  
		$db =  JFactory::getDbo();
		$query = "SELECT id From #__loan_investment_info 
					Where user_id ='$id'";
		$db->setQuery($query);
		$db->query();
		
		$num_rows = $db->getNumRows();
		$result = $db->loadRowList();

		return number_format($num_rows);
	}
	public function getProviderList($limit=0,$term=null,$state=1)	{  
	
		$db =  JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('p.*');

		$query->from('#__loan_investment_providers p');		

		if (strlen($term) > 0) $query->where("name like '%".$term."%'");

		$query->where("p.state = ".$state);
		$query->order('p.provider_name asc');
		if ($limit != 0) $query->setLimit($limit);
		//echo $query;
		$db->setQuery($query);
		$provider = $db->loadObjectList();
		
		return $provider;
	}
	public function getProviderWebSite($provider_id)
	{  
		$db =  JFactory::getDbo();
		$query = "SELECT website From #__loan_investment_providers  
					Where id ='".$provider_id."'";
		$db->setQuery($query);
		$provider = $db->loadObject();
		
		return $provider->website;
	}
	
	public function getProviderLogo($provider_id)
	{  
		$db =  JFactory::getDbo();
		$query = "SELECT provider_logo From #__loan_investment_providers  
					Where id ='".$provider_id."'";
		$db->setQuery($query);
		$provider = $db->loadObject();
		
		return $provider->provider_logo;
	}
	
	public function getProviderName($provider_name)
	{  
		$db =  JFactory::getDbo();
		$query = "SELECT id,provider_name From #__loan_investment_providers  
					Where provider_name like'".$provider_name."' and provider_name!=''";
		$db->setQuery($query);
		$provider = $db->loadObject();
		
		return $provider;
	}
	
	public function getProviderId($provider_name)
	{
		$db =  JFactory::getDbo();
		$query = "SELECT id From #__loan_investment_providers 
					Where provider_name ='".$provider_name."'";
		$db->setQuery($query);
		$provider = $db->loadObject();
		return $provider->id;
	}
	
	public function getProviderById($id)
	{  
		$db =  JFactory::getDbo();
		$query = "SELECT provider_name From #__loan_investment_providers  
					Where id = '".$id."'";
		$db->setQuery($query);
		$provider = $db->loadObject();
		
		return $provider->provider_name;
	}
	
	public function AddProvider($loan_provider)
	{
		$db = JFactory::getDBO();
		$query = "Insert Into #__loan_investment_providers (state,provider_name) Values('1','".$loan_provider."')";
		$db->setQuery($query);
		$db->execute();
		$provider_id = $db->insertid();
		return $provider_id;
	}
	
	public function UpdateProvider($loan_provider)
	{
		$db = JFactory::getDBO();
		$query = "Update #__loan_investment_providers set provider_type='1' where provider_name='$loan_provider'";
		$db->setQuery($query);
		$db->execute();
		
	}
	
	public function getLoanInfoUrl($url)
	{
		$db =  JFactory::getDbo();
		$query = "SELECT url From #__loan_investment_info 
					Where url ='".$url."'";
		$db->setQuery($query);
		$info_url = $db->loadObject();
		return $info_url->url;
	}
	
	public function AddLoanInfo($loan_data)
	{
		$user = JFactory::getUser()->id;
		if(!$user)
		{
			$user='0';
		}
		$loan_data = implode("','",$loan_data);
		$db = JFactory::getDBO();
		$query = "Insert Into #__loan_investment_info (user_id,
														advertised_rate,
														comparison_rate,
														minimum_deposit,
														application_fee,
														loan_display_name, 
														maximum_lvr, 
														loan_term, 
														borrowing_amount_range,
														refinance, 
														line_of_credit, 
														self_managed_super, 
														interest_rate_structure, 
														interest_only, 
														loan_allows_split_interest_rate, 
														principal_interest, 
														states_applicable, 
														redraw_facility, 
														redraw_fee, 
														extra_repayments, 
														weekly_repayments, 
														fortnightly_repayments, 
														monthly_repayments, 
														url,
														borrowing_amount_range_min,
														borrowing_amount_range_max,
														loan_term_min,
														loan_term_max,
														date_created, 
														date_modified, 
														created_by)
															
					Values('".$loan_data."','".date("Y-m-d h:i:s")."','".date("Y-m-d h:i:s")."','".$user."')";
		$db->setQuery($query);
		$db->execute();	
	}
	
	public function UpdateLoanInfo($loan_data)
	{	
		$url = $loan_data[23];
		//$data = array_shift($loan_data);
		
		//check current info 
		// $db =  JFactory::getDbo();
		// $query = "SELECT 
						// loan_display_name, 
						// maximum_lvr, 
						// loan_term, 
						// borrowing_amount_range,
						// refinance, 
						// line_of_credit, 
						// self_managed_super, 
						// interest_rate_structure,
						// interest_only, 
						// loan_allows_split_interest_rate, 
						// principal_interest, 
						// states_applicable, 
						// redraw_facility, 
						// redraw_fee, 
						// extra_repayments, 
						// weekly_repayments, 
						// fortnightly_repayments, 
						// monthly_repayments
					// From #__loan_investment_info 
					// Where url ='".$url."'";
		// $db->setQuery($query);
		// $data_exist = $db->loadRow();
		// var_dump($data_exist);exit();
		// if($data==$data_exist)
		// {
			// echo 'equal';
		// }
		
		
		$db = JFactory::getDBO();
		$query = "Update #__loan_investment_info set
								advertised_rate ='".$loan_data[1]."',
								comparison_rate ='".$loan_data[2]."',
								minimum_deposit ='".$loan_data[3]."',
								application_fee ='".$loan_data[4]."',
								loan_display_name ='".$loan_data[5]."', 
								maximum_lvr ='".$loan_data[6]."', 
								loan_term ='".$loan_data[7]."', 
								borrowing_amount_range ='".$loan_data[8]."',
								refinance ='".$loan_data[9]."', 
								line_of_credit ='".$loan_data[10]."', 
								self_managed_super ='".$loan_data[11]."', 
								interest_rate_structure ='".$loan_data[12]."',
								interest_only ='".$loan_data[13]."', 
								loan_allows_split_interest_rate ='".$loan_data[14]."', 
								principal_interest ='".$loan_data[15]."', 
								states_applicable ='".$loan_data[16]."', 
								redraw_facility ='".$loan_data[17]."', 
								redraw_fee ='".$loan_data[18]."', 
								extra_repayments ='".$loan_data[19]."', 
								weekly_repayments ='".$loan_data[20]."', 
								fortnightly_repayments ='".$loan_data[21]."', 
								monthly_repayments ='".$loan_data[22]."',
								date_modified = '".date("Y-m-d h:i:s")."',
								borrowing_amount_range_min ='".$loan_data[24]."',
								borrowing_amount_range_max ='".$loan_data[25]."',
								loan_term_min ='".$loan_data[26]."',
								loan_term_max ='".$loan_data[27]."'
					Where url = '".$url."'";
		$db->setQuery($query);
		$db->execute();	
	}
	
	public function scrapeByPage($final_urls,$page)
	{		
		 foreach($final_urls as $final_url)
			{
				//per pages
					$ch = curl_init();
					
					$url = 'http://www.ratecity.com.au'.$final_url;
					//$url = "http://www.ratecity.com.au/home-loans/resi-mortgage-corp/inv-smart-pro-plus-200-499k?h_max_borrowing_amount=300000&amp;h_max_loan_term=30";
					$proxy = 'proxy.crawlera.com:8010';
					$proxy_auth = '5df45f6c25474d5f831f8ae76e02a715';

					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_PROXY, $proxy);
					curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_auth);
					curl_setopt($ch, CURLOPT_HEADER, 1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_CAINFO, JURI::base().'crawlera-ca.crt');
					
					$scrape = curl_exec($ch);
					curl_close($ch);
					
					$loan_info =  strip_tags($scrape,'<span>');
					
					
					//get provider
					$loan_provider = explode('"displayName":"',$loan_info);
					$loan_provider = explode('","',$loan_provider[1]);
					$loan_provider = str_replace("'","",$loan_provider[0]);
					
						//if empty next loop
						if(!$loan_provider || $loan_provider=='')
						{
							continue;
						}
						
						//check provider
						$provider_exist = Loan_investmentHelpersLoan_investment::getProviderName($loan_provider);
						
						//add new provider
						if(!$provider_exist && $loan_provider!='' && $loan_provider)
						{
							$provider_id = Loan_investmentHelpersLoan_investment::AddProvider($loan_provider);
						}
						else
						{
							$provider_id =$provider_exist->id;
						}
						
					//init_data	
					$loan_data='';	
					
					$loan_attr = explode('"displayName":"'.$loan_provider.'","isBank"',$loan_info);
					
					//user_id	
					$loan_data[] =	$provider_id;
					//var_dump($loan_attr[1]);exit();
					//get advertised_rate
					$advertised_rate = explode('"revertRate":',$loan_attr[1]);
					$advertised_rate = explode(',',$advertised_rate[1]);
					$loan_data[] = strip_tags($advertised_rate[0]);

					//get comparison_rate
					$comparison_rate = explode('"comparisonRate":',$loan_attr[1]);
					$comparison_rate = explode(',',$comparison_rate[1]);
					$loan_data[] = strip_tags($comparison_rate[0]);
					
					//get minimum_deposit
					$minimum_deposit = explode('"minDeposit":',$loan_attr[1]);
					$minimum_deposit = explode(',',$minimum_deposit[1]);
					$loan_data[] = strip_tags($minimum_deposit[0]);
					
					//get application_fee
					$application_fee = explode('"upfrontFee":',$loan_attr[1]);
					$application_fee = explode(',',$application_fee[1]);
					$loan_data[] = strip_tags(str_replace('$','',$application_fee[0]));
					
					//get display name
					$loan_display_name = explode('"name":"',$loan_attr[1]);
					$loan_display_name = explode('","',$loan_display_name[2]);
					$loan_data[] = str_replace("'","",$loan_display_name[0]);
					
					//get maximum_lvr
					$maximum_lvr = explode('"maxLVR":',$loan_attr[1]);
					$maximum_lvr = explode(',',$maximum_lvr[1]);
					$loan_data[] = strip_tags($maximum_lvr[0]);
					
					//get Loan Term
					$max_loan_term = explode('"maxLoanTerm":',$loan_attr[1]);
					$max_loan_term = explode(',',$max_loan_term[1]);
					$max_loan_term = $max_loan_term[0];

					$min_loan_term = explode('"minLoanTerm":',$loan_attr[1]);
					$min_loan_term = explode(',',$min_loan_term[1]);
					$min_loan_term = $min_loan_term[0];
					$loan_data[] = $min_loan_term.' - '.$max_loan_term;
					
					//get borrowing_amount_range
					$min_borrowing_amount_range = explode('"minBorrowingAmount":',$loan_attr[1]);
					$min_borrowing_amount_range = explode(',',$min_borrowing_amount_range[1]);
					$min_borrowing_amount_range = $min_borrowing_amount_range[0];
					
					$max_borrowing_amount_range = explode('"maxBorrowingAmount":',$loan_attr[1]);
					$max_borrowing_amount_range = explode(',',$max_borrowing_amount_range[1]);
					$max_borrowing_amount_range = $max_borrowing_amount_range[0];
					$loan_data[] = $min_borrowing_amount_range.' - '.$max_borrowing_amount_range;
					
					//get refinance
					$refinance = explode('"isRefinanceAvailable":',$loan_attr[1]);
					$refinance = explode(',"',$refinance[1]);
					$loan_data[] = $refinance[0];
					
					//get line_of_credit
					$line_of_credit = explode('"lineOfCredit":',$loan_attr[1]);
					$line_of_credit = explode(',',$line_of_credit[1]);
					$loan_data[] = $line_of_credit[0];
					
					//get self_managed_super
					$self_managed_super = explode('"smsf":',$loan_attr[1]);
					$self_managed_super = explode(',',$self_managed_super[1]);
					$loan_data[] = $self_managed_super[0];
					
					//get interest_rate_structure
					$interest_rate_structure = explode('"rateType":["',$loan_attr[1]);
					$interest_rate_structure = explode('"',$interest_rate_structure[1]);
					$loan_data[] = strip_tags($interest_rate_structure[0]);
					
					//get interest_only
					$interest_only = explode('"interestOnly":',$loan_attr[1]);
					$interest_only = explode(',',$interest_only[1]);
					$loan_data[] = $interest_only[0];
					
					//get loan_allows_split_interest_rate
					$loan_allows_split_interest_rate = explode('"allowsSplitLoan":',$loan_attr[1]);
					$loan_allows_split_interest_rate = explode(',',$loan_allows_split_interest_rate[3]);
					$loan_data[] = $loan_allows_split_interest_rate	[0];
					
					//get principal_interest
					$principal_interest = explode('"principalAndInterest":',$loan_attr[1]);
					$principal_interest = explode(',',$principal_interest[1]);
					$loan_data[] = $principal_interest[0];
					
					//get states_applicable
					$states_applicable = explode('Applicable":"',$loan_attr[1]);
					
						foreach($states_applicable as $key => $state)
						{
							if($key > 0)
							{
								$state = explode('","',$state);
								$state = explode('"', $state[0]);
								$states[] = $state[0];
							}
						}
					
						$states_applicable=array_unique($states);
						
						$loan_data[] = implode('-',$states_applicable);
					
					//get redraw_facility
					$redraw_facility = explode('"redrawFacility":',$loan_attr[1]);
					$redraw_facility = explode(',',$redraw_facility[1]);
					$loan_data[] = $redraw_facility	[0];
					
					//get redraw_fee
					$redraw_fee = explode('"redrawFee":"',$loan_attr[1]);
					$redraw_fee = explode('"',$redraw_fee[1]);
					$loan_data[] = strip_tags($redraw_fee[0]);
					
					
					//get extra_repayments
					$extra_repayments = explode('"extraRepayments":"',$loan_attr[1]);
					$extra_repayments = explode('"',$extra_repayments[1]);
					$loan_data[] = strip_tags($extra_repayments[0]);
					
					//get weekly_repayments
					$weekly_repayments = explode('"hasWeeklyRepayments":',$loan_attr[1]);
					$weekly_repayments = explode(',',$weekly_repayments[1]);
					$loan_data[] = $weekly_repayments[0];
					
					//get fortnightly_repayments
					$fortnightly_repayments = explode('"hasFortnightlyRepayments":',$loan_attr[1]);
					$fortnightly_repayments = explode(',',$fortnightly_repayments[1]);
					$loan_data[] = $fortnightly_repayments[0];
					
					//get monthly_repayments
					$monthly_repayments = explode('"hasMonthlyRepayments":',$loan_attr[1]);
					$monthly_repayments = explode(',',$monthly_repayments[1]);
					$loan_data[] = $monthly_repayments[0];
					
					//ulr
					$loan_data[] = $url;
					
					//get borrowing_amount_range_min_max 
					// $borrowing_amount_range_min = explode('-',strip_tags($borrowing_amount_range[0]));
					// $borrowing_amount_range_max = $borrowing_amount_range_min[1];
					// $borrowing_amount_range_min = $borrowing_amount_range_min[0];

						// if((mb_ereg_match(".*k", $borrowing_amount_range_min) == true))
						// {
							// $borrowing_amount_range_min = str_replace('k','',$borrowing_amount_range_min);
							// $borrowing_amount_range_min = str_replace(' ','',$borrowing_amount_range_min);
							// $borrowing_amount_range_min = str_replace('.','',$borrowing_amount_range_min);
							// $borrowing_amount_range_min = str_replace('$','',$borrowing_amount_range_min).'000';
						// }
						// elseif((mb_ereg_match(".*m", $borrowing_amount_range_min) == true))
						// {
							// $borrowing_amount_range_min = str_replace('m','',$borrowing_amount_range_min);
							// $borrowing_amount_range_min = str_replace(' ','',$borrowing_amount_range_min);
							// $borrowing_amount_range_min = str_replace('.','',$borrowing_amount_range_min);
							// $borrowing_amount_range_min = str_replace('$','',$borrowing_amount_range_min).'000000';
						// }
					$loan_data[] =$min_borrowing_amount_range;
					
					//borrowing_amount_range_max
						// if((mb_ereg_match(".*m", $borrowing_amount_range_max) == true))
						// {
							// $borrowing_amount_range_max = str_replace('m','',$borrowing_amount_range_max);
							// $borrowing_amount_range_max = str_replace(' ','',$borrowing_amount_range_max);
							// $borrowing_amount_range_max = str_replace('.','',$borrowing_amount_range_max);
							// $borrowing_amount_range_max = str_replace('$','',$borrowing_amount_range_max).'000000';
						// }
						// elseif((mb_ereg_match(".*k", $borrowing_amount_range_max) == true))
						// {
							// $borrowing_amount_range_max = str_replace('k','',$borrowing_amount_range_max);
							// $borrowing_amount_range_max = str_replace(' ','',$borrowing_amount_range_max);
							// $borrowing_amount_range_max = str_replace('.','',$borrowing_amount_range_max);
							// $borrowing_amount_range_max = str_replace('$','',$borrowing_amount_range_max).'000';
						// }

					$loan_data[] = $max_borrowing_amount_range; 
					
					//get Loan Term_min
					$loan_data[] = $min_loan_term;
					//loan_term_max
					$loan_data[] = $max_loan_term;
					
					$url_exist = Loan_investmentHelpersLoan_investment::getLoanInfoUrl($url);
					var_dump(JRequest::getVar('action'));
					if(JRequest::getVar('action')=='add')
					{
						if(!$url_exist)
						{
							Loan_investmentHelpersLoan_investment::AddLoanInfo($loan_data);
						}
					}
					
					if(JRequest::getVar('action')=='update')
					{
						if($url_exist)
						{
							Loan_investmentHelpersLoan_investment::UpdateLoanInfo($loan_data);	
						}
					}
					
					
				sleep(5);	
				//end per page
			}//foreach final urls
			echo 'Done Scraping Page :'.$page;
	}
	
	public function updateProviderBank($final_urls,$page)
	{
		foreach($final_urls as $final_url)
			{
				//per pages
					$ch = curl_init();
					
					$url = 'http://www.ratecity.com.au'.$final_url;
					
					$proxy = 'proxy.crawlera.com:8010';
					$proxy_auth = '5df45f6c25474d5f831f8ae76e02a715';

					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_PROXY, $proxy);
					curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_auth);
					curl_setopt($ch, CURLOPT_HEADER, 1);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
					curl_setopt($ch, CURLOPT_TIMEOUT, 30);
					curl_setopt($ch, CURLOPT_CAINFO, JURI::base().'crawlera-ca.crt');
					
					$scrape = curl_exec($ch);
					curl_close($ch);
					
					$loan_info =  strip_tags($scrape,'<span>');
					
					
					//get provider
					$loan_provider = explode('"name":"',$loan_info);
					$loan_provider = explode('","',$loan_provider[1]);
					$loan_provider = str_replace("'","",$loan_provider[0]);
					
					//update provider
					Loan_investmentHelpersLoan_investment::UpdateProvider($loan_provider);
			sleep(3);			
			}//end foreach
			echo 'Done Scraping Page :'.$page;
	}

}
