<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewPerson extends JLGView
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		if($this->getLayout() == 'form')
		{
			$this->_displayForm($tpl);
			return;
		}
		elseif($this->getLayout() == 'assignperson')
		{
			$this->_displayModal($tpl);
			return;
		}
	}


	function _displayForm($tpl)
	{
		$app = Factory::getApplication();
		$input = $app->input;

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		$extended = $this->getExtended($this->item->extended,'person');
		$this->extended = $extended;

		$this->addToolbar();

		// Load the language files for the contact integration
		$jlang = Factory::getLanguage();
		$jlang->load('com_contact',JPATH_ADMINISTRATOR,'en-GB',true);
		$jlang->load('com_contact',JPATH_ADMINISTRATOR,$jlang->getDefault(),true);
		$jlang->load('com_contact',JPATH_ADMINISTRATOR,null,true);

		parent::display($tpl);
	}


	function _displayModal($tpl)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		
		$input->set('hidemainmenu',true);
		$input->set('hidesidemenu',true);
	
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_ASSIGN_DESCR'));
		//\Joomla\CMS\Http\Response::allowCache(false);
		JResponse::allowCache(false);

		$document = Factory::getDocument();
		$prjid = $input->get('prjid',array(0),'array');
		ArrayHelper::toInteger($prjid);
		$proj_id = (int) $prjid[0];
		
		// build the html select list for projects
		$projects[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_PROJECT'),'id','name');

		if($res = JoomleagueHelper::getProjects())
		{
			$projects = array_merge($projects,$res);
		}
		$lists['projects'] = HTMLHelper::_('select.genericlist', $projects,'prjid[]','class="inputbox" onChange="this.form.submit();" style="width:170px"','id',
				'name',$proj_id);
		unset($projects);

		$projectteams[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TEAM'),'value','text');

		// if a project is active we show the teams select list
		if($proj_id > 0)
		{
			if($res = JoomleagueHelper::getProjectteams($proj_id))
			{
				$projectteams = array_merge($projectteams,$res);
			}
			$lists['projectteams'] = HTMLHelper::_('select.genericlist', $projectteams,'xtid[]','class="inputbox" style="width:170px"','value','text');
			unset($projectteams);
		}

		$this->lists = $lists;
		$this->project_id = $proj_id;

		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu',true);
		$user = Factory::getUser();
		$userId = $user->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Set toolbar items for the page
		$text = $isNew ? Text::_('COM_JOOMLEAGUE_GLOBAL_NEW') : Text::_('COM_JOOMLEAGUE_GLOBAL_EDIT');

		if($isNew)
		{
			JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_TITLE'));
			/*ToolbarHelper::saveGroup(
			    [
			        ['apply', 'person.apply'],
			        ['save', 'person.save'],
			    ],
			    'btn-success'
			    );*/
			JLToolBarHelper::apply('person.apply');
			JLToolBarHelper::save('person.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('person.cancel');
		}
		else
		{
			$name = JoomleagueHelper::formatName(null,$this->form->getValue('firstname'),$this->form->getValue('nickname'),
			$this->form->getValue('lastname'),JoomleagueHelper::defaultNameFormat());
			JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_TITLE2') . ': ' . $name);
			/*ToolbarHelper::saveGroup(
			    [
			        ['apply', 'person.apply'],
			        ['save', 'person.save'],
			    ],
			    'btn-success'
			    );*/
			JLToolBarHelper::apply('person.apply');
			JLToolBarHelper::save('person.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('person.cancel',Text::_('COM_JOOMLEAGUE_GLOBAL_CLOSE'));
		}
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
