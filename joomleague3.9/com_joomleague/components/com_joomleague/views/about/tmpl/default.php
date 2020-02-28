<?php 
/**
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 *
 * Default HTML layout for the Joomleague component
 *
 * @author Joomleague Team <www.joomleague.at>
*/
use Joomla\CMS\Language\Text;
defined('_JEXEC') or die;
?>
<table class="about">
	<tr>
		<td align="center">
		<object type="application/x-shockwave-flash" data="media/com_joomleague/jl_images/joomleague_logo.swf" id="movie" width="410" height="200">
		<param name="movie" value="media/com_joomleague/jl_images/joomleague_logo.swf" />
		<param name="bgcolor" value="#FFFFFF" />
		<param name="quality" value="high" />
		<param name="loop" value="false" />
		<param name="allowscriptaccess" value="samedomain" />
	  </object>
		</td>
	</tr>
</table>
<br />
<div class="componentheading">
	<?php echo $this->pageTitle; ?>
</div>
<table class="about">
	<tr>
		<td><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_TEXT'); ?></td>
	</tr>
</table>
<br />
<div class="componentheading">
	<?php echo Text::_('COM_JOOMLEAGUE_ABOUT_DETAILS'); ?>
</div>
<table class="about">
	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_TRANSLATIONS'); ?></b></td>
		<td>
			<a href="<?php echo $this->about->translations; ?>" target="_blank">
				<?php echo $this->about->translations; ?></a>
		</td>
	</tr>
	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_REPOSITORY'); ?></b></td>
		<td>
			<a href="<?php echo $this->about->repository; ?>" target="_blank">
				<?php echo $this->about->repository; ?></a>
		</td>
	</tr>
	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_VERSION'); ?></b></td>
		<td><?php echo $this->about->version; ?></td>
	</tr>
	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_AUTHOR'); ?></b></td>
		<td><?php echo $this->about->author; ?></td>
	</tr>

	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_WEBSITE'); ?></b></td>
		<td>
			<a href="<?php echo $this->about->page; ?>" target="_blank">
				<?php echo $this->about->page; ?>
			</a>
		</td>
	</tr>
	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_SUPPORT_FORUM'); ?></b></td>
		<td>
			<a href="<?php echo $this->about->forum; ?>" target="_blank">
				<?php echo $this->about->forum; ?></a>
		</td>
	</tr>
	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_BUGS'); ?></b></td>
		<td>
			<a href="<?php echo $this->about->bugs; ?>" target="_blank">
				<?php echo $this->about->bugs; ?></a>
		</td>
	</tr>
	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_WIKI'); ?></b></td>
		<td>
			<a href="<?php echo $this->about->wiki; ?>" target="_blank">
				<?php echo $this->about->wiki; ?></a>
		</td>
	</tr>	
	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_DEVELOPERS'); ?></b></td>
		<td><?php echo $this->about->developer; ?></td>
	</tr>
<!--
	<tr>
		<td><b><?php //echo Text::_('COM_JOOMLEAGUE_ABOUT_SUPPORTERS'); ?></b></td>
		<td><?php //echo $this->about->supporters; ?></td>
	</tr>
	<tr>
		<td><b><?php //echo Text::_('COM_JOOMLEAGUE_ABOUT_TRANSLATORS'); ?></b></td>
		<td><?php //echo $this->about->translator; ?></td>
	</tr>
-->
	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_DESIGNER'); ?></b></td>
		<td><?php echo $this->about->designer; ?></td>
	</tr>
	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_ICONS'); ?></b></td>
		<td><?php echo $this->about->icons; ?></td>
	</tr>
	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_FLASH_STATISTICS'); ?></b></td>
		<td><?php echo $this->about->flash; ?></td>
	</tr>
	<tr>
		<td><b><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_PHPTHUMB'); ?></b></td>
		<td><?php echo $this->about->phpthumb; ?></td>
	</tr>	
<!--
	<tr>
		<td><b><?php //echo Text::_('COM_JOOMLEAGUE_ABOUT_GRAPHIC_LIBRARY'); ?></b></td>
		<td><?php //echo $this->about->graphic_library; ?></td>
	</tr>
-->
</table>
<br />
<div class="componentheading">
	<?php echo Text::_('COM_JOOMLEAGUE_ABOUT_LICENSE'); ?>
</div>
<table class="about">
	<tr>
		<td><?php echo Text::_('COM_JOOMLEAGUE_ABOUT_LICENSE_TEXT'); ?></td>
	</tr>
</table>
