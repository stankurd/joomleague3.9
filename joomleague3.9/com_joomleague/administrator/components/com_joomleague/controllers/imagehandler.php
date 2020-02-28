<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;

//use Joomla\CMS\Filter\InputFilter;
//jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');
//require_once (JPATH_COMPONENT.DS.'helpers'.DS.'imageselect.php');
require_once (JPATH_COMPONENT_SITE.DS.'helpers'.DS.'imageselect.php');

/**
 * sportsmanagementControllerImagehandler
 * 
 * @package 
 * @author Dieter Plöger
 * @copyright 2018
 * @version $Id$
 * @access public
 */
class JoomleagueControllerImagehandler extends JLGControllerAdmin
{
	/**
	 * Constructor
	 *
	 * @since 0.9
	 */
	function __construct()
	{
		parent::__construct();

		// Register Extra task
	}

	/**
	 * logic for uploading an image
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function upload()
	{
		// Reference global application object
        $app = Factory::getApplication();
        // JInput object
        //$jinput = $app->input;
        //$option = $this->jinput->getCmd('option');

$type = ''; 
$msg = ''; 		
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		//$file	= $this->jinput->getVar( 'userfile', '', 'files', 'array' );
        $file = $this->jinput->files->get('userfile');
		//$task	= $this->jinput->getVar( 'task' );
		$type	= $this->jinput->getVar( 'type' );
		$folder	= ImageSelectJL::getfolder($type);
		$field	= $this->jinput->getVar( 'field' );
		$linkaddress	= $this->jinput->getVar( 'linkaddress' );
		// Set FTP credentials, if given
		jimport( 'joomla.client.helper' );
		ClientHelper::setCredentialsFromRequest( 'ftp' );
		//$ftp = ClientHelper::getCredentials( 'ftp' );

		$base_Dir = JPATH_SITE . DS . 'images' . DS . $this->option . DS .'database'.DS. $folder . DS;
        
    //do we have an imagelink?
    if ( !empty( $linkaddress ) )
    {
    $file['name'] = basename($linkaddress);
    
if (preg_match("/dfs_/i", $linkaddress)) 
{
$filename = $file['name'];
}
else
{
//sanitize the image filename
$filename = ImageSelect::sanitize( $base_Dir, $file['name'] );
}

		
		$filepath = $base_Dir . $filename;
		
if ( !copy($linkaddress,$filepath) )
{
echo "<script> alert('".Text::_( 'COM_SPORTSMANAGEMENT_ADMIN_IMAGEHANDLER_COPY_FAILED' )."'); window.history.go(-1); </script>\n";
//$app->close();
}
else
{
//echo "<script> alert('" . Text::_( 'COPY COMPLETE'.'-'.$folder.'-'.$type.'-'.$filename.'-'.$field ) . "'); window.history.go(-1); window.parent.selectImage_".$type."('$filename', '$filename','$field'); </script>\n";
echo "<script>  window.parent.selectImage_".$type."('$filename', '$filename','$field');window.parent.SqueezeBox.close(); </script>\n";
//$app->close();
}

    
    }
    
		//do we have an upload?
		if ( empty( $file['name'] ) )
		{
			echo "<script> alert('".Text::_( 'COM_SPORTSMANAGEMENT_ADMIN_IMAGEHANDLER_CTRL_IMAGE_EMPTY' )."'); window.history.go(-1); </script>\n";
			//$app->close();
		}

		//check the image
		$check = ImageSelect::check( $file );

		if ( $check === false )
		{
			$app->redirect( $_SERVER['HTTP_REFERER'] );
		}

		//sanitize the image filename
		$filename = ImageSelect::sanitize( $base_Dir, $file['name'] );
		$filepath = $base_Dir . $filename;

		//upload the image
		if ( !JFile::upload( $file['tmp_name'], $filepath ) )
		{
			echo "<script> alert('".Text::_( 'COM_SPORTSMANAGEMENT_ADMIN_IMAGEHANDLER_CTRL_UPLOAD_FAILED' )."'); window.history.go(-1); </script>\n";
			//$app->close();
$msg = Text::_( 'COM_SPORTSMANAGEMENT_ADMIN_IMAGEHANDLER_CTRL_UPLOAD_FAILED' );
$type = 'error'; 			
		}
		else
		{
//			echo "<script> alert('" . Text::_( 'COM_SPORTSMANAGEMENT_ADMIN_IMAGEHANDLER_CTRL_UPLOAD_COMPLETE'.'-'.$folder.'-'.$type.'-'.$filename.'-'.$field ) . "'); window.history.go(-1); window.parent.selectImage_".$type."('$filename', '$filename','$field'); </script>\n";
//			echo "<script> alert('" . Text::_( 'COM_SPORTSMANAGEMENT_ADMIN_IMAGEHANDLER_CTRL_UPLOAD_COMPLETE' ) . "'); window.history.go(-1); window.parent.selectImage_".$type."('$filename', '$filename','$field'); </script>\n";
      echo "<script>  window.parent.selectImage_".$type."('$filename', '$filename','$field');window.close(); </script>\n";
			$msg = Text::_( 'COM_SPORTSMANAGEMENT_ADMIN_IMAGEHANDLER_CTRL_UPLOAD_COMPLETE' );
			$type = 'notice'; 
			//$app->close();
		}

	}

	/**
	 * logic to mass delete images
	 *
	 * @access public
	 * @return void
	 * @since 0.9
	 */
	function delete()
	{
		// Reference global application object
        $app = Factory::getApplication();
        // JInput object
        //$jinput = $app->input;
        //$option = $jinput->getCmd('option');
		// Set FTP credentials, if given
		jimport( 'joomla.client.helper' );
		ClientHelper::setCredentialsFromRequest( 'ftp' );

		// Get some data from the request
		$images	= $this->jinput->getVar( 'rm', array(), '', 'array' );
		$type	= $this->jinput->getVar( 'type' );

		$folder	= ImageSelectJL::getfolder( $type );

		if ( count( $images ) )
		{
			foreach ( $images as $image )
			{
				/*
				if ( $image !== InputFilter::clean( $image, 'path' ) )
				{
					JError::raiseWarning( 100, Text::_( 'COM_SPORTSMANAGEMENT_ADMIN_IMAGEHANDLER_CTRL_UNABLE_TO_DELETE' ) . ' ' . htmlspecialchars( $image, ENT_COMPAT, 'UTF-8' ) );
					continue;
				}
*/
				$fullPath = Path::clean( JPATH_SITE . DS . 'images' . DS . $this->option . DS .'database'.DS. $folder . DS . $image );
				$fullPaththumb = Path::clean( JPATH_SITE . DS . 'images' . DS . $this->option . DS .'database'.DS. $folder . DS . 'small' . DS . $image );
				if ( is_file( $fullPath ) )
				{
					JFile::delete( $fullPath );
					if ( JFile::exists( $fullPaththumb ) )
					{
						JFile::delete( $fullPaththumb );
					}
				}
			}
		}

		$app->redirect( 'index.php?option='.$this->option.'&view=imagehandler&type=' . $type . '&tmpl=component' );
	}

}
?>
