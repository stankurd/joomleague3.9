<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

class JFormFieldProject extends JFormField
{

	protected $type = 'project';

	protected function getInput() {
		$required 	= $this->required == "true" ? 'true' : 'false';
		$db			= JFactory::getDbo();
		$lang		= JFactory::getLanguage();
		$extension	= "com_joomleague";
		$source 	= JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		
		$query = 'SELECT p.id, concat(p.name, \' ('.JText::_('COM_JOOMLEAGUE_GLOBAL_LEAGUE').': \', l.name, \')\', \' ('.JText::_('COM_JOOMLEAGUE_GLOBAL_SEASON').': \', s.name, \' )\' ) as name 
					FROM #__joomleague_project AS p 
					LEFT JOIN #__joomleague_season AS s ON s.id = p.season_id 
					LEFT JOIN #__joomleague_league AS l ON l.id = p.league_id 
					WHERE p.published=1 ORDER BY p.ordering DESC';
		$db->setQuery( $query );
		$projects = $db->loadObjectList();
		$mitems = array();
		if($required=='false') {
			$mitems = array(JHtml::_('select.option', '', JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT')));
		}

		foreach ( $projects as $project ) {
			$mitems[] = JHtml::_('select.option',  $project->id, JText::_($project->name));
		}
		return  JHtml::_('select.genericlist',  $mitems, $this->name, 'class="inputbox" style="width:50%;" size="1"', 'value', 'text', $this->value, $this->id);
	}
}
