<?php 
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JoomleagueViewPredictionHeading extends JLGView
{
	function display($tpl=null)
	{
	$document =  Factory::getDocument();
        $app = Factory::getApplication();		
	$js ="registerhome('".Uri::base()."','Prediction Game Extension','".$app->getCfg('sitename')."','0');". "\n";
    $document->addScriptDeclaration( $js );	
    
        parent::display($tpl);
	}

}
?>