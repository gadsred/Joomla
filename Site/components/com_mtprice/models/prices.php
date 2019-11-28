<?php

/**
 * @version     1.0.0
 * @package     com_mtprice
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Mtprice records.
 */
class MtpriceModelPrices extends JModelList
{

	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
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
                'au_state', 'a.au_state',
                'description', 'a.description',
                'price_type', 'a.price_type',
                'keypoints_type', 'a.keypoints_type',
                'price', 'a.price',

			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{


		// Initialise variables.
		$app = JFactory::getApplication();

		// List state information
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->getInt('limitstart', 0);
		$this->setState('list.start', $limitstart);

		if ($list = $app->getUserStateFromRequest($this->context . '.list', 'list', array(), 'array'))
		{
			foreach ($list as $name => $value)
			{
				// Extra validations
				switch ($name)
				{
					case 'fullordering':
						$orderingParts = explode(' ', $value);

						if (count($orderingParts) >= 2)
						{
							// Latest part will be considered the direction
							$fullDirection = end($orderingParts);

							if (in_array(strtoupper($fullDirection), array('ASC', 'DESC', '')))
							{
								$this->setState('list.direction', $fullDirection);
							}

							unset($orderingParts[count($orderingParts) - 1]);

							// The rest will be the ordering
							$fullOrdering = implode(' ', $orderingParts);

							if (in_array($fullOrdering, $this->filter_fields))
							{
								$this->setState('list.ordering', $fullOrdering);
							}
						}
						else
						{
							$this->setState('list.ordering', $ordering);
							$this->setState('list.direction', $direction);
						}
						break;

					case 'ordering':
						if (!in_array($value, $this->filter_fields))
						{
							$value = $ordering;
						}
						break;

					case 'direction':
						if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
						{
							$value = $direction;
						}
						break;

					case 'limit':
						$limit = $value;
						break;

					// Just to keep the default case
					default:
						$value = $value;
						break;
				}

				$this->setState('list.' . $name, $value);
			}
		}

		// Receive & set filters
		if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array'))
		{
			foreach ($filters as $name => $value)
			{
				$this->setState('filter.' . $name, $value);
			}
		}

		$ordering = $app->input->get('filter_order');
		if (!empty($ordering))
		{
			$list             = $app->getUserState($this->context . '.list');
			$list['ordering'] = $app->input->get('filter_order');
			$app->setUserState($this->context . '.list', $list);
		}

		$orderingDirection = $app->input->get('filter_order_Dir');
		if (!empty($orderingDirection))
		{
			$list              = $app->getUserState($this->context . '.list');
			$list['direction'] = $app->input->get('filter_order_Dir');
			$app->setUserState($this->context . '.list', $list);
		}

		$list = $app->getUserState($this->context . '.list');

		if (empty($list['ordering']))
{
	$list['ordering'] = 'ordering';
}

if (empty($list['direction']))
{
	$list['direction'] = 'asc';
}

		$this->setState('list.ordering', $list['ordering']);
		$this->setState('list.direction', $list['direction']);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return    JDatabaseQuery
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		$jinput = JFactory::getApplication()->input;
		$state=$jinput->get('state', '', '');
		
			//---gads redirect list with us---//
			$user_name=JFactory::getUser()->username;
			// $db =  JFactory::getDbo();
			// $query= "Select link_published,sub_id,invite,invite_code,invite_open From #__mt_links Where link_id='{$link_id}'";
			// $db->setQuery($query);
			// $link_status = $db->loadObject();
			if(!$user_name)
			{
				// $app = JFactory::getApplication();
				// $app->redirect('index.php?option=com_chargify&view=registers');
			}
		
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				$this->getState(
					'list.select', 'DISTINCT a.*'
				)
			);

