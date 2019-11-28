<?php

/**
 * @version     1.0.0
 * @package     com_financial
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once(  JPATH_LIBRARIES . '/vendor/chargebee/chargebee-php/lib/ChargeBee.php');

/**
 * View class for a list of Financial.
 */
class FinancialViewPastinvoices extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $app = JFactory::getApplication();

        $this->state = $this->get('State');
        $this->items = $this->get('Pastinvoices');
        $this->pagination = $this->get('Pagination');
        $this->params = $app->getParams('com_financial');
       
		
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {

            throw new Exception(implode("\n", $errors));
        }
        $user       = JFactory::getUser();
        $db = JFactory::getDbo();
        // Create a new query object.
        $query = $db->getQuery(true);
        // Select all records from the user profile table where key begins with "custom.".
        // Order it by the ordering field.
        $query->select($db->quoteName(array('sub_id')));
        $query->from($db->quoteName('#__mt_links'));
        $query->where($db->quoteName('link_id') . ' = '. $db->quote($user->link_id));
        // Reset the query using our newly populated query object.
        $db->setQuery($query);
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $link = $db->loadObjectList()[0];
        $this->message = 'No invoices currently available.';
        try {
            $subscription = ChargeBee_Subscription::retrieve($link->sub_id);
            // var_dump($subscription->subscription()->id);
            $all = [];
            $invoices = ChargeBee_Invoice::invoicesForSubscription($subscription->subscription()->id);
            $i = 0;
            foreach($invoices as $entry ) {
                $invoice = $entry->invoice();
                if ( $invoice->status == "pending" ) {
                    continue;
                }
                $result = ChargeBee_Invoice::pdf($invoice->id);
                $the_invoice = new stdClass();
                $the_invoice->id = $invoice->id;
                $the_invoice->date = date('d/m/Y', $invoice->date);
                $the_invoice->dueDate = date('d/m/Y', $invoice->dueDate);
                $the_invoice->total = '$'.number_format($invoice->total / 100, 2, '.', '');;
                $the_invoice->amountPaid = '$'.number_format($invoice->amountPaid / 100, 2, '.', '');;
                $the_invoice->paidAt = date('d/m/Y', $invoice->paidAt);
                $the_invoice->status = $invoice->status;
                $the_invoice->link = $result->download()->downloadUrl;
                $all[$i] = $the_invoice;
                $i++;
            }
            $this->invoices = $all;
            //$this->message = '';
        } catch (Exception $e) {
           // var_dump($e);
           $this->invoices = array();
           $this->message = 'Error getting Subscriber Data. Please contact our Support Department via our Contact Us page. ';
           //JFactory::getApplication()->enqueueMessage('User has no Subscription data...', 'warning');
        }

        $this->_prepareDocument();
        parent::display($tpl);
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument() {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_FINANCIAL_DEFAULT_PAGE_TITLE'));
        }
        $title = $this->params->get('page_title', '');
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }

}