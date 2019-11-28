<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Loan_investment
 * @author     gadiel_Rojo <gadsred@gmail.com>
 * @copyright  2016 gadiel_Rojo
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Class Loan_investmentController
 *
 * @since  1.6
 */
class Loan_investmentController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean $cachable  If true, the view output will be cached
	 * @param   mixed   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
        $app  = JFactory::getApplication();
        $view = $app->input->getCmd('view', 'investments');
		$app->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
	
	public function scrape()
	{  	ini_set('max_execution_time', 8000);
		$pages=JRequest::getVar('pages');
		$url_array='';
		$x=JRequest::getVar('start_page');
		for($x=1;$x<=$pages;$x++)
		{ 
			if($x > 1)
			{
				$page='&h_page='.$x;
			}
			else
			{
				$page='';
			}
			$ch = curl_init();

			$url = 'http://www.ratecity.com.au/home-loans/mortgage-rates?h_investment_purpose=true&h_per_page=100'.$page.'&h_showAll=true';
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

			$scraped_page =  strip_tags(curl_exec($ch),'<a>');
			curl_close($ch);
			$urls =  explode('<a href="',$scraped_page);
			
			//var_dump($urls);exit();
			
			//--get all page links--//
			$prev_url='';
			foreach($urls as $key => $purl)
			{
				 if($key >=182)
				 {
					$purl = explode('"',$purl);
					if($url[0]!=$prev_url)
					{
						$count_slash = count(explode('/',$purl[0]));
					
						if($count_slash == 4 && 
							preg_match('/http:/',$purl[0])!=1 && 
							preg_match('/www./',$purl[0])!=1 && 
							preg_match('(//)',$purl[0])!=1 && 
							preg_match('/https:/',$purl[0])!=1)
						{
							
							//$count_url++;
							$purl_ = explode('?',$purl[0]);
							
							$prev_url=$purl_[0];
							$url_array[]=$purl_[0];
						}
						
					}
					
					
				}
			
			}
			//--End get all page links--//
			$final_urls=array_unique($url_array);
			//var_dump($final_urls);exit();	
			Loan_investmentHelpersLoan_investment::scrapeByPage($final_urls,$x); 
		sleep(500);			
		}
		ini_set('max_execution_time',30);	
	}
	
	public function scrape_providers_bank()
	{  
		$pages=JRequest::getVar('pages');
		$url_array='';
		$x=JRequest::getVar('start_page');
		for($x=1;$x<=$pages;$x++)
		{ 
			if($x > 1)
			{
				$page='&h_page='.$x;
			}
			else
			{
				$page='';
			}
			$ch = curl_init();

			$url = 'http://www.ratecity.com.au/home-loans/mortgage-rates?h_investment_purpose=true&h_per_page=100&h_bank=true'.$page.'&h_showAll=true';
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

			$scraped_page =  strip_tags(curl_exec($ch),'<a>');
			curl_close($ch);
			$urls =  explode('<a href="',$scraped_page);
			
			//var_dump(count($urls )-56);exit();
			
			//--get all page links--//
			$prev_url='';
			foreach($urls as $key => $purl)
			{
				if($key >=57 && $key <=662 )
				{
					$purl = explode('"',$purl);
					if($url[0]!=$prev_url)
					{
						$count_slash = count(explode('/',$purl[0]));
					
						if($count_slash == 4)
						{
							
							//$count_url++;
							$prev_url=$purl[0];
							$url_array[]=$purl[0];
						}
						
					}
					
					
				}
			
			}
			//--End get all page links--//
			$final_urls=array_unique($url_array);
			//var_dump(count($final_urls));	
			Loan_investmentHelpersLoan_investment::updateProviderBank($final_urls,$x);
		sleep(30);			
		}
							
	}
	
	public function crawler()
	{
		include('crawler/vendor/autoload.php');
		


			function urlViaProxy($url){
			   
				$url = $url;
				
				$config = [
					'proxy' => [
						'http' => '5df45f6c25474d5f831f8ae76e02a715:@proxy.crawlera.com:8010'
						]
					];
				$client = new \Goutte\Client;
				$client->setClient(new \GuzzleHttp\Client($config));

				$crawler = $client->request('GET', $url);
				return $crawler;
				$status = $client->getResponse()->getStatus();

			}

			$URL = 'http://www.propertyinvestorsonly.com.au';
			$URL2 = 'http://www.ratecity.com.au/home-loans/mortgage-rates?h_investment_purpose=true&h_per_page=100&h_showAll=true';
			$crawler = urlViaProxy($URL2);

			//var_dump($crawler);

			//get specific part of the site using class/id tag
			$ip = $crawler->filter('#ip')->each(function ($node, $i) {
						return $node->html();
			  });
			var_dump($ip);


			// display the entire crawlered webstie
			echo $crawler->html();
	}

	public function seach_loans()
	{
		// Create a new query object.
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				$db->getState(
					'list.select', 'DISTINCT a.*'
				)
			);

		$query->from('`#__loan_investment_info` AS a');
		

		// Join over the created by field 'user_id'
		$query->join('LEFT', '#__users AS user_id ON user_id.id = a.user_id');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');
		
		if (!JFactory::getUser()->authorise('core.edit', 'com_loan_investment'))
		{
			$query->where('a.state = 1');
		}

		// Filter by search in title
		$search = $db->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.provider_name LIKE ' . $search . '  OR  a.loan_display_name LIKE ' . $search . '  OR  a.redraw_fee LIKE ' . $search . ' )');
			}
		}
		

		// Filtering maximum_lvr
		$filter_maximum_lvr = '';//$this->state->get("filter.maximum_lvr");
		if ($filter_maximum_lvr != '') {
			$query->where("a.maximum_lvr = '".$db->escape($filter_maximum_lvr)."'");
		}

		// Filtering loan_term
		$filter_loan_term = '';//$this->state->get("filter.loan_term");
		if ($filter_loan_term != '') {
			$query->where("a.loan_term = '".$db->escape($filter_loan_term)."'");
		}

		// Filtering refinance
		$filter_refinance = '';//$this->state->get("filter.refinance");
		if ($filter_refinance != '') {
			$query->where("a.refinance = '".$db->escape($filter_refinance)."'");
		}

		// Filtering line_of_credit
		$filter_line_of_credit = '';//$this->state->get("filter.line_of_credit");
		if ($filter_line_of_credit != '') {
			$query->where("a.line_of_credit = '".$db->escape($filter_line_of_credit)."'");
		}

		// Filtering self_managed_super
		$filter_self_managed_super = '';//$this->state->get("filter.self_managed_super");
		if ($filter_self_managed_super != '') {
			$query->where("a.self_managed_super = '".$db->escape($filter_self_managed_super)."'");
		}

		// Filtering interest_rate_structure
		$filter_interest_rate_structure = '';//$this->state->get("filter.interest_rate_structure");
		if ($filter_interest_rate_structure != '') {
			$query->where("a.interest_rate_structure = '".$db->escape($filter_interest_rate_structure)."'");
		}

		// Filtering interest_only
		$filter_interest_only = JRequest::getVar('interest_only');//$this->state->get("filter.interest_only");
		if ($filter_interest_only != '') {
			$query->where("a.interest_only = '".$db->escape($filter_interest_only)."'");
		}

		// Filtering loan_allows_split_interest_rate
		$filter_loan_allows_split_interest_rate = JRequest::getVar('loan_allows_split_interest_rate'); //$this->state->get("filter.loan_allows_split_interest_rate");
		if ($filter_loan_allows_split_interest_rate != '') {
			$query->where("a.loan_allows_split_interest_rate = '".$db->escape($filter_loan_allows_split_interest_rate)."'");
		}

		// Filtering principal_interest
		$filter_principal_interest = JRequest::getVar('principal_interest'); //$this->state->get("filter.principal_interest");
		if ($filter_principal_interest != '') {
			$query->where("a.principal_interest = '".$db->escape($filter_principal_interest)."'");
		}

		// Filtering states_applicable
		$filter_states_applicable = JRequest::getVar("states_applicable");

		if ($filter_states_applicable != '') {
			$au_states = explode(',',$filter_states_applicable);
			
			$x=0;
			if($au_states[1]!='' || $au_states[0]!='' )
			{
				$where_state .="(";
				foreach($au_states as $au_state)
				{
					if($au_state!='')
					{
						$x++;
							if($x>1)
							{
								$where_state .=" or a.states_applicable like '%".$au_state."%'";
							}
							else
							{
								$where_state .="a.states_applicable like '%".$au_state."%'";
							}
					}

				}
				$where_state .=")";
				$query->where(" $where_state ");
			}
			
		}

		// Filtering redraw_facility
		$filter_redraw_facility = JRequest::getVar("redraw_facility"); //$this->state->get("filter.redraw_facility");
		if ($filter_redraw_facility != '') {
			$query->where("a.redraw_facility = '".$db->escape($filter_redraw_facility)."'");
		}

		// Filtering extra_repayments
		$filter_extra_repayments = JRequest::getVar("extra_repayments"); //$this->state->get("filter.extra_repayments");
		if ($filter_extra_repayments != '') {
			$extra_repayments = explode(',',$filter_extra_repayments);
			
			$x=0;
			if($extra_repayments[1]!='' || $extra_repayments[0]!='' )
			{
				$where_extra.="(";
				foreach($extra_repayments as $extra_repayment)
				{
					if($au_state!='')
					{
						$x++;
							if($x>1)
							{
								$where_extra .=" or a.extra_repayments like '%".$extra_repayment."%'";
							}
							else
							{
								$where_extra .="a.extra_repayments like '%".$extra_repayment."%'";
							}
					}

				}
				$where_extra .=")";
				$query->where(" $where_extra ");
			}
			
		}

		// Filtering weekly_repayments
		$filter_weekly_repayments = '';//$this->state->get("filter.weekly_repayments");
		if ($filter_weekly_repayments != '') {
			$query->where("a.weekly_repayments = '".$db->escape($filter_weekly_repayments)."'");
		}

		// Filtering fortnightly_repayments
		$filter_fortnightly_repayments ='';//$this->state->get("filter.fortnightly_repayments");
		if ($filter_fortnightly_repayments != '') {
			$query->where("a.fortnightly_repayments = '".$db->escape($filter_fortnightly_repayments)."'");
		}

		// Filtering monthly_repayments
		$filter_monthly_repayments = '';//$this->state->get("filter.monthly_repayments");
		if ($filter_monthly_repayments != '') {
			$query->where("a.monthly_repayments = '".$db->escape($filter_monthly_repayments)."'");
		}

		// Add the list ordering clause.
		$orderCol  = '';//$this->state->get('list.ordering');
		$orderDirn = '';//$this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
		
		echo $query;exit();
	}
}
