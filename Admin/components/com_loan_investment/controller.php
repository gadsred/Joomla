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

/**
 * Class Loan_investmentController
 *
 * @since  1.6
 */
class Loan_investmentController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   mixed    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return   JController This object to support chaining.
	 *
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view = JFactory::getApplication()->input->getCmd('view', 'investments');
		JFactory::getApplication()->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
	
	public function UploadBankLogo()
	{	
		jimport( 'joomla.application.application' );
		$app = JFactory::getApplication();
		
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');
		//var_dump($data);exit();
		//$data = JFactory::getApplication()->input->get('jform', array(), 'array');
		$jinput = JFactory::getApplication()->input;
		
		$title=$jinput->get('title', '', '');
		$name=$jinput->get('name', '', '');
		
		$file=JRequest::getVar('filename','', 'files', 'array');
		
		$user=JFactory::getUser()->id;
		
		//---gads upload cloud---//
		//require JURI::root() . 'uploadtocloud/cloudsapi/cloudfiles.php';
		require '../uploadtocloud/cloudsapi/cloudfiles.php';
		$filename= $data['id'].time();
		$localfile= $file['tmp_name'];
		if(!exif_imagetype($localfile))
		{
			echo "Invalid Image file format. Make sure you choose image file for your Logo to be uploaded.";
			$app->redirect('/administrator/index.php?option=com_loan_investment&view=investmentprovider&layout=edit&id='.$data['id'],"error","Uploadfail.");
		}
		//exit();
		$container_name= "pio-logos";
		$username= "piobruno";
		$cloud_api_key="f8d5ddb9e8ced2ee727e339415110074";
		
		//set the Clouds Authentication.
		$auth = new CF_Authentication($username,$cloud_api_key);
		$auth->authenticate();
		$connection = new CF_Connection($auth);	
		//get the appropriate container
		$container = $connection->get_container($container_name);
		
		$public_uri = $container->make_public();
		$public_uri_new="http://1ceeeb962de2fb479664-aa2ec9281ecacb9bdc19e4afbe7e84c8.r8.cf1.rackcdn.com";
		
		$object = $container->create_object($filename);
		
		if($res = $object->load_from_filename($localfile))
		{
			$db=JFactory::getDBO();
			$query="Update #__loan_investment_providers Set provider_logo='".$public_uri_new."/".$filename."' where id='".$data['id']."'";
			$db->setQuery($query);
			$db->execute();
			$app->redirect('/administrator/index.php?option=com_loan_investment&view=investmentprovider&layout=edit&id='.$data['id'],"Image Uploaded.");
	
		}
		else
		{
			var_dump($res);exit();
		}
		
	}
	
	public function Update_provider()
	{
		$data = JFactory::getApplication()->input->get('jform', array(), 'array');
		$app = JFactory::getApplication();
		
		$db=JFactory::getDBO();
		$query="Update #__loan_investment_providers Set website='".$data['website']."', provider_type = '".$data['provider_type']."'  where id='".$data['id']."'";
		$db->setQuery($query);
		$res = $db->execute();
		$app->redirect('/administrator/index.php?option=com_loan_investment&view=investmentprovider&layout=edit&id='.$data['id'],"Record Updated.");
	}
}
