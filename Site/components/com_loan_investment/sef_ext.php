<?php
/**
* Hot Property
*
* This extension will give the SEF Advance style URLs to Hot Property
* Place this file (sef_ext.php) in the main component directory:
*		/components/com_iproperty/
*
* For SEF Advance > v3.7
*
* @package Hot Property 1.0.1
* @copyright (C) 2004-2006 Lee Cher Yeong, 2010 Emir Sakic
* @url http://www.sakic.net/
**/




/**************/
define('INVESTMENT_HOME_LOANS', 'compare-investment-home-loans');
define('INVESTMENT_HOME_LOANS_TEST', 'compare-investment-home-loans-test');
define('INVESTMENT_DETAILS', 'investment-home-loans-details');
define('INVESTMENT_COMPARE', 'compare');


//index.php?option=com_iproperty&amp;view=search&amp;layout=generate_url&amp;tmpl=component_empty&amp;lang=en-GB&amp;full_keyword_withstate=Melbourne VIC (3004)&amp;search_keyword=Melbourne&amp;suburb_search_postcode=3004
class sef_loan_investment {

	/********************************************************
	* CREATE
	********************************************************/

	/**
	* Creates the SEF advance URL out of the Mambo request
	* Input: $string, string, The request URL (index.php?option=com_example&Itemid=$Itemid)
	* Output: $sefstring, string, SEF advance URL ($var1/$var2/)
	**/
	function create ($string) {
		$sefstring = '';

		$parts = parse_url(htmlspecialchars_decode($string));
		parse_str($parts['query'], $query);


		#view Home
		if ($query['view'] == 'investments' && $query['provider_id']=='') {
			$sefstring .= INVESTMENT_HOME_LOANS.'/';
				
		}
		
		if ($query['provider_id'] != '') {
				$temp = explode("&amp;provider_id=", $string);
				$temp = explode("&", $temp[1]);
				$provider_id= $temp[0];
				
			$provider_name = Loan_investmentHelpersLoan_investment::getProviderById($provider_id);
			
			$sefstring .= sefencode($provider_name).'/';
				
		}
		if ($query['view'] == 'investment') {
			$sefstring .= INVESTMENT_DETAILS.'/';
			if (stristr($string,'&amp;id=')) {
				$temp = explode("&amp;id=", $string);
				$temp = explode("&", $temp[1]);
				$id= $temp[0];
				$sefstring .=$id; 
			}
		}
		
		if ($query['view'] == 'investments' && $query['layout']=='compare') {

			$sefstring .= INVESTMENT_COMPARE.'/';


		}
		
		if (stristr($string,'&amp;layout=home')) {
			$sefstring .= INVESTMENT_HOME_LOANS_TEST.'/';
		}
		
		if (stristr($string,'&amp;start=')) {
				$temp = explode("&amp;start=", $string);
				$temp = explode("&", $temp[1]);
				$start = $temp[0];
				$sefstring .= 'start-'.$start; 
		}
		elseif(stristr($string,'&amp;limitstart=')) {
				$temp = explode("&amp;limitstart=", $string);
				$temp = explode("&", $temp[1]);
				$start = $temp[0];
				$sefstring .= 'limitstart-'.$start; 
		}
		
		return $sefstring;
	}

	/********************************************************
	* REVERT
	********************************************************/

	/**
	* Reverts to the Mambo query string out of the SEF advance URL
	* Input:
	*    $url_array, array, The SEF advance URL split in arrays (first custom virtual directory beginning at $pos+1)
	*    $pos, int, The position of the first virtual directory (component)
	* Output: $QUERY_STRING, string, Mambo query string (var1=$var1&var2=$var2)
	*    Note that this will be added to already defined first part (option=com_example&Itemid=$Itemid)
	**/
	function revert ($url_array, $pos) {

		$QUERY_STRING = '';

		if ( isset($url_array[$pos+2]) ) {
			switch($url_array[$pos+2]) {

				case INVESTMENT_HOME_LOANS:
					$_GET['view'] = $_REQUEST['view'] = 'investments';
					$QUERY_STRING .= "&view=investments";
				break;
				case INVESTMENT_COMPARE:
					$_GET['view'] = $_REQUEST['view'] = 'investments';
					$_GET['layout'] = $_REQUEST['layout'] = 'compare';
					$QUERY_STRING .= "&view=investments&layout=compare";
					if (count($url_array) >1) {
						$l = array(2,3,4,5,6);
						$ids = array();
						foreach($url_array as $ua => $a) {
							if (in_array($ua,$l)) {
								$ex = explode('-',$a);
								$ids[] = $ex[0];
							}
						}
						$QUERY_STRING .= '&ids='.implode(',',$ids);
						$_GET['ids'] = $_REQUEST['ids'] = implode(',',$ids);
					}
			
				break;
				case INVESTMENT_DETAILS:
					$_GET['view'] = $_REQUEST['view'] = 'investment';
					if (isset($url_array[$pos+3])) 
					{
						$id =  $url_array[$pos+3];
					}
					$_GET['id'] = $_REQUEST['id'] = $id;
					$QUERY_STRING .= "&view=investment&id=".$id;
				break;				
				case INVESTMENT_HOME_LOANS_TEST:
					$_GET['view'] = $_REQUEST['view'] = 'investments';
					$_GET['layout'] = $_REQUEST['layout'] = 'home';
					$QUERY_STRING .= "&view=investments&layout=home";
				break;	
				default:
					
						$provider_name =  str_replace('-',' ',$url_array[$pos+2]);
						$provider_id=Loan_investmentHelpersLoan_investment::getProviderId($provider_name);
						if($provider_id)
							{
								
								$_GET['view'] = $_REQUEST['view'] = 'investments';
								$_GET['provider_id'] = $_REQUEST['provider_id'] = $provider_id;
								$QUERY_STRING .= "&view=investments&provider_id=".$provider_id;
							}
					
				break;
			}
			
			
			if(isset($url_array[$pos+3]))
			{
				
				
				$start = explode('-',$url_array[$pos+3]);
				if($start[0]=='start')
				{
					$_GET['start'] = $_REQUEST['start'] =$start[1] ;
					$QUERY_STRING .= "&start=".$start[1];
				}
				elseif($start[0]=='limitstart')
				{
					$_GET['start'] = $_REQUEST['start'] =$start[1] ;
					$QUERY_STRING .= "&limitstart=".$start[1];
				}	
					
			}
		}

		return $QUERY_STRING;
	}

}
?>