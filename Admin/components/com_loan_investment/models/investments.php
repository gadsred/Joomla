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
				'id', 'a.`id`',
				'ordering', 'a.`ordering`',
				'state', 'a.`state`',
				'user_id', 'a.`user_id`',
				'provider_name', 'a.`provider_name`',
				'loan_display_name', 'a.`loan_display_name`',
				'maximum_lvr', 'a.`maximum_lvr`',
				'loan_term', 'a.`loan_term`',
				'borrowing_amount_range', 'a.`borrowing_amount_range`',
				'refinance', 'a.`refinance`',
				'line_of_credit', 'a.`line_of_credit`',
				'self_managed_super', 'a.`self_managed_super`',
				'interest_rate_structure', 'a.`interest_rate_structure`',
				'interest_only', 'a.`interest_only`',
				'loan_allows_split_interest_rate', 'a.`loan_allows_split_interest_rate`',
				'principal_interest', 'a.`principal_interest`',
				'states_applicable', 'a.`states_applicable`',
				'redraw_facility', 'a.`redraw_facility`',
				'redraw_fee', 'a.`redraw_fee`',
				'extra_repayments', 'a.`extra_repayments`',
				'weekly_repayments', 'a.`weekly_repayments`',
				'fortnightly_repayments', 'a.`fortnightly_repayments`',
				'monthly_repayments', 'a.`monthly_repayments`',
				'date_created', 'a.`date_created`',
				'date_modified', 'a.`date_modified`',
				'created_by', 'a.`created_by`',
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
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
		// Filtering maximum_lvr
		$this->setState('filter.maximum_lvr', $app->getUserStateFromRequest($this->context.'.filter.maximum_lvr', 'filter_maximum_lvr', '', 'string'));

		// Filtering loan_term
		$this->setState('filter.loan_term', $app->getUserStateFromRequest($this->context.'.filter.loan_term', 'filter_loan_term', '', 'string'));

		// Filtering refinance
		$this->setState('filter.refinance', $app->getUserStateFromRequest($this->context.'.filter.refinance', 'filter_refinance', '', 'string'));

		// Filtering line_of_credit
		$this->setState('filter.line_of_credit', $app->getUserStateFromRequest($this->context.'.filter.line_of_credit', 'filter_line_of_credit', '', 'string'));

		// Filtering self_managed_super
		$this->setState('filter.self_managed_super', $app->getUserStateFromRequest($this->context.'.filter.self_managed_super', 'filter_self_managed_super', '', 'string'));

		// Filtering interest_rate_structure
		$this->setState('filter.interest_rate_structure', $app->getUserStateFromRequest($this->context.'.filter.interest_rate_structure', 'filter_interest_rate_structure', '', 'string'));

		// Filtering interest_only
		$this->setState('filter.interest_only', $app->getUserStateFromRequest($this->context.'.filter.interest_only', 'filter_interest_only', '', 'string'));

		// Filtering loan_allows_split_interest_rate
		$this->setState('filter.loan_allows_split_interest_rate', $app->getUserStateFromRequest($this->context.'.filter.loan_allows_split_interest_rate', 'filter_loan_allows_split_interest_rate', '', 'string'));

		// Filtering principal_interest
		$this->setState('filter.principal_interest', $app->getUserStateFromRequest($this->context.'.filter.principal_interest', 'filter_principal_interest', '', 'string'));

		// Filtering states_applicable
		$this->setState('filter.states_applicable', $app->getUserStateFromRequest($this->context.'.filter.states_applicable', 'filter_states_applicable', '', 'string'));

		// Filtering redraw_facility
		$this->setState('filter.redraw_facility', $app->getUserStateFromRequest($this->context.'.filter.redraw_facility', 'filter_redraw_facility', '', 'string'));

		// Filtering extra_repayments
		$this->setState('filter.extra_repayments', $app->getUserStateFromRequest($this->context.'.filter.extra_repayments', 'filter_extra_repayments', '', 'string'));

		// Filtering weekly_repayments
		$this->setState('filter.weekly_repayments', $app->getUserStateFromRequest($this->context.'.filter.weekly_repayments', 'filter_weekly_repayments', '', 'string'));

		// Filtering fortnightly_repayments
		$this->setState('filter.fortnightly_repayments', $app->getUserStateFromRequest($this->context.'.filter.fortnightly_repayments', 'filter_fortnightly_repayments', '', 'string'));

		// Filtering monthly_repayments
		$this->setState('filter.monthly_repayments', $app->getUserStateFromRequest($this->context.'.filter.monthly_repayments', 'filter_monthly_repayments', '', 'string'));


		// Load the parameters.
		$params = JComponentHelper::getParams('com_loan_investment');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.provider_name', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
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
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);
		$query->from('`#__loan_investment_info` AS a');


		// Join over the user field 'user_id'
		$query->select('`user_id`.name AS `user_id`');
		$query->join('LEFT', '#__users AS `user_id` ON `user_id`.id = a.`user_id`');

		// Join over the user field 'created_by'
		$query->select('`created_by`.name AS `created_by`');
		$query->join('LEFT', '#__users AS `created_by` ON `created_by`.id = a.`created_by`');

		// Filter by published state
		$published = $this->getState('filter.state');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
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
				$query->where('( a.provider_name LIKE ' . $search . '  OR  a.loan_display_name LIKE ' . $search . '  OR  a.maximum_lvr LIKE ' . $search . '  OR  a.loan_term LIKE ' . $search . '  OR  a.borrowing_amount_range LIKE ' . $search . '  OR  a.refinance LIKE ' . $search . '  OR  a.line_of_credit LIKE ' . $search . '  OR  a.self_managed_super LIKE ' . $search . '  OR  a.interest_rate_structure LIKE ' . $search . '  OR  a.interest_only LIKE ' . $search . '  OR  a.loan_allows_split_interest_rate LIKE ' . $search . '  OR  a.principal_interest LIKE ' . $search . '  OR  a.states_applicable LIKE ' . $search . '  OR  a.redraw_facility LIKE ' . $search . '  OR  a.redraw_fee LIKE ' . $search . '  OR  a.extra_repayments LIKE ' . $search . '  OR  a.weekly_repayments LIKE ' . $search . '  OR  a.fortnightly_repayments LIKE ' . $search . '  OR  a.monthly_repayments LIKE ' . $search . ' )');
			}
		}


		//Filtering maximum_lvr
		$filter_maximum_lvr = $this->state->get("filter.maximum_lvr");
		if ($filter_maximum_lvr)
		{
			$query->where("a.`maximum_lvr` = '".$db->escape($filter_maximum_lvr)."'");
		}

		//Filtering loan_term
		$filter_loan_term = $this->state->get("filter.loan_term");
		if ($filter_loan_term)
		{
			$query->where("a.`loan_term` = '".$db->escape($filter_loan_term)."'");
		}

		//Filtering refinance
		$filter_refinance = $this->state->get("filter.refinance");
		if ($filter_refinance)
		{
			$query->where("a.`refinance` = '".$db->escape($filter_refinance)."'");
		}

		//Filtering line_of_credit
		$filter_line_of_credit = $this->state->get("filter.line_of_credit");
		if ($filter_line_of_credit)
		{
			$query->where("a.`line_of_credit` = '".$db->escape($filter_line_of_credit)."'");
		}

		//Filtering self_managed_super
		$filter_self_managed_super = $this->state->get("filter.self_managed_super");
		if ($filter_self_managed_super)
		{
			$query->where("a.`self_managed_super` = '".$db->escape($filter_self_managed_super)."'");
		}

		//Filtering interest_rate_structure
		$filter_interest_rate_structure = $this->state->get("filter.interest_rate_structure");
		if ($filter_interest_rate_structure)
		{
			$query->where("a.`interest_rate_structure` = '".$db->escape($filter_interest_rate_structure)."'");
		}

		//Filtering interest_only
		$filter_interest_only = $this->state->get("filter.interest_only");
		if ($filter_interest_only)
		{
			$query->where("a.`interest_only` = '".$db->escape($filter_interest_only)."'");
		}

		//Filtering loan_allows_split_interest_rate
		$filter_loan_allows_split_interest_rate = $this->state->get("filter.loan_allows_split_interest_rate");
		if ($filter_loan_allows_split_interest_rate)
		{
			$query->where("a.`loan_allows_split_interest_rate` = '".$db->escape($filter_loan_allows_split_interest_rate)."'");
		}

		//Filtering principal_interest
		$filter_principal_interest = $this->state->get("filter.principal_interest");
		if ($filter_principal_interest)
		{
			$query->where("a.`principal_interest` = '".$db->escape($filter_principal_interest)."'");
		}

		//Filtering states_applicable
		$filter_states_applicable = $this->state->get("filter.states_applicable");
		if ($filter_states_applicable)
		{
			$query->where("a.`states_applicable` = '".$db->escape($filter_states_applicable)."'");
		}

		//Filtering redraw_facility
		$filter_redraw_facility = $this->state->get("filter.redraw_facility");
		if ($filter_redraw_facility)
		{
			$query->where("a.`redraw_facility` = '".$db->escape($filter_redraw_facility)."'");
		}

		//Filtering extra_repayments
		$filter_extra_repayments = $this->state->get("filter.extra_repayments");
		if ($filter_extra_repayments)
		{
			$query->where("a.`extra_repayments` = '".$db->escape($filter_extra_repayments)."'");
		}

		//Filtering weekly_repayments
		$filter_weekly_repayments = $this->state->get("filter.weekly_repayments");
		if ($filter_weekly_repayments)
		{
			$query->where("a.`weekly_repayments` = '".$db->escape($filter_weekly_repayments)."'");
		}

		//Filtering fortnightly_repayments
		$filter_fortnightly_repayments = $this->state->get("filter.fortnightly_repayments");
		if ($filter_fortnightly_repayments)
		{
			$query->where("a.`fortnightly_repayments` = '".$db->escape($filter_fortnightly_repayments)."'");
		}

		//Filtering monthly_repayments
		$filter_monthly_repayments = $this->state->get("filter.monthly_repayments");
		if ($filter_monthly_repayments)
		{
			$query->where("a.`monthly_repayments` = '".$db->escape($filter_monthly_repayments)."'");
		}
		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		foreach ($items as $oneItem) {
					$oneItem->maximum_lvr = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_MAXIMUM_LVR_OPTION_' . strtoupper($oneItem->maximum_lvr));
					$oneItem->loan_term = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_LOAN_TERM_OPTION_' . strtoupper($oneItem->loan_term));
					$oneItem->borrowing_amount_range = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_BORROWING_AMOUNT_RANGE_OPTION_' . strtoupper($oneItem->borrowing_amount_range));
					$oneItem->refinance = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_REFINANCE_OPTION_' . strtoupper($oneItem->refinance));
					$oneItem->line_of_credit = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_LINE_OF_CREDIT_OPTION_' . strtoupper($oneItem->line_of_credit));
					$oneItem->self_managed_super = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_SELF_MANAGED_SUPER_OPTION_' . strtoupper($oneItem->self_managed_super));
					$oneItem->interest_rate_structure = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_INTEREST_RATE_STRUCTURE_OPTION_' . strtoupper($oneItem->interest_rate_structure));
					$oneItem->interest_only = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_INTEREST_ONLY_OPTION_' . strtoupper($oneItem->interest_only));
					$oneItem->loan_allows_split_interest_rate = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_LOAN_ALLOWS_SPLIT_INTEREST_RATE_OPTION_' . strtoupper($oneItem->loan_allows_split_interest_rate));
					$oneItem->principal_interest = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_PRINCIPAL_INTEREST_OPTION_' . strtoupper($oneItem->principal_interest));

				// Get the title of every option selected.

				$options = explode(',', $oneItem->states_applicable);

				$options_text = array();

				foreach ((array) $options as $option)
				{
					$options_text[] = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_STATES_APPLICABLE_OPTION_' . strtoupper($option));
				}

				$oneItem->states_applicable = !empty($options_text) ? implode(',', $options_text) : $oneItem->states_applicable;
					$oneItem->redraw_facility = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_REDRAW_FACILITY_OPTION_' . strtoupper($oneItem->redraw_facility));
					$oneItem->extra_repayments = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_EXTRA_REPAYMENTS_OPTION_' . strtoupper($oneItem->extra_repayments));
					$oneItem->weekly_repayments = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_WEEKLY_REPAYMENTS_OPTION_' . strtoupper($oneItem->weekly_repayments));
					$oneItem->fortnightly_repayments = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_FORTNIGHTLY_REPAYMENTS_OPTION_' . strtoupper($oneItem->fortnightly_repayments));
					$oneItem->monthly_repayments = JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_MONTHLY_REPAYMENTS_OPTION_' . strtoupper($oneItem->monthly_repayments));
		}
		return $items;
	}
}
