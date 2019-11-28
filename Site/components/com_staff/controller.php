<?php

/**
 * @version     1.0.0
 * @package     com_staff
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
jimport( 'joomla.application.application' );
class StaffController extends JControllerLegacy {

    /**
     * Method to display a view.
     *
     * @param	boolean			$cachable	If true, the view output will be cached
     * @param	array			$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return	JController		This object to support chaining.
     * @since	1.5
     */
    public function display($cachable = false, $urlparams = false) {
        require_once JPATH_COMPONENT . '/helpers/staff.php';

        $view = JFactory::getApplication()->input->getCmd('view', 'staffs');
        JFactory::getApplication()->input->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }

	function upimage()
	{jimport( 'joomla.application.application' );
		$app = JFactory::getApplication();
		
		$data=JRequest::get( 'post' );
		$jinput = JFactory::getApplication()->input;
		
		$title=$jinput->get('title', '', '');
		$name=$jinput->get('name', '', '');
		$file=JRequest::getVar('filename','', 'files', 'array');
		$user=JFactory::getUser()->id;
		
		//---gads upload cloud---//
		require JPATH_BASE . '/uploadtocloud/cloudsapi/cloudfiles.php';
		
		$filename= $user.'-'.$name.time();
		
		$localfile= $file['tmp_name'];
		if(!exif_imagetype($localfile))
		{
			$app->redirect(JRoute::_('index.php?option=com_staff&view=staffform&layout=edit&Itemid=221'),"Invalid Image file format. Make sure you choose image file for your Photo to be uploaded.","error");
		}
		$container_name= "pcd-staff-photos";
		$username= "piobruno";
		$cloud_api_key="f8d5ddb9e8ced2ee727e339415110074";
		
		//set the Clouds Authentication.
		$auth = new CF_Authentication($username,$cloud_api_key);
		$auth->authenticate();
		$connection = new CF_Connection($auth);	
		//get the appropriate container
		$container = $connection->get_container($container_name);
		$public_uri = $container->make_public();
		$public_uri_new="https://fb40f2b7bfb26e4d4d15-4cd0c9f2d4e37bb3c4bf33aaa42f24ff.ssl.cf1.rackcdn.com";
		// if(file_exists($localfile)) {
			// $objects = $container->list_objects();
			// if (count($objects)>0) {
				// //$filename=$this->checkFileName($objects, $filename,$filename,1);
				// /*echo $filename;
				// exit;*/
			// }
		//echo $public_uri.'<br />';
		//echo $filename;exit;
			$object = $container->create_object($filename);
			$object->load_from_filename($localfile);
			
		// }
	
		$session =& JFactory::getSession();
		$session->clear('image_cloud');
		$session->set( 'image_cloud',$public_uri_new.'/'.$filename);
		$session->set( 'name',$name);
		$session->set( 'title',$title);
		
		$app->redirect(JRoute::_('index.php?option=com_staff&view=staffform&layout=edit&Itemid=221'),"Photo was successfully uploaded!!! Press Submit to complete the process.");
	}
	
	//gads upload logo to cloud
	public function uplogo(){
		jimport( 'joomla.application.application' );
		$app = JFactory::getApplication();
		
		$data=JRequest::get( 'post' );
		$jinput = JFactory::getApplication()->input;
		
		$title=$jinput->get('title', '', '');
		$name=$jinput->get('name', '', '');
		$file=JRequest::getVar('filename','', 'files', 'array');
		$user=JFactory::getUser()->id;
		
		//---gads upload cloud---//
		require JPATH_BASE . '/uploadtocloud/cloudsapi/cloudfiles.php';
		
		$filename= $data['link_id'].time();
		$localfile= $file['tmp_name'];
		if(!exif_imagetype($localfile))
		{
			$app->redirect("index.php?option=com_mtree&task=editlisting&Itemid=218&link_id={$data['link_id']}","Invalid Image file format. Make sure you choose image file for your Logo to be uploaded.","error");
		}
		//exit();
		$container_name= "pcd-company-logo-new";
		$username= "piobruno";
		$cloud_api_key="f8d5ddb9e8ced2ee727e339415110074";
		
		//set the Clouds Authentication.
		$auth = new CF_Authentication($username,$cloud_api_key);
		$auth->authenticate();
		$connection = new CF_Connection($auth);	
		//get the appropriate container
		$container = $connection->get_container($container_name);
		
		$public_uri = $container->make_public();
		$public_uri_new="https://3a514c8d6ec4579f81ef-22115b8757a250a8da6d14f1654778fc.ssl.cf1.rackcdn.com";
		

		// if(file_exists($localfile)) {
			// $objects = $container->list_objects();
			// if (count($objects)>0) {
				// $filename=$this->checkFileName($objects, $filename,$filename,1);
				// /*echo $filename;
				// exit;*/
			// }
		
		//echo $public_uri.'<br />';
		//echo $filename;exit;
			$object = $container->create_object($filename);
			$object->load_from_filename($localfile);
			
			// if($public_uri)
			// {
				$db=JFactory::getDBO();
				$query="Update #__mt_links Set logo='".$public_uri_new."/".$filename."' where link_id='".$data['link_id']."'";
				$db->setQuery($query);
				$db->execute();
				
				//var_dump($public_uri."/".$filename);
			// }
			// else
			// {
				//echo 'Invalid Upload! Make sure you upload image format file';
			// }
			
		// }
	
		// $session =& JFactory::getSession();
		// $session->set( 'image_cloud',$public_uri.'/'.$filename);
		// $session->set( 'name',$name);
		// $session->set( 'title',$title);
		
		$app->redirect("index.php?option=com_mtree&task=editlisting&Itemid=218&link_id={$data['link_id']}","Company logo was successfully uploaded");
	}
}
