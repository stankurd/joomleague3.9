<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\String\StringHelper;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once (JPATH_COMPONENT.'/models/list.php');

/**
 * Joomleague Component Seasons Model
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelpredictiongroups extends JoomLeagueModelList
{
	var $_identifier = "predictiongroups";
	
	function _buildQuery()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		// Get the WHERE and ORDER BY clauses for the query
		$where=$this->_buildContentWhere();
		$orderby=$this->_buildContentOrderBy();
        
        // Create a new query object.
        $query = $db->getQuery(true);
        $query->select(array('s.*', 'u.name AS editor'))
        ->from('#__joomleague_prediction_groups AS s')
        ->join('LEFT', '#__users AS u ON u.id = s.checked_out');

        if ($where)
        {
            $query->where($where);
        }
        if ($orderby)
        {
            $query->order($orderby);
        }

		
		return $query;
	}

	function _buildContentOrderBy()
	{
	    $app = Factory::getApplication();
		$option = $app->input->getCmd('option');
		$filter_order		= $app->getUserStateFromRequest($option.'s_filter_order',		'filter_order',		's.ordering',	'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest($option.'s_filter_order_Dir',	'filter_order_Dir',	'',				'word');

		if ($filter_order=='s.ordering')
		{
			$orderby='s.ordering '.$filter_order_Dir;
		}
		else
		{
			$orderby=''.$filter_order.' '.$filter_order_Dir.',s.ordering ';
		}
		return $orderby;
	}

	function _buildContentWhere()
	{
	$app = Factory::getApplication();
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
	$option = $app->input->getCmd('option');

		$filter_order		= $app->getUserStateFromRequest($option.'s_filter_order',		'filter_order',		's.ordering',	'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest($option.'s_filter_order_Dir',	'filter_order_Dir',	'',				'word');
		$search				= $app->getUserStateFromRequest($option.'s_search',			'search',			'',				'string');
		$search=StringHelper::strtolower($search);
		$where=array();
		if ($search)
		{
			$where[]='LOWER(s.name) LIKE '.$db->Quote('%'.$search.'%');
		}
		$where=(count($where) ? ''.implode(' AND ',$where) : '');
		return $where;
	}

	/**
	* Method to return a season array (id, name)
	*
	* @access	public
	* @return	array seasons
	* @since	1.5.0a
	*/
	function getSeasons()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$query = 'SELECT id, name FROM #__joomleague_prediction_groups ORDER BY name ASC ';
		$db->setQuery($query);
		if (!$result = $db->loadObjectList())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		return $result;
	}
}
?>