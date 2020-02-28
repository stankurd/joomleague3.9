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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

$option = 'com_joomleague';
?>
<!-- Start Joomleague content -->
<div class="test">
	<div id="j-sidebar-container" class="span2">
		<div id="element-box">
			<div class="m">
				<div id="navbar">
					<form action="index.php?option=com_joomleague&view=projects" method="post" id="adminForm1">
						<div id="area" class="qMenu">
							<div class="qMenu_header"></div>
							<div class="qMenu_content">
						<?php echo $this->lists['sportstypes']; ?>
						<?php if ($this->sports_type_id): ?>
							<?php echo $this->lists['seasons']; ?><br />
							<?php echo $this->lists['projects']; ?><br />
						<?php endif; ?>
						<?php
						if($this->project && $this->sports_type_id)
						{
							echo $this->lists['projectteams'];
							?>
							<br />
						<?php
							echo $this->lists['projectrounds'];
						}
						?>
						</div>
					</div>
				<input type="hidden" name="option" value="com_joomleague" />
				<input type="hidden" name="act" value="" id="jl_short_act" />
				<input type="hidden" name="task" value="joomleague.selectws" />
				<?php echo HTMLHelper::_('form.token'); ?>
			</form>
			<?php
			$n = 0;
			$tabs = $this->tabs;
			$link = $this->link;
			$label = $this->label;
			$limage = $this->limage;
			$href = '';
			$title = '';
			$image = '';
			$text = '';
			$selector = "joomleagueamenu";
			/*echo HTMLHelper::_('bootstrap.startAccordion', $selector,
			array(
							'allowAllClose' => true,
							'startOffset' => $this->active,
							'startTransition' => true,
							false
					)
					);
					*/

			echo HTMLHelper::_('sliders.start','sliders',
			array(
			'allowAllClose' => true,
			'startOffset' => $this->active,
			'startTransition' => true,
			false
			));

			foreach($tabs as $tab)
			{
				$title = $tab->title;

				echo HTMLHelper::_('sliders.panel',$title,'jfcpanel-panel-' . $tab->name);
				?>
					<div>
						<table class="table table-hover"><?php
				for($i = 0;$i < count($link[$n]);$i ++)
				{
					$href = $link[$n][$i];
					$title = $label[$n][$i];
					$image = $limage[$n][$i];
					$text = $label[$n][$i];
					$allowed = true;
					$data = Uri::getInstance($href)->getQuery(true);
					$jinput = new JInput($data);
					$task = $jinput->getCmd('task');
					if($task != '' && $option == 'com_joomleague')
					{
						if(! Factory::getUser()->authorise($task,'com_joomleague'))
						{
							$allowed = false;
						}
					}
					if($allowed)
					{
						echo '<tr><td><b><a href="' . $href . '" title="">' . $image . ' ' . $text . '</a></b></td></tr>';
					}
					else
					{
						echo '<tr><td><span title="' . Text::_('JGLOBAL_AUTH_ACCESS_DENIED') . '">' . $image . ' ' . $text . '</span></td></tr>';
					}
				}

				?></table>
				
					</div>
					<?php
				$n ++;
			}
			echo HTMLHelper::_('sliders.end');
			// Extension
			$extensions = JoomleagueHelper::getExtensions(1);
			foreach($extensions as $e=>$extension)
			{
				$JLGPATH_EXTENSION = JPATH_COMPONENT_SITE . '/extensions/' . $extension;
				$menufile = $JLGPATH_EXTENSION . '/admin/views/joomleague/tmpl/default_' . $extension . '.php';
				if(JFile::exists($menufile))
				{
					echo $this->loadTemplate($extension);
				}
				else
				{
				}
			}
			//echo HTMLHelper::_('bootstrap.endSlide');
			//echo HTMLHelper::_('bootstrap.endAccordion');
			?>

			<div class="center">
						<br />
				<?php
				$image = HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/jl.png',Text::_('JoomLeague'),
						array(
								"title" => Text::_('JoomLeague')
						));
				$url = 'index.php?option=com_joomleague&view=about';

				echo HTMLHelper::_('link',Route::_($url),$image);
				?>
			</div>
					<div style="text-align: center;">
						<small style="color: blue;"><?php echo 'v'.JoomleagueHelper::getVersion(); ?></small>
					</div>
				</div>
			</div>
		</div>
</div>
	</div>
	<div id="j-main-container" class="span10">