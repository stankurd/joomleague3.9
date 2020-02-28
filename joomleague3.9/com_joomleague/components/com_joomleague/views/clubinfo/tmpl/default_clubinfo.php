<?php use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

if (!isset ($this->club))
{
    Factory::getApplication()->enqueueMessage(Text::_('Error: ClubID was not submitted in URL or Club was not found in database'), 'warning');
}
else
{
	$classOrStyle = $this->config['show_club_info'] == 0 ? 'style="text-align:center; width:100%;"' : 'class="left-column"';
	?>
	<div <?php echo $classOrStyle; ?>>
		<!-- SHOW LOGO - START -->
		<?php
		if (($this->config['show_club_logo']) && ($this->club->logo_big != ''))
		{
			$club_emblem_title = str_replace("%CLUBNAME%", $this->club->name, Text::_('COM_JOOMLEAGUE_CLUBINFO_EMBLEM_TITLE'));
			$picture = $this->club->logo_big;
			echo JoomleagueHelper::getPictureThumb($picture,  $club_emblem_title,
				$this->config['team_picture_width'], $this->config['team_picture_height'], 1);
		}
		?>
		<!-- SHOW LOGO - END -->
		<!-- SHOW SMALL LOGO - START -->
		<?php
		if ($this->config['show_club_shirt'] && $this->club->logo_small != '')
		{
			$club_trikot_title = str_replace("%CLUBNAME%", $this->club->name, Text::_("COM_JOOMLEAGUE_CLUBINFO_TRIKOT_TITLE"));
			$picture = $this->club->logo_small;
			echo JoomleagueHelper::getPictureThumb($picture, $club_emblem_title, 20, 20, 3);
		}
		?>
		<!-- SHOW SMALL LOGO - END -->
	</div>
	<?php
        if (!($this->config['show_club_info'] == 0)) {
        ?>
	<div class="right-column">
		<?php
		if ($this->club->address || $this->club->zipcode)
		{
			$addressString = Countries::convertAddressString($this->club->name, $this->club->address, $this->club->state,
				$this->club->zipcode, $this->club->location, $this->club->country, 'COM_JOOMLEAGUE_CLUBINFO_ADDRESS_FORM');
			?>
			<span class="clubinfo_listing_item">
				<?php
				echo Text::_('COM_JOOMLEAGUE_CLUBINFO_ADDRESS');
				$dummyStr = explode('<br />', $addressString);
				for ($i = 0; $i < count($dummyStr); $i++)
				{
					echo '<br />';
				}
				?>
			</span>
			<span class="clubinfo_listing_value"><?php echo $addressString; ?></span>
			<?php
		}

		if ($this->club->phone)
		{
			?>
			<span class="clubinfo_listing_item"><?php echo Text::_('COM_JOOMLEAGUE_CLUBINFO_PHONE'); ?></span>
			<span class="clubinfo_listing_value"><?php echo $this->club->phone; ?></span>
			<?php
		}

		if ($this->club->fax)
		{
			?>
			<span class="clubinfo_listing_item"><?php echo Text::_('COM_JOOMLEAGUE_CLUBINFO_FAX'); ?></span>
			<span class="clubinfo_listing_value"><?php echo $this->club->fax; ?></span>
			<?php
		}

		if ($this->club->email)
		{
			?>
			<span class="clubinfo_listing_item"><?php echo Text::_('COM_JOOMLEAGUE_CLUBINFO_EMAIL'); ?></span>
			<span class="clubinfo_listing_value">
				<?php
				// to prevent spam, crypt email display if nospam_email is selected
				//or user is a guest
				$user = Factory::getUser();
				if ($user->id || !$this->overallconfig['nospam_email'])
				{
					?>
					<a href="mailto: <?php echo $this->club->email; ?>"><?php echo $this->club->email; ?></a>
					<?php
				}
				else
				{
					echo HTMLHelper::_('email.cloak', $this->club->email);
				}
				?>
			</span>
			<?php
		}

		if ($this->club->website)
		{
			?>
			<span class="clubinfo_listing_item"><?php echo Text::_('COM_JOOMLEAGUE_CLUBINFO_WWW'); ?></span>
			<span class="clubinfo_listing_value">
				<?php echo HTMLHelper::_('link', $this->club->website, $this->club->website, array("target" => "_blank")); ?>
			</span>
			<?php
		}

		if ($this->club->president)
		{
			?>
			<span class="clubinfo_listing_item"><?php echo Text::_('COM_JOOMLEAGUE_CLUBINFO_PRESIDENT'); ?></span>
			<span class="clubinfo_listing_value"><?php echo $this->club->president; ?></span>
			<?php
		}

		if ($this->club->manager)
		{
			?>
			<span class="clubinfo_listing_item"><?php echo Text::_('COM_JOOMLEAGUE_CLUBINFO_MANAGER'); ?></span>
			<span class="clubinfo_listing_value"><?php echo $this->club->manager; ?></span>
			<?php
		}

		if ($this->club->founded && $this->club->founded != '0000-00-00')
		{
			?>
			<span class="clubinfo_listing_item"><?php echo Text::_('COM_JOOMLEAGUE_CLUBINFO_FOUNDED'); ?></span>
			<span class="clubinfo_listing_value"><?php echo $this->club->founded; ?></span>
			<?php
		}

		if ($this->club->dissolved && $this->club->dissolved != '0000-00-00')
		{
			?>
			<span class="clubinfo_listing_item"><?php echo Text::_('COM_JOOMLEAGUE_CLUBINFO_DISSOLVED'); ?></span>
			<span class="clubinfo_listing_value"><?php echo $this->club->dissolved; ?></span>
			<?php
		}

		if ($this->config['show_playgrounds_of_club'] == 1 && !empty($this->playgrounds))
		{
			$playground_number = 1;
			foreach ($this->playgrounds AS $playground)
			{
				$link = JoomleagueHelperRoute::getPlaygroundRoute($this->project->slug, $playground->slug);
				$pl_dummy = Text::_('COM_JOOMLEAGUE_CLUBINFO_PLAYGROUND');
				?>
				<span class="clubinfo_listing_item"><?php echo str_replace("%NUMBER%", $playground_number, $pl_dummy); ?></span>
				<span class="clubinfo_listing_value"><?php echo HTMLHelper::link($link, $playground->name); ?></span>
				<?php
				$playground_number++;
			}
		}
		?>
	</div>
	<?php
	}
}
?>