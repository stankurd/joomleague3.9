<?php use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

defined('_JEXEC') or die; // Protect from unauthorized access
/**
* @package	 	Joomla
* @subpackage  	Joomleague results module
* @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Make sure JoomLeague is enabled
jimport( 'joomla.application.component.helper' );

if ( !ComponentHelper::isEnabled( 'com_joomleague') )
{
	throw new Exception( 'E_JLNOTENABLED', JText( 'JL_NOT_ENABLED' ) );
	return;
}

// Initialize defaults
$lang = Factory::getLanguage();
$image = "joomleague-48.png";
$label = JText::_( 'MOD_JOOMLEAGUE_ADMINPANEL_ICON_LABEL' );
?>
<div id="cpanel">
	<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
		<div class="icon">
			<a href="index.php?option=com_joomleague">
				<img src="components/com_joomleague/assets/images/jl_icon.png" alt="<?php echo $label; ?>"  />					
				<span><?php echo $label; ?></span></a>
		</div>
	</div>
</div>