<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldPlaygrounds extends JFormFieldList
{

	public $type = 'playgrounds';
	
	
	protected function getOptions()
	{
		// Initialise variables.
		$options 	= array();
		$published	= $this->element['published']? $this->element['published'] : array(0,1);
		$name		= (string) $this->element['name'];
		
		// Let's get the id for the current item
		$jinput 	= JFactory::getApplication()->input;
		
		// language
		$lang = JFactory::getLanguage();
		$extension = "com_joomleague";
		$source = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		
		// Create SQL
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select(array('pl.id AS value', 'pl.name AS text'));
		$query->from('#__joomleague_playground AS pl');
		
		/*
		// Filter on the published state
		if (is_numeric($published))
		{
			$query->where('pl.published = ' . (int) $published);
		}
		elseif (is_array($published))
		{
			JArrayHelper::toInteger($published);
			$query->where('pl.published IN (' . implode(',', $published) . ')');
		}
		*/
		
		$query->group('pl.id');
		$query->order('pl.name');
		
		// Get the options.
		$db->setQuery($query);
		
		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage);
		}
		
		/*
		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			if ($options[$i]->published == 1) {
		
			} else {
		
			}
		}
		*/
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		
		return $options;
	}
}
 