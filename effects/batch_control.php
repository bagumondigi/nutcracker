<?php
require_once("../effects/read_file.php");
$target="AA2244"; // small target 6x30, runs fast good for testing
$target="AA";  // larger target, 2400 channes
$batch=2;   
//  Batch
//	Level     |   Html  |   Full Size Gifs  |  Thumbnail Gif's   |  NC Data file
// ------------------------------------------------------------------------------
//    0       |   Yes   |        Yes        |       Yes          |      Yes
//    1       |   No    |        Yes        |       Yes          |      Yes
//    2       |   No    |        No         |       Yes          |      Yes
//    3       |   No    |        No         |       No           |      Yes
$song_list[] = array($target,"BARS1","f_bars");
$song_list[] = array($target,"FIRE1","f_fire");
$song_list[] = array($target,"GARLAND0","f_garlands");
$song_list[] = array($target,"FLY_0_0","f_butterfly");
$song_list[] = array($target,"BARS1_TEST","f_bars");
$song_list[] = array($target,"BARBERPOLE","f_spirals");
$song_list[] = array($target,"BARBERPOLE_180","f_spirals");
$song_list[] = array($target,"COLOR_WASH1","f_color_wash");
$song_list[] = array($target,"GIF1","f_gif");
$song_list[] = array($target,"LIFE","f_life");
$song_list[] = array($target,"METEOR1","f_meteors");
$song_list[] = array($target,"TEXT1","f_text");
$song_list[] = array($target,"PICT1","f_pictures");
$song_list[] = array($target,"USER1","f_user_defined");
$song_list[] = array($target,"SNOW1","f_snowstorm");
$username='f';
echo "<html>";
echo "<body>";
require_once ("../effects/f_bars.php");
require_once ("../effects/f_spirals.php");
require_once ("../effects/f_butterfly.php");
require_once ("../effects/f_fire.php");
require_once ("../effects/f_garlands.php");
require_once ("../effects/f_color_wash.php");
require_once ("../effects/f_gif.php");
require_once ("../effects/f_life.php");
require_once ("../effects/f_meteors.php");
require_once ("../effects/f_text.php");
require_once ("../effects/f_pictures.php");
require_once ("../effects/f_user_defined.php");
require_once ("../effects/f_snowstorm.php");
//
echo "<table border=2>";
echo "<tr>";
echo "<th>#</th>";
echo "<th>Target</th>";
echo "<th>Effect</th>";
echo "<th>Elapsed<br/>Time (secs)</th>";
echo "<th>gif</th>";
echo "</tr>";
list($usec, $sec) = explode(' ', microtime());
$program_start = (float) $sec + (float) $usec;
foreach($song_list as $i=>$arr2)
{
	//
	$target=$arr2[0];
	$effect=$arr2[1];
	$program=$arr2[2];
	echo "<tr>";
	$row=$i+1;
	echo "<td>$row</td>";
	echo "<td>$target</td>";
	echo "<td>$effect</td>";
	$get=get_user_effects($target,$effect,$username);
	$get['batch']=$batch;
	$get['username']='f';
	$get['user_target']=$target;
	extract($get);
	/*echo "<pre>";
	print_r($get);
	echo "</pre>";*/
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	//
	if($program=="f_bars")       f_bars      ($get);
	if($program=="f_spirals")    f_spirals   ($get);
	if($program=="f_butterfly")  f_butterfly ($get);
	if($program=="f_fire")       f_fire      ($get);
	if($program=="f_garlands")   f_garlands  ($get);
	if($program=="f_color_wash") f_color_wash($get);
	if($program=="f_gif")        f_gif       ($get);
	if($program=="f_life")       f_life      ($get);
	if($program=="f_meteors")    f_meteors   ($get);
	if($program=="f_text")       f_text      ($get);
	if($program=="f_pictures")   f_pictures      ($get);
	if($program=="f_user_defined") f_user_defined      ($get);
	if($program=="f_snowstorm")  f_snowstorm      ($get);
	//
	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
	echo "<td>$elapsed_time</td>";
	$gif="../effects/workspaces/2/" . $target . "~" . $effect ."_th.gif";
	$gif_big="../effects/workspaces/2/" . $target . "~" . $effect .".gif";
	echo "<td><a href=$gif_big><img src=\"$gif\" /></a></td>\n";
	//
	echo "</tr>";
	ob_flush();
	flush();
}
echo "</table>";
list($usec, $sec) = explode(' ', microtime());
$program_end = (float) $sec + (float) $usec;
$elapsed_time = round($program_end - $program_start, 5); // to 5 decimal places
$number_effects=$i+1;
echo "<h2>Total time to process these $number_effects effects was $elapsed_time seconds</h2>\n";
echo "</body>";
echo "</html>";
