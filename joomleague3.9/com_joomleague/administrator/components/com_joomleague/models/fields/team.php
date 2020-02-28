<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

class JFormFieldTeam extends JFormField
{

	protected $type = 'team';

	function getInput() {
		$db = JFactory::getDbo();
		$lang = JFactory::getLanguage();
		$extension = "com_joomleague";
		$source = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		

		$query = 'SELECT t.id, t.name FROM #__joomleague_team t ORDER BY name';
		$db->setQuery( $query );
		$teams = $db->loadObjectList();
		$mitems = array(JHtml::_('select.option', '', JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT')));

		foreach ( $teams as $team ) {
			$mitems[] = JHtml::_('select.option',  $team->id, '&nbsp;'.$team->name. ' ('.$team->id.')' );
		}
		
		$output= JHtml::_('select.genericlist',  $mitems, $this->name, 'class="inputbox" size="1"', 'value', 'text', $this->value, $this->id );
		return $output;
	}
}
 