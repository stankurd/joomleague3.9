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
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once (JPATH_COMPONENT.DS.'models'.DS.'item.php');

/**
 * Joomleague Component Season Model
 *
 * @author	Julien Vonthron <julien.vonthron@gmail.com>
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelpredictiongroup extends JoomLeagueModelItem
{
	/**
	 * Method to remove a season
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function delete($pks=array())
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$app	= Factory::getApplication();
		$result=false;
		if (count($pks))
		{
			//JArrayHelper::toInteger($cid);
			/*
            $cids=implode(',',$pks);
			$app->enqueueMessage(JText::_('JoomleagueModelSeason-delete id->'.$cids),'Notice');
			$query="SELECT id FROM #__joomleague_project WHERE season_id IN ($cids)";
			$db->setQuery($query);
			if ($db->loadResult())
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_SEASON_MODEL_ERROR_PROJECT_EXISTS'));
				return false;
			}
            */
			return parent::delete($pks);
		}
		return true;
	}

	/**
	 * Method to load content season data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadData()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query='SELECT * FROM #__joomleague_prediction_groups WHERE id='.(int) $this->_id;
			$db->setQuery($query);
			$this->_data=$db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the season data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$season						= new stdClass();
			$season->id					= 0;
			$season->name				= null;
			$season->alias				= null;
			$season->checked_out		= 0;
			$season->checked_out_time	= 0;
			$season->extended			= null;
			$season->ordering			= 0;
			$season->modified			= null;
			$season->modified_by		= null;
			
			$this->_data				= $season;

			return (boolean) $this->_data;
		}

		return true;
	}
	
	/**
	 * Method to add a new season if not already exists
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 **/
	function addSeason($newseason)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		//check if season exists. If not add a new season to table
		$query="SELECT * FROM #__joomleague_prediction_groups WHERE name='$newseason'";
		$db->setQuery($query);
		if ($seasonObject=$db->loadObject())
		{
			//season already exists
			return $seasonObject->id;
		}
		//season does NOT exist and has to be created
		$p_season = $this->getTable();
		$p_season->set('name',$newseason);
		if (!$p_season->store())
		{
			$seasonObject->id=0;
		}
		else
		{
			$seasonObject->id=$db->insertid(); //mysql_insert_id();
		}
		return $seasonObject->id;
	}
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	Table	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'predictiongroups', $prefix = 'table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.7
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joomleague.'.$this->name, $this->name,
				array('load_data' => $loadData) );
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.7
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_joomleague.edit.'.$this->name.'.data', array());
		if (empty($data))
		{
			$data = $this->getData();
		}
		return $data;
	}
}
?>