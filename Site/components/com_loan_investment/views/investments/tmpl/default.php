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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'ar-body-wrapper select');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canCreate  = $user->authorise('core.create', 'com_loan_investment') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'investmentform.xml');
$canEdit    = $user->authorise('core.edit', 'com_loan_investment') && file_exists(JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'forms' . DIRECTORY_SEPARATOR . 'investmentform.xml');
$canCheckin = $user->authorise('core.manage', 'com_loan_investment');
$canChange  = $user->authorise('core.edit.state', 'com_loan_investment');
$canDelete  = $user->authorise('core.delete', 'com_loan_investment');
?>

<form action="<?php echo JRoute::_('index.php?option=com_loan_investment&view=investments'); ?>" method="post" name="adminForm" id="adminForm">

	<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
	<table class="table table-striped" id="investmentList">
		<thead>
			<tr>
				<?php if (isset($this->items[0]->state)): ?>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<?php endif; ?>

				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_PROVIDER_NAME', 'a.provider_name', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_LOAN_DISPLAY_NAME', 'a.loan_display_name', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_MAXIMUM_LVR', 'a.maximum_lvr', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_LOAN_TERM', 'a.loan_term', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_BORROWING_AMOUNT_RANGE', 'a.borrowing_amount_range', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_REFINANCE', 'a.refinance', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_LINE_OF_CREDIT', 'a.line_of_credit', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_SELF_MANAGED_SUPER', 'a.self_managed_super', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_INTEREST_RATE_STRUCTURE', 'a.interest_rate_structure', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_INTEREST_ONLY', 'a.interest_only', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_LOAN_ALLOWS_SPLIT_INTEREST_RATE', 'a.loan_allows_split_interest_rate', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_PRINCIPAL_INTEREST', 'a.principal_interest', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_STATES_APPLICABLE', 'a.states_applicable', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_REDRAW_FACILITY', 'a.redraw_facility', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_REDRAW_FEE', 'a.redraw_fee', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_EXTRA_REPAYMENTS', 'a.extra_repayments', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_WEEKLY_REPAYMENTS', 'a.weekly_repayments', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_FORTNIGHTLY_REPAYMENTS', 'a.fortnightly_repayments', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_MONTHLY_REPAYMENTS', 'a.monthly_repayments', $listDirn, $listOrder); ?>
				</th>
				<th class=''>
				<?php echo JHtml::_('grid.sort',  'COM_LOAN_INVESTMENT_INVESTMENTS_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
				</th>


				<?php if ($canEdit || $canDelete): ?>
					<th class="center">
				<?php echo JText::_('COM_LOAN_INVESTMENT_INVESTMENTS_ACTIONS'); ?>
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
				<?php $canEdit = $user->authorise('core.edit', 'com_loan_investment'); ?>
				<?php if (!$canEdit && $user->authorise('core.edit.own', 'com_loan_investment')): ?>
				<?php $canEdit = JFactory::getUser()->id == $item->created_by; ?>
				<?php endif; ?>

				<tr class="row<?php echo $i % 2; ?>">

				<?php if (isset($this->items[0]->state)) : ?>
					<?php $class = ($canChange) ? 'active' : 'disabled'; ?>
					<td class="center">
						<a class="btn btn-micro <?php echo $class; ?>" href="<?php echo ($canChange) ? JRoute::_('index.php?option=com_loan_investment&task=investment.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2), false, 2) : '#'; ?>">
						<?php if ($item->state == 1): ?>
							<i class="icon-publish"></i>
						<?php else: ?>
							<i class="icon-unpublish"></i>
						<?php endif; ?>
						</a>
					</td>
				<?php endif; ?>

					<td>
						<?php echo $item->id; ?>
					</td>
					<td>
						<?php if (isset($item->checked_out) && $item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'investments.', $canCheckin); ?>
						<?php endif; ?>
						<a href="<?php echo JRoute::_('index.php?option=com_loan_investment&view=investment&id='.(int) $item->id); ?>">
						<?php echo $this->escape($item->provider_name); ?></a>
					</td>
					<td>

						<?php echo $item->loan_display_name; ?>
					</td>
					<td>

						<?php echo $item->maximum_lvr; ?>
					</td>
					<td>

						<?php echo $item->loan_term; ?>
					</td>
					<td>

						<?php echo $item->borrowing_amount_range; ?>
					</td>
					<td>

						<?php echo $item->refinance; ?>
					</td>
					<td>

						<?php echo $item->line_of_credit; ?>
					</td>
					<td>

						<?php echo $item->self_managed_super; ?>
					</td>
					<td>

						<?php echo $item->interest_rate_structure; ?>
					</td>
					<td>

						<?php echo $item->interest_only; ?>
					</td>
					<td>

						<?php echo $item->loan_allows_split_interest_rate; ?>
					</td>
					<td>

						<?php echo $item->principal_interest; ?>
					</td>
					<td>

						<?php echo $item->states_applicable; ?>
					</td>
					<td>

						<?php echo $item->redraw_facility; ?>
					</td>
					<td>

						<?php echo $item->redraw_fee; ?>
					</td>
					<td>

						<?php echo $item->extra_repayments; ?>
					</td>
					<td>

						<?php echo $item->weekly_repayments; ?>
					</td>
					<td>

						<?php echo $item->fortnightly_repayments; ?>
					</td>
					<td>

						<?php echo $item->monthly_repayments; ?>
					</td>
					<td>
						<?php echo JFactory::getUser($item->created_by)->name; ?>				
					</td>
					<?php if ($canEdit || $canDelete): ?>
					<td class="center">
						<?php if ($canEdit): ?>
							<a href="<?php echo JRoute::_('index.php?option=com_loan_investment&task=investmentform.edit&id=' . $item->id, false, 2); ?>" class="btn btn-mini" type="button"><i class="icon-edit" ></i></a>
						<?php endif; ?>
						<?php if ($canDelete): ?>
							<a href="<?php echo JRoute::_('index.php?option=com_loan_investment&task=investmentform.remove&id=' . $item->id, false, 2); ?>" class="btn btn-mini delete-button" type="button"><i class="icon-trash" ></i></a>
						<?php endif; ?>
					</td>
				<?php endif; ?>

			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ($canCreate) : ?>
		<a href="<?php echo JRoute::_('index.php?option=com_loan_investment&task=investmentform.edit&id=0', false, 2); ?>"
		   class="btn btn-success btn-small"><i
				class="icon-plus"></i>
			<?php echo JText::_('COM_LOAN_INVESTMENT_ADD_ITEM'); ?></a>
	<?php endif; ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php if($canDelete) : ?>
<script type="text/javascript">

	jQuery(document).ready(function () {
		jQuery('.delete-button').click(deleteItem);
	});

	function deleteItem() {

		if (!confirm("<?php echo JText::_('COM_LOAN_INVESTMENT_DELETE_MESSAGE'); ?>")) {
			return false;
		}
	}
</script>
<?php endif; ?>
