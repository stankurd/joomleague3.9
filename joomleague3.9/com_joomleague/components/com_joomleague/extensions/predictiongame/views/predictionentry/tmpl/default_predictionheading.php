<?php 
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');
?>
<?php
//echo '<br /><pre>~' . print_r($this,true) . '~</pre><br />';
//if ((isset($this->config['show_prediction_heading'])) && ($this->config['show_prediction_heading']))
{
	?>
	<table class='blog' cellpadding='0' cellspacing='0' width='100%'>
		<tr>
			<td class='sectiontableheader'>
				<?php
				echo Text::sprintf('COM_JOOMLEAGUE_PRED_HEAD_ACTUAL_PRED_GAME','<b><i>'.$this->predictionGame->name.'</i></b>');
				if ((isset($this->showediticon)) && ($this->showediticon) && ($this->predictionMember->pmID > 0))
				{
					echo '&nbsp;&nbsp;';
					$link = PredictionHelperRoute::getPredictionMemberRoute($this->predictionGame->id,$this->predictionMember->pmID,'edit');
					$imgTitle=Text::_('COM_JOOMLEAGUE_PRED_HEAD_EDIT_IMAGE_TITLE');
					$desc = HTMLHelper::image('media/com_joomleague/jl_images/edit.png',$imgTitle,array('border' => 0, 'title' => $imgTitle));
					echo HTMLHelper::link($link,$desc);
				}
				?>
			</td>
			<?php
			if (!isset($this->allowedAdmin)){$this->allowedAdmin=false;}
			if (($this->getName()=='predictionusers') || (($this->allowedAdmin) && ($this->getName()=='predictionentry')))
			{
				?>
				<td class='sectiontableheader' style='text-align:right; ' width='15%'  nowrap='nowrap'>
					<form name='predictionMemberSelect' method='post' >
					<input type='hidden' name='prediction_id' value='<?php echo intval($this->predictionGame->id); ?>' />
					<input type='hidden' name='task' value='select' />
					<input type='hidden' name='option' value='com_joomleague' />
					<input type='hidden' name='controller' value='<?php echo $this->getName(); ?>' />
					<?php echo HTMLHelper::_('form.token'); ?>
						<?php echo $this->lists['predictionMembers']; ?>
					</form>
				</td>
				<?php
			}
			?>
			<td class='sectiontableheader' style='text-align:right; ' width='15%' nowrap='nowrap'>
				<?php
				$output = '';
				$imgTitle = Text::_('COM_JOOMLEAGUE_PRED_HEAD_ENTRY_IMAGE_TITLE');
				$img = HTMLHelper::image(Uri::base().'media/com_joomleague/jl_images/prediction_entry.png',$imgTitle,array('border' => 0, 'title' => $imgTitle));
				$link = PredictionHelperRoute::getPredictionTippEntryRoute($this->predictionGame->id);
				$output .= HTMLHelper::link($link,$img,array('title' => $imgTitle));
				$output .= '&nbsp;';
				$imgTitle = Text::_('COM_JOOMLEAGUE_PRED_HEAD_MEMBER_IMAGE_TITLE');
				$img = HTMLHelper::image(Uri::root().'media/com_joomleague/jl_images/prediction_member.png',$imgTitle,array('border' => 0, 'title' => $imgTitle));
				if ($this->predictionMember->pmID > 0){$pmVar=$this->predictionMember->pmID;}else{$pmVar=null;}
				$link = PredictionHelperRoute::getPredictionMemberRoute($this->predictionGame->id,$pmVar);
				$output .= HTMLHelper::link($link,$img,array('title' => $imgTitle));
				$output .= '&nbsp;';
				$imgTitle = Text::_('COM_JOOMLEAGUE_PRED_HEAD_RESULTS_IMAGE_TITLE');
				$img = HTMLHelper::image(Uri::root().'media/com_joomleague/jl_images/prediction_results.png',$imgTitle,array('border' => 0, 'title' => $imgTitle));
				$link = PredictionHelperRoute::getPredictionResultsRoute($this->predictionGame->id);
				$output .= HTMLHelper::link($link,$img,array('title' => $imgTitle));
				$output .= '&nbsp;';
				$imgTitle = Text::_('COM_JOOMLEAGUE_PRED_HEAD_RANKING_IMAGE_TITLE');
				$img = HTMLHelper::image(Uri::root().'media/com_joomleague/jl_images/prediction_ranking.png',$imgTitle,array('border' => 0, 'title' => $imgTitle));
				$link = PredictionHelperRoute::getPredictionRankingRoute($this->predictionGame->id);
				$output .= HTMLHelper::link($link,$img,array('title' => $imgTitle));
				$output .= '&nbsp;';
				$imgTitle = Text::_('COM_JOOMLEAGUE_PRED_HEAD_RULES_IMAGE_TITLE');
				$img = HTMLHelper::image(Uri::root().'media/com_joomleague/jl_images/prediction_rules.png',$imgTitle,array('border' => 0, 'title' => $imgTitle));
				$link = PredictionHelperRoute::getPredictionRulesRoute($this->predictionGame->id);
				$output .= HTMLHelper::link($link,$img,array('title' => $imgTitle));
                var_dump($output);
				echo $output;
				?>
			</td>
			<?php
			/*
			if ($this->config['show_pdf_button'])
			{
				?><td class='sectiontableheader' align='right' >
					<?php
					$url = '';
					$imgTitle = Text::_('PDF');
					$desc = HTMLHelper::image(	Uri::root() . 'media/com_joomleague/jl_images/pdf_button.png',
											$imgTitle,
											array(	'id' => 'pdf',
													'border' => 0,
													'title' => $imgTitle ) );
					//echo '<a class="mymodal" title="example" href="' . $url . '" >';
						echo $desc;
					//echo '</a>';
					?>
				</td><?php
			}
			if ($this->config['show_print_button'])
			{
				?><td class='sectiontableheader' align='right' >
					<?php
					$url = '';
					$imgTitle = Text::_('Print');
					$desc = HTMLHelper::image(	Uri::root() . 'media/com_joomleague/jl_images/printButton.png',
											$imgTitle,
											array(	'id' => 'print',
													'border' => 0,
													'title' => $imgTitle ) );
					//echo '<a href="#" onclick="window.print();return false;">';
						echo $desc;
					//echo '</a>';
					?>
				</td><?php
			}
			if ($this->config['show_email_button'])
			{
				?><td class='sectiontableheader' align='right' >
					<?php
					$url = '';
					$imgTitle = Text::_('E-Mail');
					$desc = HTMLHelper::image(	Uri::root() . 'media/com_joomleague/jl_images/mail.gif',
											$imgTitle,
											array(	'id' => 'email',
													'border' => 0,
													'title' => $imgTitle ) );
					//echo '<a href="#" onclick="window.print();return false;">';
						echo $desc;
					//echo '</a>';
					?>
				</td><?php
			}
			*/
			?>
		</tr>
	</table><?php
}
?>