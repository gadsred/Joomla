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
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_loan_investment/css/form.css');

?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'investmentprovider.cancel') {
			Joomla.submitform(task, document.getElementById('investmentprovider-form'));
		}
		else {
			
			if (task != 'investmentprovider.cancel' && document.formvalidator.isValid(document.id('investmentprovider-form'))) {
				
				Joomla.submitform(task, document.getElementById('investmentprovider-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_loan_investment&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="investmentprovider-form" class="form-validate">

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_LOAN_INVESTMENT_TITLE_INVESTMENTPROVIDER', true)); ?>
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">

									<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<?php echo $this->form->renderField('provider_name'); ?>
				<?php //echo $this->form->renderField('provider_logo'); ?>
				<div class="control-label">
					<label id="jform_provider_logo-lbl" for="jform_provider_logo" class="">
						<?php echo JText::_("COM_LOAN_INVESTMENT_INVESTMENTPROVIDERS_PROVIDER_LOGO");?>
					</label>
				</div>
				<div class="controls">
					<img src="<?php echo ($this->item->provider_logo)? $this->item->provider_logo :'http://723bb8f93f13efb0e739-db6ecb33749351ecca7d660da3560f85.r57.cf4.rackcdn.com/login_page_logo.png';?>">
				</div>
				<?php //echo $this->form->renderField('logo'); ?>
				<div class="controls">
					<input id="uplogo" name="filename" type="file" accept="image/.gif, .jpg, .jpeg, .png" /> 
				</div>
				<?php echo $this->form->renderField('website'); ?>
				<?php echo $this->form->renderField('provider_type'); ?>
				<?php echo $this->form->renderField('provider_logo'); ?>
					<?php if ($this->state->params->get('save_history', 1)) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
						<div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
					</div>
					<?php endif; ?>
				</fieldset>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php if (JFactory::getUser()->authorise('core.admin','loan_investment')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL', true)); ?>
		<?php echo $this->form->getInput('rules'); ?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
<?php endif; ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>

		<input type="hidden" id="option" name="option" value=""/>
		<input type="hidden" id="task" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>

	</div>
</form>
<script>

(function( $ ) {
	$(document).ready(function(){
		$('.btn-success').prop('disabled',true);
		$('#uplogo').change(function(){
			
			$('#investmentprovider-form').attr('action','<?php echo '/administrator/index.php?option=com_loan_investment&task=UploadBankLogo'; ?>');
			$('#option').val('com_loan_investment');
			$('#task').val('UploadBankLogo');
			$('#investmentprovider-form').submit();
					//var  formData = jQuery('#investmentprovider-form').serialize();
					// var fd = new FormData();
					// fd.append("fileToUpload",jQuery('#uplogo')[0].files[0]);
					// jQuery.ajax({
						// type: "POST",
						// url: "http://www.propertyinvestorsonly.com.au/index.php?option=com_loan_investment&task=UploadBankLogo",
						// data: fd,
						// //dataType: "json",
						// processData: false,  // tell jQuery not to process the data
						// contentType: false,  // tell jQuery not to set contentType
						// success: function(data) {
							// alert(data);
						// },
						// error: function() {
							// alert('Error Uploading Image, Plz. check you connection.');
						// }
					// });
		});
		
		$('#jform_website, select').change(function(){
			
			$('#investmentprovider-form').attr('action','<?php echo '/administrator/index.php?option=com_loan_investment&task=UploadBankLogo'; ?>');
			$('#option').val('com_loan_investment');
			$('#task').val('Update_provider');
			$('#investmentprovider-form').submit();
			//event.preventDefault();
		});
	});
})(jQuery);
</script>