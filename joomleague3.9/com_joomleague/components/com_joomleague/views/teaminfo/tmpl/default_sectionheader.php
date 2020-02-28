<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die; 

HTMLHelper::addIncludePath(JPATH_COMPONENT.'/helpers');

$canEdit = $this->showediticon;
?>
<!-- START: Contentheading -->
<div class='contentpaneopen'>
	<div class='contentheading'>
		<?php 
		echo $this->pagetitle;
		if ($canEdit) {
		    $file = 'media/com_joomleague/jl_images/edit.png';
		    echo HTMLHelper::image($file, $alt, $attribs = null, $relative = false, $returnPath = 0);	    
		    //echo HTMLHelper::image('media/com_joomleague/jl_images/edit.png',$this->project->id,$this->team,$this->team->project_team_id,'projectteamform.edit','teaminfo');
		} 		
		?>
	</div>
</div>
<!-- END: Contentheading -->
