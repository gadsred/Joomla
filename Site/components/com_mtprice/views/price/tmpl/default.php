<?php
/**
 * @version     1.0.0
 * @package     com_mtprice
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */
// no direct access
defined('_JEXEC') or die;
$doc = JFactory::getDocument();
$doc->addCustomTag("<meta name=\"robots\" content=\"noindex,nofollow\">");
$canEdit = JFactory::getUser()->authorise('core.edit', 'com_mtprice');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_mtprice')) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

    <div class="item_fields">
        <table class="table">
            <tr>
			<th><?php echo JText::_('COM_MTPRICE_FORM_LBL_PRICE_ID'); ?></th>
			<td><?php echo $this->item->id; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTPRICE_FORM_LBL_PRICE_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTPRICE_FORM_LBL_PRICE_USER_ID'); ?></th>
			<td><?php echo $this->item->user_id_name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTPRICE_FORM_LBL_PRICE_AU_STATE'); ?></th>
			<td><?php echo $this->item->au_state; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTPRICE_FORM_LBL_PRICE_DESCRIPTION'); ?></th>
			<td><?php echo $this->item->description; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTPRICE_FORM_LBL_PRICE_PRICE_TYPE'); ?></th>
			<td><?php echo $this->item->price_type; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTPRICE_FORM_LBL_PRICE_KEYPOINTS_TYPE'); ?></th>
			<td><?php echo $this->item->keypoints_type; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTPRICE_FORM_LBL_PRICE_PRICE'); ?></th>
			<td><?php echo $this->item->price; ?></td>
</tr>

        </table>
    </div>
    <?php if($canEdit): ?>
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_mtprice&task=price.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_MTPRICE_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_mtprice')):?>
									<a class="btn" href="<?php echo JRoute::_('index.php?option=com_mtprice&task=price.remove&id=' . $this->item->id, false, 2); ?>"><?php echo JText::_("COM_MTPRICE_DELETE_ITEM"); ?></a>
								<?php endif; ?>
    <?php
else:
    echo JText::_('COM_MTPRICE_ITEM_NOT_LOADED');
endif;
?>
