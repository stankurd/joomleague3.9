<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;


require_once JLG_PATH_ADMIN.'/helpers/joomleaguehelper.php';


jimport('joomla.filesystem.folder');
FormHelper::loadFieldClass('list');


class JFormFieldseasonlist extends FormField
{
	/**
	 * field type
	 * @var string
	 */
	public $type = 'seasonlist';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	//protected function getOptions()
    protected function getInput()
	{
		// Initialize variables.
		$options = array();
        // Reference global application object
        $app = Factory::getApplication();
        // JInput object
        $jinput = $app->input;
        $view = $jinput->getCmd('view');
        $option = $jinput->getCmd('option');
        $lang = Factory::getLanguage();
		$lang->load("com_joomleague", JPATH_ADMINISTRATOR); 
        
        
    $attribs = '';
    $ctrl = $this->name;
    $val = ($this->element['value_field'] ? $this->element['value_field'] : $this->name);
//    $value = $this->form->getValue($val,'request');
//    if ( !$value )
//        {
//        $value = $this->form->getValue($val,'params');
//        $div = 'params';
//        }
//        else
//        {
//        $div = 'request';
//        }
        
        switch ($option)
        {
            case 'com_modules':
            $div = 'params';
            break;
            default:
            $div = 'request';
            break;
        }
        
        if ($v = $this->element['size'])
		{
			$attribs .= ' size="'.$v.'"';
		}
        
       
			$db = Factory::getDbo;
			$query = $db->getQuery(true);
			
			$query->select('id AS value, name AS text');
			$query->from('#__joomleague_season');
			$query->order('name DESC');
			$db->setQuery($query);
			$result = $db->loadObjectList();
    
		//// Merge any additional options in the XML definition.
//		$options = array_merge(parent::getOptions(), $options);
//		return $options;
$options = array(HTMLHelper::_('select.option', '', Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT'), 'value','text' ));
     if ( $result )
        {
     $options = array_merge($options, $result);
     }
//     // Merge any additional options in the XML definition.
//		$options = array_merge(parent::getOptions(), $options);
//
//		return $options;   
    //return HTMLHelper::_('select.genericlist',  $options, $ctrl, $attribs, $key, $val, $this->value, $this->id);
    return HTMLHelper::_('select.genericlist',  $options, $ctrl, $attribs, 'value', 'text', $this->value, $this->id);
    
	}
}
