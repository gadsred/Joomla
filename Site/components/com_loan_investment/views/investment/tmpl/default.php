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

// $canEdit = JFactory::getUser()->authorise('core.edit', 'com_loan_investment.' . $this->item->id);

// if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_loan_investment' . $this->item->id))
// {
	// $canEdit = JFactory::getUser()->id == $this->item->created_by;
// }
?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_USER_ID'); ?></th>
			<td><?php echo $this->item->user_id_name; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_PROVIDER_NAME'); ?></th>
			<td><?php echo $this->item->provider_name; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_LOAN_DISPLAY_NAME'); ?></th>
			<td><?php echo $this->item->loan_display_name; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_MAXIMUM_LVR'); ?></th>
			<td><?php echo $this->item->maximum_lvr; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_LOAN_TERM'); ?></th>
			<td><?php echo $this->item->loan_term; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_BORROWING_AMOUNT_RANGE'); ?></th>
			<td><?php echo $this->item->borrowing_amount_range; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_REFINANCE'); ?></th>
			<td><?php echo $this->item->refinance; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_LINE_OF_CREDIT'); ?></th>
			<td><?php echo $this->item->line_of_credit; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_SELF_MANAGED_SUPER'); ?></th>
			<td><?php echo $this->item->self_managed_super; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_INTEREST_RATE_STRUCTURE'); ?></th>
			<td><?php echo $this->item->interest_rate_structure; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_INTEREST_ONLY'); ?></th>
			<td><?php echo $this->item->interest_only; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_LOAN_ALLOWS_SPLIT_INTEREST_RATE'); ?></th>
			<td><?php echo $this->item->loan_allows_split_interest_rate; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_PRINCIPAL_INTEREST'); ?></th>
			<td><?php echo $this->item->principal_interest; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_STATES_APPLICABLE'); ?></th>
			<td><?php echo $this->item->states_applicable; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_REDRAW_FACILITY'); ?></th>
			<td><?php echo $this->item->redraw_facility; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_REDRAW_FEE'); ?></th>
			<td><?php echo $this->item->redraw_fee; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_EXTRA_REPAYMENTS'); ?></th>
			<td><?php echo $this->item->extra_repayments; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_WEEKLY_REPAYMENTS'); ?></th>
			<td><?php echo $this->item->weekly_repayments; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_FORTNIGHTLY_REPAYMENTS'); ?></th>
			<td><?php echo $this->item->fortnightly_repayments; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_MONTHLY_REPAYMENTS'); ?></th>
			<td><?php echo $this->item->monthly_repayments; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_DATE_CREATED'); ?></th>
			<td><?php echo $this->item->date_created; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_DATE_MODIFIED'); ?></th>
			<td><?php echo $this->item->date_modified; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENT_CREATED_BY'); ?></th>
			<td><?php echo $this->item->created_by_name; ?></td>
		</tr>

	</table>

</div>

<?php if($canEdit): ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_loan_investment&task=investment.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_LOAN_INVESTMENT_EDIT_ITEM"); ?></a>

<?php endif; ?>

<?php //if (JFactory::getUser()->authorise('core.delete','com_loan_investment.investment.'.$this->item->id)) : ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_loan_investment&task=investment.remove&id=' . $this->item->id, false, 2); ?>"><?php echo JText::_("COM_LOAN_INVESTMENT_DELETE_ITEM"); ?></a>

<?php// endif; ?>