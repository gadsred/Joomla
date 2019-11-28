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
class MtpriceModelPrices extends JModelList {

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
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
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_published', '', 'string');
        $this->setState('filter.state', $published);

        
		//Filtering au_state
		$this->setState('filter.au_state', $app->getUserStateFromRequest($this->context.'.filter.au_state', 'filter_au_state', '', 'string'));

		//Filtering price_type
		$this->setState('filter.price_type', $app->getUserStateFromRequest($this->context.'.filter.price_type', 'filter_price_type', '', 'string'));

		//Filtering keypoints_type
		$this->setState('filter.keypoints_type', $app->getUserStateFromRequest($this->context.'.filter.keypoints_type', 'filter_keypoints_type', '', 'string'));


        // Load the parameters.
        $params = JComponentHelper::getParams('com_mtprice');
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.au_state', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param	string		$id	A prefix for the store id.
     * @return	string		A store id.
     * @since	1.6
     */
    protected function getStoreId($id = '') {
        // Compile the store id.
        $id.= ':' . $this->getState('filter.search');
        $id.= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return	JDatabaseQuery
     * @since	1.6
     */
    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
                $this->getState(
                        'list.select', 'DISTINCT a.*'
                )
        );
        $query->from('`#__mt_price` AS a');

        
		// Join over the user field 'user_id'
		$query->select('user_id.name AS user_id');
		$query->join('LEFT', '#__users AS user_id ON user_id.id = a.user_id');

        

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int) substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.au_state LIKE '.$search.'  OR  a.description LIKE '.$search.'  OR  a.price_type LIKE '.$search.'  OR  a.keypoints_type LIKE '.$search.'  OR  a.price LIKE '.$search.' )');
            }
        }

        

		//Filtering au_state
		$filter_au_state = $this->state->get("filter.au_state");
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
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        
		foreach ($items as $oneItem) {
					$oneItem->au_state = JText::_('COM_MTPRICE_PRICES_AU_STATE_OPTION_' . strtoupper($oneItem->au_state));
					$oneItem->price_type = JText::_('COM_MTPRICE_PRICES_PRICE_TYPE_OPTION_' . strtoupper($oneItem->price_type));
					$oneItem->keypoints_type = JText::_('COM_MTPRICE_PRICES_KEYPOINTS_TYPE_OPTION_' . strtoupper($oneItem->keypoints_type));
		}
        return $items;
    }

}