		$query->from('`#__mt_price` AS a');

		
		// Join over the created by field 'user_id'
		$query->join('LEFT', '#__users AS user_id ON user_id.id = a.user_id');
		$query->where('a.keypoints_type= "2" and a.price_type="b" and a.id!="0"');
		
if (!JFactory::getUser()->authorise('core.edit.state', 'com_mtprice'))
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
				$query->where('( a.description LIKE '.$search.' )');
			}
		}

		

		//Filtering au_state
		$filter_au_state = $this->state->get("filter.au_state");
		//gads filter after submit price
		if(!empty($state))
		{
			$filter_au_state=$state;
		}
		
		
		if ($filter_au_state) {
			$query->where("a.au_state = '".$db->escape($filter_au_state)."'");
		}

		//Filtering price_type
		$filter_price_type = $this->state->get("filter.price_type");
		if ($filter_price_type) {
			$query->where("a.price_type = '".$db->escape($filter_price_type)."'");
		}

		//Filtering keypoints_type
		$filter_keypoints_type = $this->state->get("filter.keypoints_type");
		if ($filter_keypoints_type) {
			$query->where("a.keypoints_type = '".$db->escape($filter_keypoints_type)."'");
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
	
	//gads get extra start
	 function getExtra()
	{
		
		$jinput = JFactory::getApplication()->input;
		$state=$jinput->get('state', '', '');
		
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query
			->select(
				$this->getState(
					'list.select', 'DISTINCT a.*'
				)
			);

		$query->from('`#__mt_price` AS a');

		
		// Join over the created by field 'user_id'
		$query->join('LEFT', '#__users AS user_id ON user_id.id = a.user_id');
		$query->where('a.keypoints_type= "1" and price_type="b"');
		
			if (!JFactory::getUser()->authorise('core.edit.state', 'com_mtprice'))
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
				$query->where('( a.description LIKE '.$search.' )');
			}
		}

		

		//Filtering au_state
		$filter_au_state = $this->state->get("filter.au_state");
		//gads filter after submit price
		if(!empty($state))
		{
			$filter_au_state=$state;
		}
		
		if ($filter_au_state) {
			$query->where("a.au_state = '".$db->escape($filter_au_state)."'");
		}

		//Filtering price_type
		$filter_price_type = $this->state->get("filter.price_type");
		if ($filter_price_type) {
			$query->where("a.price_type = '".$db->escape($filter_price_type)."'");
		}

		//Filtering keypoints_type
		$filter_keypoints_type = $this->state->get("filter.keypoints_type");
		if ($filter_keypoints_type) {
			$query->where("a.keypoints_type = '".$db->escape($filter_keypoints_type)."'");
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		$db->setQuery($query);
		$row = $db->loadObjectList();
		return $row;
	}
	//gads get extra end
	
	//gads get price start
	function getPrice()
	{
		
		$jinput = JFactory::getApplication()->input;
		$state=$jinput->get('state', '', '');
		$link_id=JFactory::getUser()->link_id;
		
		$user=JFactory::getUser();
		if($user->name)
		{
			// Create a new query object.
			$db    = $this->getDbo();
			$query = $db->getQuery(true);

			// Select the required fields from the table.
			$query
				->select(
					$this->getState(
						'list.select', 'DISTINCT a.*'
					)
				);

			$query->from('`#__mt_price` AS a');

			
			// Join over the created by field 'user_id'
			$query->join('LEFT', '#__users AS user ON a.link_id=user.link_id ');
			$query->where('a.price_type="b" and a.link_id='.$link_id);
			
				if (!JFactory::getUser()->authorise('core.edit.state', 'com_mtprice'))
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
					$query->where('( a.description LIKE '.$search.' )');
				}
			}

			

			//Filtering au_state
			// $filter_au_state = $this->state->get("filter.au_state");
			// //gads filter after submit price
			// if(!empty($state))
			// {
				// $filter_au_state=$state;
			// }
			
			// if ($filter_au_state) {
				// $query->where("a.au_state = '".$db->escape($filter_au_state)."'");
			// }

			//Filtering price_type
			$filter_price_type = $this->state->get("filter.price_type");
			if ($filter_price_type) {
				$query->where("a.price_type = '".$db->escape($filter_price_type)."'");
			}

			//Filtering keypoints_type
			$filter_keypoints_type = $this->state->get("filter.keypoints_type");
			if ($filter_keypoints_type) {
				$query->where("a.keypoints_type = '".$db->escape($filter_keypoints_type)."'");
			}

			// Add the list ordering clause.
			$orderCol  = $this->state->get('list.ordering');
			$orderDirn = $this->state->get('list.direction');
			if ($orderCol && $orderDirn)
			{
				$query->order($db->escape($orderCol . ' ' . $orderDirn));
			}

			$db->setQuery($query);
			$row = $db->loadObjectList();
			return $row;
		}
	}
	//gads get price end

	public function getItems()
	{
		$items = parent::getItems();
		foreach($items as $item){
	
					$item->au_state = JText::_('COM_MTPRICE_PRICES_AU_STATE_OPTION_' . strtoupper($item->au_state));
					$item->price_type = JText::_('COM_MTPRICE_PRICES_PRICE_TYPE_OPTION_' . strtoupper($item->price_type));
					$item->keypoints_type = JText::_('COM_MTPRICE_PRICES_KEYPOINTS_TYPE_OPTION_' . strtoupper($item->keypoints_type));
}

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 */
	protected function loadFormData()
	{
		$app              = JFactory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;
		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && !$this->isValidDate($value))
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}
		if ($error_dateformat)
		{
			$app->enqueueMessage(JText::_("COM_MTPRICE_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in an specified format (YYYY-MM-DD)
	 *
	 * @param string Contains the date to be checked
	 *
	 */
	private function isValidDate($date)
	{
		return preg_match("/^(19|20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$/", $date) && date_create($date);
	}

}
