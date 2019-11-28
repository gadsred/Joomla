<?php

/**
 * @version     1.0.0
 * @package     com_mtprice
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Mtprice.
 */
class MtpriceViewPrices extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        MtpriceHelper::addSubmenu('prices');

        $this->addToolbar();

        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar() {
        require_once JPATH_COMPONENT . '/helpers/mtprice.php';

        $state = $this->get('State');
        $canDo = MtpriceHelper::getActions($state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_MTPRICE_TITLE_PRICES'), 'prices.png');

        //Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/price';
        if (file_exists($formPath)) {

            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('price.add', 'JTOOLBAR_NEW');
            }

            if ($canDo->get('core.edit') && isset($this->items[0])) {
                JToolBarHelper::editList('price.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('prices.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('prices.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } else if (isset($this->items[0])) {
                //If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'prices.delete', 'JTOOLBAR_DELETE');
            }

            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::archiveList('prices.archive', 'JTOOLBAR_ARCHIVE');
            }
            if (isset($this->items[0]->checked_out)) {
                JToolBarHelper::custom('prices.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
            }
        }

        //Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'prices.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } else if ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('prices.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_mtprice');
        }

        //Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_mtprice&view=prices');

        $this->extra_sidebar = '';
        
		JHtmlSidebar::addFilter(

			JText::_('JOPTION_SELECT_PUBLISHED'),

			'filter_published',

			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true)

		);

		//Filter for the field au_state
		$select_label = JText::sprintf('COM_MTPRICE_FILTER_SELECT_LABEL', 'Au State');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "ALL";
		$options[0]->text = "ALL";
		$options[1] = new stdClass();
		$options[1]->value = "ACT";
		$options[1]->text = "ACT";
		$options[2] = new stdClass();
		$options[2]->value = "NSW";
		$options[2]->text = "NSW";
		$options[3] = new stdClass();
		$options[3]->value = "NT";
		$options[3]->text = "NT";
		$options[4] = new stdClass();
		$options[4]->value = "QLD";
		$options[4]->text = "QLD";
		$options[5] = new stdClass();
		$options[5]->value = "SA";
		$options[5]->text = "SA";
		$options[6] = new stdClass();
		$options[6]->value = "TAS";
		$options[6]->text = "TAS";
		$options[7] = new stdClass();
		$options[7]->value = "VIC";
		$options[7]->text = "VIC";
		$options[8] = new stdClass();
		$options[8]->value = "WA";
		$options[8]->text = "WA";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_au_state',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.au_state'), true)
		);

		//Filter for the field price_type
		$select_label = JText::sprintf('COM_MTPRICE_FILTER_SELECT_LABEL', 'Price Type');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "b";
		$options[0]->text = "Buyers";
		$options[1] = new stdClass();
		$options[1]->value = "s";
		$options[1]->text = "Sellers";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_price_type',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.price_type'), true)
		);

		//Filter for the field keypoints_type
		$select_label = JText::sprintf('COM_MTPRICE_FILTER_SELECT_LABEL', 'Keypoints For');
		$options = array();
		$options[0] = new stdClass();
		$options[0]->value = "1";
		$options[0]->text = "Extra Chages";
		$options[1] = new stdClass();
		$options[1]->value = "2";
		$options[1]->text = "Estimated Conveyancing fees";
		JHtmlSidebar::addFilter(
			$select_label,
			'filter_keypoints_type',
			JHtml::_('select.options', $options , "value", "text", $this->state->get('filter.keypoints_type'), true)
		);

    }

	protected function getSortFields()
	{
		return array(
		'a.id' => JText::_('JGRID_HEADING_ID'),
		'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
		'a.state' => JText::_('JSTATUS'),
		'a.au_state' => JText::_('COM_MTPRICE_PRICES_AU_STATE'),
		'a.description' => JText::_('COM_MTPRICE_PRICES_DESCRIPTION'),
		'a.price_type' => JText::_('COM_MTPRICE_PRICES_PRICE_TYPE'),
		'a.keypoints_type' => JText::_('COM_MTPRICE_PRICES_KEYPOINTS_TYPE'),
		'a.price' => JText::_('COM_MTPRICE_PRICES_PRICE'),
		);
	}

}
