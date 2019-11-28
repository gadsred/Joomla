<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Loan_investment
 * @author     gadiel_Rojo <gadsred@gmail.com>
 * @copyright  2016 gadiel_Rojo
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
jimport('joomla.event.dispatcher');

use Joomla\Utilities\ArrayHelper;
/**
 * Loan_investment model.
 *
 * @since  1.6
 */
class Loan_investmentModelInvestment extends JModelItem
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @since    1.6
	 *
	 */
	protected function populateState()
	{
		$app  = JFactory::getApplication('com_loan_investment');
        $user = JFactory::getUser();

        // Check published state
        if ((!$user->authorise('core.edit.state', 'com_loan_investment')) && (!$user->authorise('core.edit', 'com_loan_investment')))
        {
            $this->setState('filter.published', 1);
            $this->setState('fileter.archived', 2);
        }

		// Load state from the request userState on edit or from the passed variable on default
		if (JFactory::getApplication()->input->get('layout') == 'edit')
		{
			$id = JFactory::getApplication()->getUserState('com_loan_investment.edit.investment.id');
		}
		else
		{
			$id = JFactory::getApplication()->input->get('id');
			JFactory::getApplication()->setUserState('com_loan_investment.edit.investment.id', $id);
		}

		$this->setState('investment.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('investment.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

	/**
	 * Method to get an object.
	 *
	 * @param   integer  $id  The id of the object to get.
	 *
	 * @return  mixed    Object on success, false on failure.
	 */
	public function &getData($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = new stdClass();//false;

			if (empty($id))
			{
				$id = $this->getState('investment.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($id))
			{
				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if (isset($table->state) && $table->state != $published)
					{
						throw new Exception(JText::_('COM_LOAN_INVESTMENT_ITEM_NOT_LOADED'), 403);
					}
				}

				// Convert the JTable to a clean JObject.
				$properties  = $table->getProperties(1);
				$this->_item = ArrayHelper::toObject($properties, 'JObject');
			}

		}

		if (isset($this->_item->user_id) )
		{
			//$this->_item->user_id_name = JFactory::getUser($this->_item->user_id)->name;
		}
					$this->_item->maximum_lvr = JText::_('' . $this->_item->maximum_lvr);
					$this->_item->loan_term = JText::_('' . $this->_item->loan_term);
					$this->_item->borrowing_amount_range = JText::_('' . $this->_item->borrowing_amount_range);
					$this->_item->refinance = JText::_('' . $this->_item->refinance);
					$this->_item->line_of_credit = JText::_('' . $this->_item->line_of_credit);
					$this->_item->self_managed_super = JText::_('' . $this->_item->self_managed_super);
					$this->_item->interest_rate_structure = JText::_('' . $this->_item->interest_rate_structure);
					$this->_item->interest_only = JText::_('' . $this->_item->interest_only);
					$this->_item->loan_allows_split_interest_rate = JText::_('' . $this->_item->loan_allows_split_interest_rate);
					$this->_item->principal_interest = JText::_('' . $this->_item->principal_interest);
					$this->_item->redraw_facility = JText::_('' . $this->_item->redraw_facility);
					$this->_item->extra_repayments = JText::_('' . $this->_item->extra_repayments);
					$this->_item->weekly_repayments = JText::_('' . $this->_item->weekly_repayments);
					$this->_item->fortnightly_repayments = JText::_('' . $this->_item->fortnightly_repayments);
					$this->_item->monthly_repayments = JText::_('' . $this->_item->monthly_repayments);if (isset($this->_item->created_by) )
		{
			//$this->_item->created_by_name = JFactory::getUser($this->_item->created_by)->name;
		}

		return $this->_item;
	}

	/**
	 * Get an instance of JTable class
	 *
	 * @param   string  $type    Name of the JTable class to get an instance of.
	 * @param   string  $prefix  Prefix for the table class name. Optional.
	 * @param   array   $config  Array of configuration values for the JTable object. Optional.
	 *
	 * @return  JTable|bool JTable if success, false on failure.
	 */
	public function getTable($type = 'Investment', $prefix = 'Loan_investmentTable', $config = array())
	{
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_loan_investment/tables');

		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get the id of an item by alias
	 *
	 * @param   string  $alias  Item alias
	 *
	 * @return  mixed
	 */
	public function getItemIdByAlias($alias)
	{
		$table = $this->getTable();

		$table->load(array('alias' => $alias));

		return $table->id;
	}

	/**
	 * Method to check in an item.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('investment.id');

		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
			if (method_exists($table, 'checkin'))
			{
				if (!$table->checkin($id))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('investment.id');

		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = JFactory::getUser();

			// Attempt to check the row out.
			if (method_exists($table, 'checkout'))
			{
				if (!$table->checkout($user->get('id'), $id))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Get the name of a category by id
	 *
	 * @param   int  $id  Category id
	 *
	 * @return  Object|null	Object if success, null in case of failure
	 */
	public function getCategoryName($id)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('title')
			->from('#__categories')
			->where('id = ' . $id);
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Publish the element
	 *
	 * @param   int  $id     Item id
	 * @param   int  $state  Publish state
	 *
	 * @return  boolean
	 */
	public function publish($id, $state)
	{
		$table = $this->getTable();
		$table->load($id);
		$table->state = $state;

		return $table->store();
	}

	/**
	 * Method to delete an item
	 *
	 * @param   int  $id  Element id
	 *
	 * @return  bool
	 */
	public function delete($id)
	{
		$table = $this->getTable();

		return $table->delete($id);
	}

	
}
