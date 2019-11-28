<?php
/**
 * @version     1.0.0
 * @package     com_financial
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */
// no direct access
defined('_JEXEC') or die;

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_financial');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_financial')) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">
        <table class="table">
            <tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_UPDATECARD_ID'); ?></th>
			<td><?php echo $this->item->id; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_UPDATECARD_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_UPDATECARD_CREATED_BY'); ?></th>
			<td><?php echo $this->item->created_by_name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_UPDATECARD_FIRST_NAME'); ?></th>
			<td><?php echo $this->item->first_name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_UPDATECARD_LAST_NAME'); ?></th>
			<td><?php echo $this->item->last_name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_UPDATECARD_CARD_NUMBER'); ?></th>
			<td><?php echo $this->item->card_number; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_UPDATECARD_CVV'); ?></th>
			<td><?php echo $this->item->cvv; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_UPDATECARD_EXPIRE_MONTH'); ?></th>
			<td><?php echo $this->item->expire_month; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_UPDATECARD_EXPIRE_YEAR'); ?></th>
			<td><?php echo $this->item->expire_year; ?></td>
</tr>

        </table>
    </div>
    <?php if($canEdit): ?>
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_financial&task=updatecard.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_FINANCIAL_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_financial')):?>
									<a class="btn" href="<?php echo JRoute::_('index.php?option=com_financial&task=updatecard.remove&id=' . $this->item->id, false, 2); ?>"><?php echo JText::_("COM_FINANCIAL_DELETE_ITEM"); ?></a>
								<?php endif; ?>
    <?php
else:
    echo JText::_('COM_FINANCIAL_ITEM_NOT_LOADED');
endif;
?>
