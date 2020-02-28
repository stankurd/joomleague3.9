<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

class JFormFieldPosition extends JFormField
{
	protected $type = 'position';

	function getInput()
	{
		$required 	= $this->element['required'] == "true" ? 'true' : 'false';
		$result = array();
		$db = JFactory::getDbo();
		$lang = JFactory::getLanguage();
		$extension = "com_joomleague";
		$source 	= JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		
		$query='SELECT	pos.id,
						pos.name AS name
					FROM #__joomleague_position pos
					INNER JOIN #__joomleague_sports_type AS s ON s.id=pos.sports_type_id
					WHERE pos.published=1
					ORDER BY pos.ordering, pos.name	';
		$db->setQuery($query);
		if (!$result=$db->loadObjectList())
		{
			return false;
		}
		foreach ($result as $position)
		{
			$position->name=JText::_($position->name);
		}
		if($this->required == false) {
			$mitems = array(JHtml::_('select.option', '', JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_POSITION')));
		}
		
		foreach ( $result as $item )
		{
			$mitems[] = JHtml::_('select.option',  $item->id, '&nbsp;'.$item->name. ' ('.$item->id.')' );
		}
		return JHtml::_('select.genericlist',  $mitems, $this->name, 
						'class="inputbox" size="1"', 'value', 'text', $this->value, $this->id);
	}
}
 