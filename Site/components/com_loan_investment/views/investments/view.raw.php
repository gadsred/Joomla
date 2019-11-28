<?php
/**
 * @version 1.5.5 2011-04-01
 * @package Joomla
 * @subpackage Intellectual Property
 * @copyright (C) 2011 the Thinkery
 * @license GNU/GPL see LICENSE.php
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.view');

class Loan_investmentViewInvestments extends JViewLegacy
{
	function display()
	{
	 	
		global $option; 
		/* datatable stuff */
		$_REQUEST['borrow_amount'] = htmlspecialchars_decode($_REQUEST['borrow_amount']);	
		$_REQUEST['borrow_amount'] = intval( preg_replace("/([^0-9\\.])/i", "", $_REQUEST['borrow_amount']));


		$app = JFactory::getApplication();
        $document 	= JFactory::getDocument();
		$model          = $this->getModel();
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->params     = $app->getParams('com_loan_investment');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');      
	
		$this->totals = $this->get('Totals');
		

		$out = array(
			"draw"=> isset($_GET['draw']) ? $_GET['draw'] : 1,
			"recordsTotal"=> $this->totals,
			"recordsFiltered"=>$this->pagination->total
  		);
		$out['data'] = array();
		
		


		
		$borrow_amount = JRequest::getVar('borrow_amount');
		$paid_back = JRequest::getVar('paid_back');
		$paid_back_in_months = $paid_back * 12;
		
		foreach($this->items as $item) {

				if($item->advertised_rate!='')	{	
					/*
					if(JRequest::getVar('repayments')=='weekly')	{
						if(JRequest::getVar('loan_type')=='1')	{
							$adout = number_format(($borrow_amount*(float)$item->advertised_rate)/100/52,2);
						} else {
						
						
							$ir_permonth= ((float)$item->advertised_rate/100)/12;						
							$principal_interest =  $borrow_amount * ($ir_permonth)/(1-pow((1+$ir_permonth),-$paid_back_in_months));		
							$principal_interest = ($principal_interest*12)/52;	
							$adout =  number_format($principal_interest,2);
							
						}	
					} else {
						if(JRequest::getVar('loan_type')=='1')
						{
							$adout = number_format(($borrow_amount*(float)$item->advertised_rate)/100/12,2);
						}
						else
						{ 
							$ir_permonth= ((float)$item->advertised_rate/100)/12;
							$principal_interest =  $borrow_amount * ($ir_permonth)/(1-pow((1+$ir_permonth),-$paid_back_in_months));
							$principal_interest = ($principal_interest*12)/12;	
							$adout = number_format($principal_interest,2);
						}
					}*/

					$adout = Loan_investmentHelpersLoan_investment::getRepayment($borrow_amount,$paid_back_in_months,$item->advertised_rate,JRequest::getVar('repayments'),JRequest::getVar('loan_type'));
				}

				//Loan_investmentHelpersLoan_investment::getProviderWebSite($item->user_id),
			$more = array(				
				$item->pid,
				$item->provider_name,
				Loan_investmentHelpersLoan_investment::getUrlLoanDisplayName($item->loan_display_name),
				$item->website,
				$item->is_sponsor
			);
		
			$loans_name = $item->loan_display_name;
			
			
			$out['data'][] = array(
				$item->id,
				$item->provider_logo,
				$loans_name,
				$item->advertised_rate.'%',
				$item->comparison_rate.'%',
				($item->application_fee > 0) ? '$'.number_format($item->application_fee) : '$'.$item->application_fee ,
				$item->minimum_deposit.'%',
				'$'.$adout,
				$more,

			);
		
		}

		echo json_encode($out);
		//setcookie('draw',intval($_COOKIE['draw'])+1);
		//var_dump($this->items);
        //return $this->items;

	}


}

?>
