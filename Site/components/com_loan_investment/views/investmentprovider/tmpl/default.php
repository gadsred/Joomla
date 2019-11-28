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

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_loan_investment.' . $this->item->id);

if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_loan_investment' . $this->item->id))
{
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>

<div class="item_fields">

	<table class="table">
		

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENTPROVIDER_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENTPROVIDER_PROVIDER_NAME'); ?></th>
			<td><?php echo $this->item->provider_name; ?></td>
		</tr>

		<tr>
			<th><?php echo JText::_('COM_LOAN_INVESTMENT_FORM_LBL_INVESTMENTPROVIDER_PROVIDER_LOGO'); ?></th>
			<td><?php echo $this->item->provider_logo; ?></td>
		</tr>

	</table>

</div>

<?php if($canEdit): ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_loan_investment&task=investmentprovider.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_LOAN_INVESTMENT_EDIT_ITEM"); ?></a>

<?php endif; ?>

<?php if (JFactory::getUser()->authorise('core.delete','com_loan_investment.investmentprovider.'.$this->item->id)) : ?>

	<a class="btn" href="<?php echo JRoute::_('index.php?option=com_loan_investment&task=investmentprovider.remove&id=' . $this->item->id, false, 2); ?>"><?php echo JText::_("COM_LOAN_INVESTMENT_DELETE_ITEM"); ?></a>

<?php endif; ?>