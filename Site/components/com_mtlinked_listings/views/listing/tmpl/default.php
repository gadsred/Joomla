<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Mtlinked_listings
 * @author     gadiel_Rojo <gadsred@gmail.com>
 * @copyright  Copyright (C) 2016. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_mtlinked_listings.' . $this->item->id);
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_mtlinked_listings' . $this->item->id)) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}
?>
<?php if ($this->item) : ?>

	<div class="item_fields">
		<table class="table">
			<tr>
			<th><?php echo JText::_('COM_MTLINKED_LISTINGS_FORM_LBL_LISTING_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTLINKED_LISTINGS_FORM_LBL_LISTING_MAIN_LINK'); ?></th>
			<td><?php echo $this->item->main_link; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTLINKED_LISTINGS_FORM_LBL_LISTING_SUB_LINK'); ?></th>
			<td><?php echo $this->item->sub_link; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTLINKED_LISTINGS_FORM_LBL_LISTING_SUBS_ID'); ?></th>
			<td><?php echo $this->item->subs_id; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTLINKED_LISTINGS_FORM_LBL_LISTING_SUBS_TYPE'); ?></th>
			<td><?php echo $this->item->subs_type; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTLINKED_LISTINGS_FORM_LBL_LISTING_USER_ID'); ?></th>
			<td><?php echo $this->item->user_id_name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTLINKED_LISTINGS_FORM_LBL_LISTING_LINK_CREATED'); ?></th>
			<td><?php echo $this->item->link_created; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_MTLINKED_LISTINGS_FORM_LBL_LISTING_LINK_UPDATED'); ?></th>
			<td><?php echo $this->item->link_updated; ?></td>
</tr>

		</table>
	</div>
	<?php if($canEdit): ?>
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_mtlinked_listings&task=listing.edit&id='.$this->item->id); ?>"><?php echo JText::_("COM_MTLINKED_LISTINGS_EDIT_ITEM"); ?></a>
	<?php endif; ?>
								<?php if(JFactory::getUser()->authorise('core.delete','com_mtlinked_listings.listing.'.$this->item->id)):?>
									<a class="btn" href="<?php echo JRoute::_('index.php?option=com_mtlinked_listings&task=listing.remove&id=' . $this->item->id, false, 2); ?>"><?php echo JText::_("COM_MTLINKED_LISTINGS_DELETE_ITEM"); ?></a>
								<?php endif; ?>
	<?php
else:
	echo JText::_('COM_MTLINKED_LISTINGS_ITEM_NOT_LOADED');
endif;
