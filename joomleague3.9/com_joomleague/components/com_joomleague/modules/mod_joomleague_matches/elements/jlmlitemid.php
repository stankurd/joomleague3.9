<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die();

class JElementJLMLItemid extends FormField 
    {

	var	$_name = 'jlmlitemid';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = Factory::getDbo();

		$options 	= array(HTMLHelper::_('select.option', '', '- '.JText::_('Select Item').' -'));

		$query = "SELECT menutype, title FROM #__menu_types ORDER BY title";
		$db->setQuery( $query );
		$mtypes = $db->loadObjectList();

		$menu = CMSApplication::getMenu('site', $options);
		$mitems = $menu->getMenu();		
		foreach ($mitems as &$item) {
		 	
			if ($item->component == "com_joomleague"){
				$item->name = "--&gt; ".$item->name." &lt;--";
			}
			unset($item);
		 } 

		$childs = array();

		if ($mitems)
		{

			foreach ($mitems as $val)
			{
				$parent 	= $val->parent;
				$list 	= @$childs[$parent] ? $childs[$parent] : array();
				array_push( $list, $val );
				$childs[$parent] = $list;
			}
		}

		$list = HTMLHelper::_('menu.treerecurse', 0, '', array(), $childs, true, 0, 0 );

		$cnt = count( $list );
		$olist = array();
		foreach ($list as $k => $v) {
			$olist[$v->menutype][] = &$list[$k];
		}

		foreach ($mtypes as $type)
		{
			$options[]	= HTMLHelper::_('select.option',  $type->menutype, $type->title , 'value', 'text', true );
			if (isset( $olist[$type->menutype] ))
			{
				$cnt = count( $olist[$type->menutype] );
				for ($x=0; $x < $cnt; $x++)
				{
					$item = &$olist[$type->menutype][$x];
					
					//If menutype is changed but item is not saved yet, use the new type in the list
					if ( Factory :: getApplication()->input->get('option', '', 'get') == 'com_menus' ) {
						$currentItemArray = Factory :: getApplication()->input->get('cid', array(0), '', 'array');
						$currentItemId = (int) $currentItemArray[0];
						$currentItemType = Factory :: getApplication()->input->get('type', $item->type, 'get');
						if ( $currentItemId == $item->id && $currentItemType != $item->type) {
							$item->type = $currentItemType;
						}
					}
					
					$disable = strpos($node->attributes('disable'), $item->type) !== false ? true : false;
					$options[] = HTMLHelper::_('select.option',  $item->id, $item->id.'&nbsp;&nbsp;&nbsp;' .$item->treename, 'value', 'text', $disable );

				}
			}
		}

		return HTMLHelper::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name);
	}
	
	function fetchElementOLD($name, $value, &$node, $control_name)
	{
		$options = array();
		$menu = CMSApplication::getMenu('site', $options);
		$items = $menu->getMenu();		
		
		$items2 = $menu->getItems("component","com_joomleague");
		ArrayHelper::sortObjects($items2,"menutype");
		foreach ($items2 as &$item) {
			$item->title = $item->name. " (".$item->menutype." - ".$item->component.")";
			unset($item);
		}
		
		ArrayHelper::sortObjects($items,"menutype");
		foreach ($items as &$item) {
			if ($item->component!="com_joomleague"){
				$item->title = $item->name. " (".$item->menutype." - ".$item->component.")";
				$items2[] = $item;
				unset($item);
			}
		}

		return HTMLHelper::_('select.genericlist',  $items2, ''.$control_name.'['.$name.']', '', 'id', 'title', $value, $control_name.$name );
	}
}