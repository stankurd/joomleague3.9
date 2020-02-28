<?php

// no direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined( '_JEXEC' ) or die( 'Restricted access' );

$version			= '3.0.22.57ae969';
$updateFileDate		= '2018-12-30';
$updateFileTime		= '12:00';
$updateDescription	='<span style="color:green">Update table _treeto to 2TeamsInNode</span>';
$excludeFile		= 'false';

if(!function_exists('PrintStepResult')) {
    function PrintStepResult($result)
    {
        if ($result)
        {
            $output=' - <span style="color:green">'.Text::_('SUCCESS').'</span>';
        }
        else
        {
            $output=' - <span style="color:red">'.Text::_('FAILED').'</span>';
        }

        return $output;
    }}

function UpdateTableTreeto()
{
	$db		= Factory::getDBO();

	echo Text::sprintf( 'Update table %1$s', '<b>#__joomleague_treeto</b>' );

	$query = "
ALTER TABLE `#__joomleague_treeto` ADD
  `is_2teamsinnode` tinyint(1) NOT NULL default '0'
  AFTER `hide`
";

	$db->setQuery( $query );
	if ( $db->execute() )
	{
		echo ' - <span style="color:green">' . Text::_( 'SUCCESS' ) . '</span>';
	}
	else
	{
		echo ' - <span style="color:red">' . Text::_( 'FAILED' ) . '</span>';
	}

	return '';
}

?>
<hr>
<?php
	$output = Text::sprintf(	'JoomLeague v%1$s - Database-Update - Filedate: %2$s / %3$s',
								$version, $updateFileDate, $updateFileTime );
	JLToolBarHelper::title( $output );

	echo UpdateTableTreeto();
	echo '<br/>';
?>
<hr />
