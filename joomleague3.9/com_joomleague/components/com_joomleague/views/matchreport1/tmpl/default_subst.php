<?php

use Joomla\CMS\Language\Text;
defined('_JEXEC') or die;
?>
<!-- START of Substitutions -->
<?php
if ($this->config['show_substitutions']==1)
{
	if (!empty($this->substitutes))
	{
		?>
		<h2><?php echo Text::_('COM_JOOMLEAGUE_MATCHREPORT_SUBSTITUTES'); ?></h2>	
		<table class="matchreport">
			<tr>
				<td class="list">
					<ul><?php
						foreach ($this->substitutes as $sub)
						{
							if ($sub->ptid==$this->match->projectteam1_id)
							{
								?><li class="list"><?php echo $this->showSubstitution($sub); ?></li><?php
							}
						}
						?></ul>
				</td>
				<td class="list">
					<ul><?php
						foreach ($this->substitutes as $sub)
						{
							if ($sub->ptid==$this->match->projectteam2_id)
							{
								?><li class="list"><?php echo $this->showSubstitution($sub); ?></li><?php
							}
						}
						?></ul>
				</td>
			</tr>
		</table>
		<?php
	}
}
?>
<!-- END of Substitutions -->
