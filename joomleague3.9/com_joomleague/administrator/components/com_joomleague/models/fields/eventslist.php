<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('list');

/**
 * Eventslist form field class
*/
class JFormFieldEventslist extends JFormFieldList
{
	/**
	 * field type
	 * @var string
	 */
	public $type = 'Eventslist';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();
		
		//automigrate current saved values to the new field format (array)
		if(is_array($this->value) === FALSE && $this->value !='') {
			$this->value = explode(',', $this->value);
		}
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
			
		$query->select('id AS value');
		$query->select('CASE LENGTH(name) when 0 then CONCAT('.$db->Quote(JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT')). ', " ", id)	else name END as text ');
		$query->from('#__joomleague_eventtype');
		$query->order('id');
		$db->setQuery($query);
		$options = $db->loadObjectList();
		
		foreach($options as $k) {
			if(strpos($k->text,"COM_JOOMLEAGUE_E")!==false) {
				$k->text = JTEXT::_($k->text);
			}
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
