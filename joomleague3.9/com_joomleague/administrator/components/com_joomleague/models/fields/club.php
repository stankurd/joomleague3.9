<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2007-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

defined('_JEXEC') or die;

class JFormFieldClub extends JFormField
{

	protected $type = 'club';
	
	function getInput() {
		$required 	= $this->element['required'] == 'true' ? 'true' : 'false';
		$db = JFactory::getDbo();
		$lang = JFactory::getLanguage();
		$extension = "com_joomleague";
		$source = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		
		$query = 'SELECT c.id, c.name FROM #__joomleague_club c ORDER BY name';
		$db->setQuery( $query );
		$clubs = $db->loadObjectList();
		$mitems = array();
		if($required == 'false') {
			$mitems[] = JHtml::_('select.option', '', JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT'));
		}
	
		foreach ( $clubs as $club ) {
			$mitems[] = JHtml::_('select.option',  $club->id, '&nbsp;'.$club->name. ' ('.$club->id.')' );
		}
		
		$output= JHtml::_('select.genericlist',  $mitems, $this->name, 'class="inputbox" size="1"', 'value', 'text', $this->value, $this->id );
		return $output;
	}
}
 