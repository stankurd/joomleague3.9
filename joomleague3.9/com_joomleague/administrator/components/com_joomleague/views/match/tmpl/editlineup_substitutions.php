<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

defined ( '_JEXEC' ) or die ();

?>
<script type="text/javascript">
<!--
// url for ajax
var baseajaxurl='<?php echo Uri::root();?>administrator/index.php?option=com_joomleague&<?php echo Session::getFormToken() ?>=1';
var teamid=<?php echo $this->tid; ?>;
var matchid=<?php echo $this->match->id; ?>;
// We need to setup some text variables for translation
var str_delete="<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_REMOVE'); ?>";
//-->
</script>
<!-- SUBSTITUTIONS START -->
<div id="io">
	<!-- Don't remove this "<div id"ajaxresponse"></div> as it is neede for ajax changings -->
	<div id="ajaxresponse"></div>
	<fieldset class="adminform">
		<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUSUBST_SUBST'); ?></legend>
		<table class='adminlist table' id="substitutions">
			<thead>
				<tr>
					<th>
							<?php
							echo HTMLHelper::image ( 'administrator/components/com_joomleague/assets/images/out.png', Text::_ ( 'Out' ) );
							echo '&nbsp;' . Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCH_ELUSUBST_OUT' );
							?>
						</th>
					<th>
							<?php
							echo Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCH_ELUSUBST_IN' ) . '&nbsp;';
							echo HTMLHelper::image ( 'administrator/components/com_joomleague/assets/images/in.png', Text::_ ( 'In' ) );
							?>
						</th>
					<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUSUBST_POS'); ?></th>
					<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ELUSUBST_TIME'); ?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
					<?php
					$k = 0;
					for($i = 0; $i < count ( $this->substitutions ); $i ++) {
						$substitution = $this->substitutions [$i];
						?>
						<tr id="sub-<?php echo $substitution->id; ?>"
					class="<?php echo "row$k"; ?>">
					<td>
								<?php
						if ($substitution->came_in == 2) {
							echo JoomleagueHelper::formatName ( null, $substitution->firstname, $substitution->nickname, $substitution->lastname, $this->default_name_format );
						} else {
							echo JoomleagueHelper::formatName ( null, $substitution->out_firstname, $substitution->out_nickname, $substitution->out_lastname, $this->default_name_format );
						}
						?>
							</td>
					<td>
								<?php
						if ($substitution->came_in == 1) {
							echo JoomleagueHelper::formatName ( null, $substitution->firstname, $substitution->nickname, $substitution->lastname, $this->default_name_format );
						}
						?>
							</td>
					<td>
								<?php echo Text::_($substitution->in_position); ?>
							</td>
					<td>
								<?php
						$time = (! is_null ( $substitution->in_out_time ) && $substitution->in_out_time > 0) ? $substitution->in_out_time : '--';
						echo $time;
						?>
							</td>
					<td><input id="delete-<?php echo $substitution->id; ?>"
						type="button" class="inputbox button-delete btn"
						value="<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_REMOVE'); ?>" />
					</td>
				</tr>
						<?php
						$k = (1 - $k);
					}
					?>
					<tr id="row-new">
					<td><?php echo HTMLHelper::_('select.genericlist',$this->playersoptions_subs_out,'out','class="inputbox player-out"'); ?></td>
					<td><?php echo HTMLHelper::_('select.genericlist',$this->playersoptions_subs_in,'in','class="inputbox player-in"'); ?></td>
					<td><?php echo $this->lists['projectpositions']; ?></td>
					<td><input type="text" size="3" id="in_out_time" name="in_out_time"
						class="inputbox" /></td>
					<td><input id="save-new" type="button"
						class="inputbox button-save btn"
						value="<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_SAVE'); ?>" /></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
</div>
<!-- SUBSTITUTIONS END -->
