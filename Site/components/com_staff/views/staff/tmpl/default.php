<?php
/**
 * @version     1.0.0
 * @package     com_staff
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */
// no direct access
defined('_JEXEC') or die;

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_staff');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_staff')) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">
        <table class="table">
            <tr>
			<th><?php echo JText::_('COM_STAFF_FORM_LBL_STAFF_ID'); ?></th>
			<td><?php echo $this->item->id; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_STAFF_FORM_LBL_STAFF_USER_ID'); ?></th>
			<td><?php echo $this->item->user_id_name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_STAFF_FORM_LBL_STAFF_NAME'); ?></th>
			<td><?php echo $this->item->name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_STAFF_FORM_LBL_STAFF_TITLE'); ?></th>
			<td><?php echo $this->item->title; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_STAFF_FORM_LBL_STAFF_IMAGE'); ?></th>
			<td><?php echo $this->item->image; ?></td>
</tr>

        </table>
    </div>
    <?php if($canEdit): ?>
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_staff&task=staff.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_STAFF_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_staff')):?>
									<a class="btn" href="<?php echo JRoute::_('index.php?option=com_staff&task=staff.remove&id=' . $this->item->id, false, 2); ?>"><?php echo JText::_("COM_STAFF_DELETE_ITEM"); ?></a>
								<?php endif; ?>
    <?php
else:
    echo JText::_('COM_STAFF_ITEM_NOT_LOADED');
endif;
?>
