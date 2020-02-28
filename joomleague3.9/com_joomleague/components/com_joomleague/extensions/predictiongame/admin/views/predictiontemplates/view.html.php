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

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * HTML View class for the Joomleague component
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.01a
 */

class JoomleagueViewPredictionTemplates extends JLGView
{
	function display( $tpl = null )
	{
	$app= Factory::getApplication();
    // Get a refrence of the page instance in joomla
	$document	= Factory::getDocument();
    $option 	= $app->input->getCmd('option');
    $optiontext = strtoupper($app->input->getCmd('option').'_');
    $this->optiontext = $optiontext;
    
		$prediction_id		= (int) $app->getUserState( $option . 'prediction_id' );
		$lists				= array();
		$db					= Factory::getDBO();
		//$uri				= Factory::getURI();
		$uri				= Uri::getInstance();
		$items				= $this->get( 'Data' );
		$total				= $this->get( 'Total' );
		$pagination			= $this->get( 'Pagination' );
		$predictiongame		= $this->getModel()->getPredictionGame( $prediction_id );
//echo '<pre>' . print_r( $predictiongame, true ) . '</pre>';
		$filter_order		= $app->getUserStateFromRequest( $option . 'tmpl_filter_order',		'filter_order',		'tmpl.title',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'tmpl_filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		//build the html select list for prediction games
		$predictions[] = HTMLHelper::_( 'select.option', '0', '- ' . JText::_( 'COM_JOOMLEAGUE_GLOBAL_SELECT_PRED_GAME' ) . ' -', 'value', 'text' );
		if ( $res = $this->getModel()->getPredictionGames() ) { $predictions = array_merge( $predictions, $res ); }
		$lists['predictions'] = HTMLHelper::_(	'select.genericlist',
											$predictions,
											'prediction_id',
											'class="inputbox" onChange="this.form.submit();" ',
											'value',
											'text',
											$prediction_id
										);
		unset( $res );

		// Set toolbar items for the page
        $stylelink = '<link rel="stylesheet" href="'.Uri::root().'administrator/components/com_joomleague/assets/css/jlextusericons.css'.'" type="text/css" />' ."\n";
		$document->addCustomTag($stylelink);
		JToolBarHelper::title( JText::_( 'COM_JOOMLEAGUE_ADMIN_PTMPLS_TITLE' ), 'pred-cpanel' );
        
		if ( $prediction_id > 0 )
		{
			JToolBarHelper::editList('predictiontemplate.edit');
			//JToolBarHelper::save();  // TO BE FIXED: Marked out. Better an import Button should be added here if it is not master-template
			JToolBarHelper::divider();
			if ( ( $prediction_id > 0 ) && ( $predictiongame->master_template ) )
			{
				JToolBarHelper::deleteList();
				//JToolBarHelper::deleteList( JText::_( 'Warning: all prediction-user-data and tipps of selected member will COMPLETELY be deleted!!! This is NOT reversible!!!' ) );
			}
			else
			{
				JToolBarHelper::custom( 'predictiontemplate.reset', 'restore', 'restore', JText::_( 'COM_JOOMLEAGUE_GLOBAL_RESET' ), true );
			}
			JToolBarHelper::divider();
		}
		//JLToolBarHelper::onlinehelp();

		$this->user = Factory::getUser();
		$this->pred_id = $prediction_id;
		$this->lists = $lists;
		$this->items = $items;
		$this->pagination = $pagination;
		$this->predictiongame = $predictiongame;
		$url=$uri->toString();
		$this->request_url = $url;
		parent::display( $tpl );
	}

}
?>