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

jimport('joomla.application.component.view');

/**
 * View class for a list of Loan_investment.
 *
 * @since  1.6
 */
class Loan_investmentViewInvestments extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		Loan_investmentHelpersLoan_investment::addSubmenu('investments');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = Loan_investmentHelpersLoan_investment::getActions();

		JToolBarHelper::title(JText::_('COM_LOAN_INVESTMENT_TITLE_INVESTMENTS'), 'investments.png');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/investment';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('investment.add', 'JTOOLBAR_NEW');
				JToolbarHelper::custom('investments.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('investment.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('investments.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('investments.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'investments.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('investments.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('investments.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'investments.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('investments.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_loan_investment');
		}

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_loan_investment&view=investments');

		$this->extra_sidebar = '';
		JHtmlSidebar::addFilter(

			JText::_('JOPTION_SELECT_PUBLISHED'),

			'filter_published',

			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

		);
		//Filter for the field maximum_lvr
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Maximum Lvr');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "0.80";
		$options[0]->text = "80%";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_maximum_lvr',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.maximum_lvr'), true)
		);

		//Filter for the field loan_term
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Loan Term');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "1 - 30 years";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_loan_term',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.loan_term'), true)
		);

		//Filter for the field refinance
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Refinance');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "YES";
		$options[1] = new stdClass();
		$options[1]->value = "0";
		$options[1]->text = "NO";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_refinance',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.refinance'), true)
		);

		//Filter for the field line_of_credit
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Line Of Credit');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "YES";
		$options[1] = new stdClass();
		$options[1]->value = "0";
		$options[1]->text = "NO";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_line_of_credit',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.line_of_credit'), true)
		);

		//Filter for the field self_managed_super
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Self-managed Super');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "YES";
		$options[1] = new stdClass();
		$options[1]->value = "0";
		$options[1]->text = "NO";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_self_managed_super',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.self_managed_super'), true)
		);

		//Filter for the field interest_rate_structure
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Interest Rate Structure');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "Variable";
		$options[0]->text = "Variable";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_interest_rate_structure',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.interest_rate_structure'), true)
		);

		//Filter for the field interest_only
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Interest Only');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "YES";
		$options[1] = new stdClass();
		$options[1]->value = "0";
		$options[1]->text = "NO";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_interest_only',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.interest_only'), true)
		);

		//Filter for the field loan_allows_split_interest_rate
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Loan allows split interest rate');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "YES";
		$options[1] = new stdClass();
		$options[1]->value = "0";
		$options[1]->text = "NO";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_loan_allows_split_interest_rate',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.loan_allows_split_interest_rate'), true)
		);

		//Filter for the field principal_interest
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Principal and interest');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "YES";
		$options[1] = new stdClass();
		$options[1]->value = "0";
		$options[1]->text = "NO";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_principal_interest',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.principal_interest'), true)
		);

		//Filter for the field states_applicable
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'States Applicable');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "ACT";
		$options[0]->text = "ACT";
		$options[1] = new stdClass();
		$options[1]->value = "NSW";
		$options[1]->text = "NSW";
		$options[2] = new stdClass();
		$options[2]->value = "NT";
		$options[2]->text = "NT";
		$options[3] = new stdClass();
		$options[3]->value = "QLD";
		$options[3]->text = "QLD";
		$options[4] = new stdClass();
		$options[4]->value = "SA";
		$options[4]->text = "SA";
		$options[5] = new stdClass();
		$options[5]->value = "TAS";
		$options[5]->text = "TAS";
		$options[6] = new stdClass();
		$options[6]->value = "VIC";
		$options[6]->text = "VIC";
		$options[7] = new stdClass();
		$options[7]->value = "WA";
		$options[7]->text = "WA";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_states_applicable',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.states_applicable'), true)
		);

		//Filter for the field redraw_facility
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Redraw Facility');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "YES";
		$options[1] = new stdClass();
		$options[1]->value = "0";
		$options[1]->text = "NO";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_redraw_facility',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.redraw_facility'), true)
		);

		//Filter for the field extra_repayments
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Extra Repayments');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "Unlimited extra repayments";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_extra_repayments',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.extra_repayments'), true)
		);

		//Filter for the field weekly_repayments
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Weekly Repayments');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "YES";
		$options[1] = new stdClass();
		$options[1]->value = "0";
		$options[1]->text = "NO";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_weekly_repayments',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.weekly_repayments'), true)
		);

		//Filter for the field fortnightly_repayments
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Fortnightly Repayments');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "YES";
		$options[1] = new stdClass();
		$options[1]->value = "0";
		$options[1]->text = "NO";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_fortnightly_repayments',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.fortnightly_repayments'), true)
		);

		//Filter for the field monthly_repayments
		$select_label = JText::sprintf('COM_LOAN_INVESTMENT_FILTER_SELECT_LABEL', 'Monthly Repayments');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "YES";
		$options[1] = new stdClass();
		$options[1]->value = "0";
		$options[1]->text = "NO";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_monthly_repayments',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.monthly_repayments'), true)
		);

	}

	/**
	 * Method to order fields 
	 *
	 * @return void 
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => JText::_('JGRID_HEADING_ID'),
			'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
			'a.`state`' => JText::_('JSTATUS'),
			'a.`provider_name`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_PROVIDER_NAME'),
			'a.`loan_display_name`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_LOAN_DISPLAY_NAME'),
			'a.`maximum_lvr`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_MAXIMUM_LVR'),
			'a.`loan_term`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_LOAN_TERM'),
			'a.`borrowing_amount_range`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_BORROWING_AMOUNT_RANGE'),
			'a.`refinance`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_REFINANCE'),
			'a.`line_of_credit`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_LINE_OF_CREDIT'),
			'a.`self_managed_super`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_SELF_MANAGED_SUPER'),
			'a.`interest_rate_structure`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_INTEREST_RATE_STRUCTURE'),
			'a.`interest_only`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_INTEREST_ONLY'),
			'a.`loan_allows_split_interest_rate`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_LOAN_ALLOWS_SPLIT_INTEREST_RATE'),
			'a.`principal_interest`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_PRINCIPAL_INTEREST'),
			'a.`states_applicable`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_STATES_APPLICABLE'),
			'a.`redraw_facility`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_REDRAW_FACILITY'),
			'a.`redraw_fee`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_REDRAW_FEE'),
			'a.`extra_repayments`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_EXTRA_REPAYMENTS'),
			'a.`weekly_repayments`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_WEEKLY_REPAYMENTS'),
			'a.`fortnightly_repayments`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_FORTNIGHTLY_REPAYMENTS'),
			'a.`monthly_repayments`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_MONTHLY_REPAYMENTS'),
			'a.`created_by`' => JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_CREATED_BY'),
		);
	}
}
