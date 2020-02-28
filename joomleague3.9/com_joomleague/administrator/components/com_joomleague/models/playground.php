<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;


/**
 * Playground Model
 */
class JoomleagueModelPlayground extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.playground';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param object $record	A record object.
	 *
	 * @return boolean True 	if allowed to delete the record.
	 * Defaults to the	permission for the component.
	 */
	protected function canDelete($record)
	{
		if(!empty($record->id))
		{
			$user = Factory::getUser();
	
			if($user->authorise('core.admin','com_joomleague')
					|| $user->authorise('core.delete','com_joomleague')
					|| $user->authorise('core.delete','com_joomleague.playground.'.$id))
			{
				return true;
			} else {
				return false;
			}
		}
	}
	
	
	/**
	 * Method to remove venues
	 * @todo add check for assigned playground to club
	 *
	 * @access public
	 * @return boolean on success
	 */
	public function delete(&$pks = array())
	{
		$return = array();
		if($pks)
		{
			$pksTodelete = array();
			$errorNotice = array();
			$db = Factory::getDbo();
			foreach($pks as $pk)
			{
				$result = array();
				
				// check entry Project-Team Table
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__joomleague_project_team');
				$query->where('standard_playground = '.$pk);
				$db->setQuery($query);
				if($db->loadResult())
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_VENUE_MODEL_ERROR_P_TEAM_EXISTS');
				}
				
				// check entry Match-Table
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__joomleague_match');
				$query->where('playground_id = '.$pk);
				$db->setQuery($query);
				if($db->loadResult())
				{
					$result[] = Text::_('COM_JOOMLEAGUE_ADMIN_VENUE_MODEL_ERROR_MATCH_EXISTS');
				}

				if($result)
				{
					$pkInfo = array("id:".$pk);
					$result = array_merge($pkInfo,$result);
					$errorNotice[] = $result;
				}
				else
				{
					$pksTodelete[] = $pk;
				}
			}
			
			if($pksTodelete)
			{
				$return['removed'] = parent::delete($pksTodelete);
				$return['removedCount'] = count($pksTodelete);
			}
			else
			{
				$return['removed'] = false;
				$return['removedCount'] = false;
			}
			
			if($errorNotice)
			{
				$return['error'] = $errorNotice;
			}
			else
			{
				$return['error'] = false;
			}
			
			return $return;
		}
		
		$return['removed'] = false;
		$return['error'] = false;
		$return['removedCount'] = false;
		
		return $return;
	}


	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type The table type to instantiate
	 * @param	string A prefix for the table class name. Optional.
	 * @param	array Configuration array for model. Optional.
	 * @return Table database object
	 */
	public function getTable($type = 'Playground',$prefix = 'Table',$config = array())
	{
		return Table::getInstance($type,$prefix,$config);
	}


	/**
	 * Method to get the record form.
	 *
	 * @param array $data		the form.
	 * @param boolean $loadData	the form is to load its own data (default case), false if not.
	 * @return mixed JForm object on success, false on failure
	 */
	public function getForm($data = array(),$loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joomleague.playground','playground',array('control' => 'jform','load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}
		
		$input = Factory::getApplication()->input;
		
		if($this->getState('playground.id'))
		{
			$pk = $this->getState('playground.id');
			$item = $this->getItem($pk);
		} else {
			$params		 	=	ComponentHelper::getParams('com_joomleague');
			$ph_team		=	$params->get('ph_team','images/com_joomleague/database/placeholders/placeholder_150_2.png');
			$form->setFieldAttribute('picture', 'default',$ph_team);
		}
		

		return $form;
	}


	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return mixed data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = Factory::getApplication();
		$data = $app->getUserState('com_joomleague.edit.playground.data',array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	
	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   Table  $table  A JTable object.
	 *
	 * @return  void
	 */
	protected function prepareTable($table)
	{
		$date = Factory::getDate();
		$user = Factory::getUser();
	
		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true)
				->select('MAX(ordering)')
				->from('#__joomleague_playground');
	
				$db->setQuery($query);
				$max = $db->loadResult();
	
				$table->ordering = $max + 1;
			}
		}
		else
		{
			// Set the values
			$table->modified    = $date->toSql();
			$table->modified_by = $user->get('id');
		}
	}
	

	/**
	 * Method to save the form data.
	 *
	 * @param array $data	The form data.
	 *
	 * @return boolean True on success.
	 */
	public function save($data)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		
		$data['extended'] = $input->get('extended',array(),'array');
		
		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName().'.id');
			$item = $this->getItem($pk);

			return true;
		}

		return false;
	}
}
