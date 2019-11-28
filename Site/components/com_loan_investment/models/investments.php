<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Loan_investment
 * @author     gadiel_Rojo <gadsred@gmail.com>
 * @copyright  2016 gadiel_Rojo
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Loan_investment records.
 *
 * @since  1.6
 */
class Loan_investmentModelInvestments extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'user_id', 'a.user_id',
				'provider_name', 'a.provider_name',
				'loan_display_name', 'a.loan_display_name',
				'maximum_lvr', 'a.maximum_lvr',
				'loan_term', 'a.loan_term',
				'borrowing_amount_range', 'a.borrowing_amount_range',
				'refinance', 'a.refinance',
				'line_of_credit', 'a.line_of_credit',
				'self_managed_super', 'a.self_managed_super',
				'interest_rate_structure', 'a.interest_rate_structure',
				'interest_only', 'a.interest_only',
				'loan_allows_split_interest_rate', 'a.loan_allows_split_interest_rate',
				'principal_interest', 'a.principal_interest',
				'states_applicable', 'a.states_applicable',
				'redraw_facility', 'a.redraw_facility',
				'redraw_fee', 'a.redraw_fee',
				'extra_repayments', 'a.extra_repayments',
				'weekly_repayments', 'a.weekly_repayments',
				'fortnightly_repayments', 'a.fortnightly_repayments',
				'monthly_repayments', 'a.monthly_repayments',
				'date_created', 'a.date_created',
				'date_modified', 'a.date_modified',
				'created_by', 'a.created_by',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app  = JFactory::getApplication();
		$list = $app->getUserState($this->context . '.list');

		$ordering  = isset($list['filter_order'])     ? $list['filter_order']     : null;
		$direction = isset($list['filter_order_Dir']) ? $list['filter_order_Dir'] : null;


		if ($_REQUEST['length']) $_REQUEST['limit'] = $_REQUEST['length'];

		$list['limit']     = ($_REQUEST['limit']) ? (int) $_REQUEST['limit'] : (int) JFactory::getConfig()->get('list_limit', 10);
		$list['start']     = $app->input->getInt('start', 0);
		$list['ordering']  = $ordering;
		$list['direction'] = $direction;



		$app->setUserState($this->context . '.list', $list);
		$app->input->set('list', null);

		// List state information.
		parent::populateState($ordering, $direction);

        $app = JFactory::getApplication();

        $ordering  = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);
        $direction = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $ordering);

        $this->setState('list.ordering', $ordering);
        $this->setState('list.direction', $direction);

        $start = $app->getUserStateFromRequest($this->context . '.start', 'start', 0, 'int');
        $limit = $app->getUserStateFromRequest($this->context . '.limit', 'limit', 0, 'int');

        if ($limit == 0)
        {
            $limit = $app->get('list_limit', 0);
        }

        $this->setState('list.limit', $limit);
        $this->setState('list.start', $start);


        
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		
		// Select the required fields from the table.
		$query
			->select(
				$this->getState(
					'list.select', 'DISTINCT a.*,p.id as pid, p.provider_logo, p.website, p.provider_name'
				)
			);

		$query->from('`#__loan_investment_info` AS a');
		

		// Join over the created by field 'user_id'
		//$query->join('LEFT', '#__users AS user_id ON user_id.id = a.user_id');

		// Join over the created by field 'created_by'
		//$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');
		
		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__loan_investment_providers as p ON p.id = a.user_id');

		if (!JFactory::getUser()->authorise('core.edit', 'com_loan_investment'))
		{
			$query->where('a.state = 1');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

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

		// Filtering Provider from footer
		$provider_id= JRequest::getVar('provider_id');
		if ($provider_id) {
			$query->where("a.user_id = '".$db->escape($provider_id)."'");
		}
		else
		{
			
		
			// Filtering maximum_lvr
			$filter_maximum_lvr = $this->state->get("filter.maximum_lvr");
			if ($filter_maximum_lvr != '') {
				$query->where("a.maximum_lvr = '".$db->escape($filter_maximum_lvr)."'");
			}

			// Filtering loan_term
			$filter_loan_term = $this->state->get("filter.loan_term");
			if ($filter_loan_term != '') {
				$query->where("a.loan_term = '".$db->escape($filter_loan_term)."'");
			}

			//filter loan type
			$filter_loan_type = JRequest::getVar("loan_type");
			$loan_type = array(
				1 => 'a.interest_only',
				2 => 'principal_interest'
			);
			if ($filter_loan_type != '' && isset($loan_type[$filter_loan_type])) {
				
				$query->where($loan_type[$filter_loan_type]." = 'true'");
			}


			// Filtering refinance
			$filter_refinance = $this->state->get("filter.refinance");
			if ($filter_refinance != '') {
				$query->where("a.refinance = '".$db->escape($filter_refinance)."'");
			}

			// Filtering line_of_credit
			$filter_line_of_credit = $this->state->get("filter.line_of_credit");
			if ($filter_line_of_credit != '') {
				$query->where("a.line_of_credit = '".$db->escape($filter_line_of_credit)."'");
			}

			// Filtering self_managed_super
			$filter_self_managed_super = $this->state->get("filter.self_managed_super");
			if ($filter_self_managed_super != '') {
				$query->where("a.self_managed_super = '".$db->escape($filter_self_managed_super)."'");
			}

			// Filtering interest_rate_structure
			$filter_interest_rate_structure = $this->state->get("filter.interest_rate_structure");
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
			
			// Filtering located in au_state
			$filter_au_state= JRequest::getVar('au_state');
			if (is_array($filter_au_state)) {
				// do something here.
			} else {
				if ($filter_au_state != '') {
					$query->where("a.states_applicable Like '%".$db->escape($filter_au_state)."%'");
				}
			}

			// Filtering states_applicable
			$filter_states_applicable = JRequest::getVar("states_applicable");

			if ($filter_states_applicable != '') {			

				$au_states = (is_array($filter_states_applicable)) ? $filter_states_applicable : explode(',',$filter_states_applicable);
				
				$x=0;
				if($au_states[1]!='' || $au_states[0]!='' )	{
					$where_state .="(";
					foreach($au_states as $au_state) {
						if($au_state!='') {
							$x++;
							$where_state .=" find_in_set('".$au_state."',replace(a.states_applicable,'-',',')) and ";
								
						}
					}
					$where_state .=" 1=1)";
					if($where_state!='()')	{
						$query->where(" $where_state ");
					}
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
				$extra_repayments = (is_array($filter_extra_repayments)) ? $filter_extra_repayments : explode(',',$filter_extra_repayments);
				
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
					if($where_extra!='()')
					{
						$query->where(" $where_extra ");
					}
				}
				
			}

			// Filtering weekly_repayments
			$filter_weekly_repayments = $this->state->get("filter.weekly_repayments");
			if ($filter_weekly_repayments != '') {
				$query->where("a.weekly_repayments = '".$db->escape($filter_weekly_repayments)."'");
			}

			// Filtering fortnightly_repayments
			$filter_fortnightly_repayments = $this->state->get("filter.fortnightly_repayments");
			if ($filter_fortnightly_repayments != '') {
				$query->where("a.fortnightly_repayments = '".$db->escape($filter_fortnightly_repayments)."'");
			}

			// Filtering monthly_repayments
			$filter_monthly_repayments = $this->state->get("filter.monthly_repayments");
			if ($filter_monthly_repayments != '') {
				$query->where("a.monthly_repayments = '".$db->escape($filter_monthly_repayments)."'");
			}

			// advertise rate min max
			$min_advertised_rate = JRequest::getVar('min_advertised_rate', 0, '', 'int');
			$max_advertised_rate = JRequest::getVar('max_advertised_rate', 0, '', 'int');

			if ($max_advertised_rate == '' and $min_advertised_rate != '')
				$query->where("a.advertised_rate >= ".$db->escape($min_advertised_rate));
			if ($max_advertised_rate != '' and $min_advertised_rate == '')
				$query->where("a.advertised_rate <= ".$db->escape($max_advertised_rate));
			if ($max_advertised_rate != '' and $min_advertised_rate != '')
				$query->where("a.advertised_rate BETWEEN ".$db->escape($min_advertised_rate)." AND ".$db->escape($max_advertised_rate)."");
			

			// advertise rate min max
			$min_comparison_rate = round(JRequest::getVar('min_comparison_rate'),2);
			$max_comparison_rate = round(JRequest::getVar('max_comparison_rate'),2);

			if ($max_comparison_rate == '' and $min_comparison_rate != '')
				$query->where("(round(a.comparison_rate,2) >= ".$db->escape($min_comparison_rate) .")");
			if ($max_comparison_rate != '' and $min_comparison_rate == '')
				$query->where("(round(a.comparison_rate,2) <= ".$db->escape($max_comparison_rate) .")");
			if ($max_comparison_rate != '' and $min_comparison_rate != '')
				$query->where("(round(a.comparison_rate,2) BETWEEN ".$db->escape($min_comparison_rate)." AND ".$db->escape($max_comparison_rate).")");
			
		}//End else Filterin provider_id footer	
		
		$query->where('a.loan_display_name !=""');		

		/* table tag search */
		$this->getTagSearch($query,$db);

		//var_dump(array($min_advertised_rate,$max_advertised_rate));
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		/* table column ordering */
		$this->getTableOrder($query,$db);
			//var_dump($_GET);


		//echo $query;
		return $query;
	}


	public function getTagSearch($query,$db) {
		$_tags = explode(',',JRequest::getVar('tags'));
		$_where = array();
		//var_dump($_GET);

		$_columns = $_REQUEST['columns'];

		if (is_array($_tags) && count($_tags) >0) {
			foreach($_tags as $tag) {
				if (strlen($tag) > 0) {
				$temp = array('p.provider_name');
				foreach($_columns as $col => $o) {
					if ($o['searchable'] == true && strlen($o['name']) > 1) {
						
						$temp[] = "a.".$o['name'];
					}
				}
				$_where[] = '(CONCAT_WS('.implode(',',$temp) .") like '%".$tag."%')";
				}
			}
			if (count($_where) >0) 
				$query->where(implode(' and ',$_where));
			
		}

		//remove all investment loans without a company attached
		$query->where("p.id is not null");

		// filter with borrow amount range	
		$_borrow = intval(JRequest::getVar('borrow_amount'));
		if ($_borrow > 0) {
			$query->where("(a.borrowing_amount_range_min <= ".$_borrow." and a.borrowing_amount_range_max >= ".$_borrow.")");
		}

		// filter with paid back range
		$_paid_back = intval(JRequest::getVar('paid_back'));
		if ($_paid_back > 0) {
			$query->where("(a.loan_term_min <= ".$_paid_back." and a.loan_term_max >= ".$_paid_back.")");
		}


		//company name filter
		$_company = JRequest::getVar('company_name');
		if (strlen($_company) > 2) {
			$query->where("p.provider_name like '%".$_company."%'");
		} 

		//banks filter
		$_banks = JRequest::getVar('banks');		
		if (isset($_banks) && is_array($_banks)) {
			$query->where("p.provider_type in (".implode(',',$_banks).")");
		}

		//compage page special filter
		$_ids = JRequest::getVar('ids');
		
		if (strlen($_ids) > 1) {
			$_ids = explode(',',$_ids);
			$_ids =array_filter($_ids, function($value) { return $value !== ''; });
			
			if (is_array($_ids) && count($_ids)> 0) {
				$query->where("(a.id in (".implode(',',$_ids).") or a.is_sponsor = 1)");
			}
		}
		return $query;

	}

	public function getTableOrder($query,$db) {
		//default order special to sponsored loans products
		$query->order('(a.is_sponsor*1) desc ');	


		$_columns = $_REQUEST['columns'];
		$_order = $_REQUEST['order'];
		if(is_array($_order)) {
			foreach($_order as $or => $o) {
				if ($_columns[$o['column']]['orderable'] == true) {
					$query->order($db->escape($_columns[$o['column']]['name'] .' '. $o['dir']));				
				}
			}
		}
		return $query;

	}
	public function getTotals() {
		$db    = $this->getDbo();

		$query = 'select count(*) as CNT from #__loan_investment_info a
			LEFT JOIN #__loan_investment_providers as p ON p.id = a.user_id 
 			WHERE p.id is not null';
			
		
		$db->setQuery($query);
		$count = $db->loadResult();    
	
		return $count;

		
	}
	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as $item)
		{

					$item->maximum_lvr = JText::_('' . strtoupper($item->maximum_lvr));
					$item->loan_term = JText::_('' . strtoupper($item->loan_term));
					$item->borrowing_amount_range = JText::_('' . strtoupper($item->borrowing_amount_range));
					$item->refinance = JText::_('' . strtoupper($item->refinance));
					$item->line_of_credit = JText::_('' . strtoupper($item->line_of_credit));
					$item->self_managed_super = JText::_('' . strtoupper($item->self_managed_super));
					$item->interest_rate_structure = JText::_('' . strtoupper($item->interest_rate_structure));
					$item->interest_only = JText::_('' . strtoupper($item->interest_only));
					$item->loan_allows_split_interest_rate = JText::_('' . strtoupper($item->loan_allows_split_interest_rate));
					$item->principal_interest = JText::_('' . strtoupper($item->principal_interest));

				// Get the title of every option selected.
				$options      = explode(',',$item->states_applicable);
				$options_text = array();

				/*foreach ((array) $options as $option)
				{
					$options_text[] = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_STATES_APPLICABLE_OPTION_' . strtoupper($option));
				}

					$item->states_applicable = !empty($options_text) ? implode(',', $options_text) : $item->states_applicable;
					$item->redraw_facility = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_REDRAW_FACILITY_OPTION_' . strtoupper($item->redraw_facility));
					$item->extra_repayments = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_EXTRA_REPAYMENTS_OPTION_' . strtoupper($item->extra_repayments));
					$item->weekly_repayments = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_WEEKLY_REPAYMENTS_OPTION_' . strtoupper($item->weekly_repayments));
					$item->fortnightly_repayments = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_FORTNIGHTLY_REPAYMENTS_OPTION_' . strtoupper($item->fortnightly_repayments));
					$item->monthly_repayments = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_MONTHLY_REPAYMENTS_OPTION_' . strtoupper($item->monthly_repayments));
				*/
		}

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = JFactory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(JText::_("COM_LOAN_INVESTMENT_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);
		return (date_create($date)) ? JFactory::getDate($date)->format("Y-m-d") : null;
	}
}
