<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

/**
 * Renders a multiple item select element
 */
 
class JFormFieldMultiList extends JFormField
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	protected $type = 'MultiList';
 
	function getInput() {
		// Base name of the HTML control.
		$ctrl	= $control_name .'['. $name .']';
 
		// Construct an array of the HTML OPTION statements.
		$options = array ();
		foreach ($node->children() as $option)
		{
			$val	= $option->attributes('value');
			$text	= $option->data();
			$options[] = JHtml::_('select.option', $val, JText::_($text));
		}
 
		// Construct the various argument calls that are supported.
		$attribs	= ' ';
		if ($v = $node->attributes( 'size' )) {
			$attribs	.= 'size="'.$v.'"';
		}
		if ($v = $node->attributes( 'class' )) {
			$attribs	.= 'class="'.$v.'"';
		} else {
			$attribs	.= 'class="inputbox"';
		}
		if ($m = $node->attributes( 'multiple' ))
		{
			$attribs	.= ' multiple="multiple"';
			$ctrl		.= '[]';
		}
 
		// Render the HTML SELECT list.
		//return JHtml::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );
		return JHtml::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', array_map('trim', explode(',', $value)), $control_name.$name );
	}
}
