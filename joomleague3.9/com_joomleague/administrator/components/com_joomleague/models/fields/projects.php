<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

class JFormFieldProjects extends JFormField
{

	protected $type = 'projects';

	protected function getInput() {
		$required 	= $this->element['required'] == "true" ? 'true' : 'false';
		$db = JFactory::getDbo();
		$lang = JFactory::getLanguage();
		$extension = "com_joomleague";
		$source 	= JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		
		$query = 'SELECT p.id, concat(p.name, \' ('.JText::_('COM_JOOMLEAGUE_GLOBAL_LEAGUE').': \', l.name, \')\', \' ('.JText::_('COM_JOOMLEAGUE_GLOBAL_SEASON').': \', s.name, \' )\' ) as name 
					FROM #__joomleague_project AS p 
					LEFT JOIN #__joomleague_season AS s ON s.id = p.season_id 
					LEFT JOIN #__joomleague_league AS l ON l.id = p.league_id 
					WHERE p.published=1 ORDER BY p.id DESC';
		$db->setQuery( $query );
		$projects = $db->loadObjectList();
		if($required == 'false') {
			// $mitems = array(JHtml::_('select.option', '', JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT')));
		}
		foreach ( $projects as $project ) {
			$mitems[] = JHtml::_('select.option',  $project->id, '&nbsp;&nbsp;&nbsp;'.$project->name );
		}
		
		$output= JHtml::_('select.genericlist',  $mitems, $this->name.'[]', 'class="inputbox" style="width:90%;" multiple="multiple" size="10"', 'value', 'text', $this->value, $this->id );
		return $output;
	}
}
