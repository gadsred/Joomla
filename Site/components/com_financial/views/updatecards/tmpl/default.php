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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_financial');
$canEdit    = $user->authorise('core.edit', 'com_financial');
$canCheckin = $user->authorise('core.manage', 'com_financial');
$canChange  = $user->authorise('core.edit.state', 'com_financial');
$canDelete  = $user->authorise('core.delete', 'com_financial');
?>

<form action="<?php echo JRoute::_('index.php?option=com_financial&view=updatecards'); ?>" method="post" name="adminForm" id="adminForm">

	
	<table class="table table-striped" id="updatecardList">
		<thead>
		<tr>
			<?php if (isset($this->items[0]->state)): ?>
				<th width="5%">
	<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
</th>
			<?php endif; ?>

							<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FINANCIAL_UPDATECARDS_FIRST_NAME', 'a.first_name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FINANCIAL_UPDATECARDS_LAST_NAME', 'a.last_name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FINANCIAL_UPDATECARDS_CARD_NUMBER', 'a.card_number', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FINANCIAL_UPDATECARDS_CVV', 'a.cvv', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FINANCIAL_UPDATECARDS_EXPIRE_MONTH', 'a.expire_month', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
				<?php echo JHtml::_('grid.sort',  'COM_FINANCIAL_UPDATECARDS_EXPIRE_YEAR', 'a.expire_year', $listDirn, $listOrder); ?>
				</th>


			<?php if (isset($this->items[0]->id)): ?>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			<?php endif; ?>

							<?php if ($canEdit || $canDelete): ?>
					<th class="center">
				<?php echo JText::_('COM_FINANCIAL_UPDATECARDS_ACTIONS'); ?>
				</th>
				<?php endif; ?>

		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<?php $canEdit = $user->authorise('core.edit', 'com_financial'); ?>

							<?php if (!$canEdit && $user->authorise('core.edit.own', 'com_financial')): ?>
					<?php $canEdit = JFactory::getUser()->id == $item->created_by; ?>
				<?php endif; ?>

			<tr class="row<?php echo $i % 2; ?>">

				<?php if (isset($this->items[0]->state)): ?>
					<?php $class = ($canEdit || $canChange) ? 'active' : 'disabled'; ?>
					<td class="center">
	<a class="btn btn-micro <?php echo $class; ?>" href="<?php echo ($canEdit || $canChange) ? JRoute::_('index.php?option=&task=updatecard.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?>">
		<?php if ($item->state == 1): ?>
			<i class="icon-publish"></i>
		<?php else: ?>
			<i class="icon-unpublish"></i>
		<?php endif; ?>
	</a>
</td>
				<?php endif; ?>

								<td>
				<?php if (isset($item->checked_out) && $item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'updatecards.', $canCheckin); ?>
				<?php endif; ?>
				<a href="<?php echo JRoute::_('index.php?option=com_financial&view=updatecard&id='.(int) $item->id); ?>">
				<?php echo $this->escape($item->first_name); ?></a>
				</td>
				<td>

					<?php echo $item->last_name; ?>
				</td>
				<td>

					<?php echo $item->card_number; ?>
				</td>
				<td>

					<?php echo $item->cvv; ?>
				</td>
				<td>

					<?php echo $item->expire_month; ?>
				</td>
				<td>

					<?php echo $item->expire_year; ?>
				</td>


				<?php if (isset($this->items[0]->id)): ?>
					<td class="center hidden-phone">
						<?php echo (int) $item->id; ?>
					</td>
				<?php endif; ?>

								<?php if ($canEdit || $canDelete): ?>
					<td class="center">
						<?php if ($canEdit): ?>
							<a href="<?php echo JRoute::_('index.php?option=com_financial&task=updatecardform.edit&id=' . $item->id, false, 2); ?>" class="btn btn-mini" type="button"><i class="icon-edit" ></i></a>
						<?php endif; ?>
						<?php if ($canDelete): ?>
							<button data-item-id="<?php echo $item->id; ?>" class="btn btn-mini delete-button" type="button"><i class="icon-trash" ></i></button>
						<?php endif; ?>
					</td>
				<?php endif; ?>

			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ($canCreate): ?>
		<a href="<?php echo JRoute::_('index.php?option=com_financial&task=updatecardform.edit&id=0', false, 2); ?>"
			class="btn btn-success btn-small"><i
				class="icon-plus"></i> <?php echo JText::_('COM_FINANCIAL_ADD_ITEM'); ?></a>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<script type="text/javascript">

	jQuery(document).ready(function () {
		jQuery('.delete-button').click(deleteItem);
	});

	function deleteItem() {
		var item_id = jQuery(this).attr('data-item-id');
		if (confirm("<?php echo JText::_('COM_FINANCIAL_DELETE_MESSAGE'); ?>")) {
			window.location.href = '<?php echo JRoute::_('index.php?option=com_financial&task=updatecardform.remove&id=', false, 2) ?>' + item_id;
		}
	}
</script>


