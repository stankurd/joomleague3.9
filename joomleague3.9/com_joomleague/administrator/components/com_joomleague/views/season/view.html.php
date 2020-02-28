<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * HTML View class
 */
class JoomleagueViewSeason extends JLGView
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
			
		$this->addToolbar();			
		parent::display($tpl);
	}

	
	/**
	* Add the page title and toolbar
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
			JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_SEASON_ADD_NEW'),'jl-seasons');
			/*JLToolbarHelper::saveGroup(
			[
				['apply', 'season.apply'],
				['save', 'season.save']
			],
			'btn-success'
		);*/
			JLToolBarHelper::apply('season.apply');
			JLToolBarHelper::save('season.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('season.cancel');
		}
		else
		{
			JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_SEASON_EDIT').': '.$this->form->getValue('name'),'jl-seasons');
			/*JLToolbarHelper::saveGroup(
			[
				['apply', 'season.apply'],
				['save', 'season.save']
			],
			'btn-success'
		);*/
			JLToolBarHelper::apply('season.apply');
			JLToolBarHelper::save('season.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('season.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}		
}
