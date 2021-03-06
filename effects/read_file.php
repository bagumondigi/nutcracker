<?php
define ("RED",0.9722);
define ("VIOLET",0.8055);
define ("BLUE",0.6388);
define ("CYAN",0.4722);
define ("GREEN",0.3055);
define ("YELLOW",0.1666);
define ("ORANGE",0.0833);

if(!function_exists('color_picker')){ 
	function color_picker($p,$maxPixel,$numberSpirals,$start_color,$end_color){
		/*
		* 	imagine RED to VIOLET
		* 	.972 to .896
		*
		*
		*/
		$start_dec = hexdec($start_color);
		$end_dec = hexdec($end_color);
		$rgb = $start_dec;
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		$HSL=RGB_TO_HSV ($r, $g, $b);
		$start_H=$HSL['H']; 
		$start_S=$HSL['S']; 
		$start_V=$HSL['V']; 
		$rgb = $end_dec;
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		$HSL=RGB_TO_HSV ($r, $g, $b);
		$end_H=$HSL['H']; 
		$end_S=$HSL['S']; 
		$end_V=$HSL['V']; 
		$range=$start_H-$end_H;
		if($start_H<$end_H)		$range=$end_H-$start_H;
		//if($range<0) $range+=1.0;
		$percentage = $p/$maxPixel; // 0 to 1.0
		if($start_H>$end_H)		$H = $start_H - $percentage*$range;
		else	$H = $start_H + $percentage*$range;
		if($H<0) $H+=1.0;
		$range=$start_S-$end_S;
		if($start_S<$end_S)		$range=$end_S-$start_S;
		//if($range<0) $range+=1.0;
		if($start_S>$end_S)		$S = $start_S - $percentage*$range;
		else	$S = $start_S + $percentage*$range;
		if($S<0) $S+=1.0;
		$range=$start_V-$end_V;
		if($start_V<$end_V)		$range=$end_V-$start_V;
		//if($range<0) $range+=1.0;
		if($start_V>$end_V)		$V = $start_V - $percentage*$range;
		else	$V = $start_V + $percentage*$range;
		if($V<0) $V+=1.0;
		$color_HSV=array('H'=>$H,'S'=>$S,'V'=>$V);
		return $color_HSV;
	}
}
if(!function_exists('read_file')){ 
	function read_file($file,$path){
		$max_i=-1;
		$min_user_pixel = $min_string = $min_strand=$min_pixel = 9999999;
		$max_user_pixel = $max_string = $max_strand=$max_pixel = -1;
		$lines=0;
		$full_path = $path . "/" . $file;
		$fh = fopen($full_path, 'r') or die("can't open file $full_path");
		$debug=0;
		$i=0;
		$min_x=99999;
		$min_y=99999;
		$min_z=99999;
		$max_x = -999999;
		$max_y = -999999;
		$max_z = -999999;
		#    ../targets/f/BB.dat
		#    Col 1: Your TARGET_MODEL_NAME
		#    Col 2: Strand number.
		#    Col 3: Nutcracker Pixel#
		#    Col 4: X location in world coordinates
		#    Col 5: Y location in world coordinates
		#    Col 6: Z location in world coordinates
		#    Col 7: User string
		#    Col 8: User pixel
		# 
		//BB   1   1   0.000   0.732 142.091     1    50
		//BB   1   2   0.000   1.465 139.182     1    49
		//BB   1   3   0.000   2.197 136.272     1    48
		//BB   1   4   0.000   2.929 133.363     1    47
		//BB   1   5   0.000   3.662 130.454     1    46
		while(!feof($fh)){
			$line = fgets($fh);
			$tok=preg_split("/ +/", $line);
			$l=strlen($line);
			$c= substr($line,0,1);
			if($l>20 and $c !="#"){
				$i++;
				$device=$tok[0];	// 0 device name
				$strand=$tok[1];	// 1 strand#
				$pixel=$tok[2];// 2 pixel#	
				$x=(float)$tok[3]; // 3 X value
				$y=(float)$tok[4];	// 4 Y value
				$z=(float)$tok[5];	// 5 Z value
				$rgb=$tok[6];	// 5 Z value
				$string=$tok[7];
				$user_pixel=$tok[8];
				if($x<$min_x) $min_x=$x;
				if($y<$min_y) $min_y=$y;
				if($z<$min_z){
					$min_z=$z;
				}
				if($x>$max_x){
					$max_x=$x;
				}
				if($y>$max_y){
					$max_y=$y;
				}
				if($z>$max_z){
					$max_z=$z;
				}
				if(empty($rgb))				$rgb=0;
				else			$rgb=$tok[6];
				//
				if($strand<$min_strand) $min_strand=$strand;
				if($pixel<$min_pixel) $min_pixel=$pixel;
				if($strand>$max_strand) $max_strand=$strand;
				if($pixel>$max_pixel) $max_pixel=$pixel;
				if($string<$min_string) $min_string=$string;
				if($user_pixel<$min_user_pixel) $min_user_pixel=$user_pixel;
				if($string>$max_string) $max_string=$string;
				if($user_pixel>$max_user_pixel) $max_user_pixel=$user_pixel;
				$tree_rgb[$strand][$pixel]=$rgb;
				//
				//$tree_user_string_pixel[$string][$user_pixel]['strand']=$strand;
				//$tree_user_string_pixel[$string][$user_pixel]['pixel']=$:w
				//pixel;
				$strand_pixel[$strand][$pixel]=array($string,$user_pixel);
				$tree_xyz[$strand][$pixel]=array($x,$y,$z,$rgb);
			}
		}
		fclose($fh);
		$min_max = array($min_x,$max_x,$min_y,$max_y,$min_z,$max_z);
		$arr = array($min_strand,$min_pixel,$max_strand,$max_pixel,$i,$tree_rgb,$tree_xyz,$file,$min_max,$strand_pixel);
		return $arr;
	}
}
if(!function_exists('fill_in_zeros')){ 
	function fill_in_zeros($arr,$dat_file_array){
		$minStrand =$arr[0];  // lowest strand seen on target
		$minPixel  =$arr[1];  // lowest pixel seen on skeleton
		$maxStrand =$arr[2];  // highest strand seen on target
		$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
		$maxI      =$arr[4];  // maximum number of pixels in target
		$tree_rgb  =$arr[5];
		$tree_xyz  =$arr[6];
		$file      =$arr[7];
		$min_max   =$arr[8];
		$strand_pixel=$arr[9];
		$tree_user_text1_pixel   =$arr[9];
		foreach($dat_file_array as $i=>$full_path){
			/*echo "<pre>Processing $full_path </pre>\n";*/
			$fh = fopen($full_path, 'r') or die("can't open file $full_path");
			/*
			Read a dat file and create a new dat file with all missing rgb values set to zero.
			#    workspaces/2/AA+TEXT1_d_15.dat
			#    Token
			#    Strand
			#    Pixel
			#    X
			#    Y
			#    ZZ
			#    rgb_val
			#    User_String
			#    User_pixel
			#    Strand_Pixel[0]
			#    Strand_Pixel[1]
			#    k
			#    seq_number
			t1    2   11     1.561     7.850   107.516 16713769 0 0 1 63
			t1    2   20     2.839    14.273    81.322 589617 0 0 1 72
			t1    2   12     1.703     8.564   104.606 16713769 0 0 1 64
			t1    2   21     2.981    14.986    78.412 0 0 0 1 73
			t1    2   13     1.845     9.277   101.695 16713769 0 0 1 65
			t1    2   22     3.123    15.700    75.501 0 0 0 1 74
			t1    2   14     1.987     9.991    98.785 16713769 0 0 1 66
			t1    2   23     3.265    16.413    72.591 0 0 0 1 75
			t1    2   15     2.129    10.704    95.874 16713769 0 0 1 67
			t1    2   24     3.407    17.127    69.680 0 0 0 1 76
			*/
			// Fill in a matrix with zeros.
			for($strand=1;$strand<=$maxStrand;$strand++)for($pixel=1;$pixel<=$maxPixel;$pixel++)			$matrix[$strand][$pixel]=-1;
			// Now go back and set any lines that have a non zero rgb value.
			while(!feof($fh)){
				$line = fgets($fh);
				$tok=preg_split("/ +/", $line);
				$cnt=count($tok);
				$l=strlen($line);
				$c= substr($line,0,1);
				if($c !="#" and $cnt>1){
					$device=$tok[0];	// 0 device name
					$strand=$tok[1];	// 1 strand#
					$pixel=$tok[2];// 2 pixel#	
					$rgb=$tok[6];
					if($rgb!=0)					$matrix[$strand][$pixel]=$line;  // store original line
				}
			}
			//	okay now put out new Files
			fclose($fh);
			$fh = fopen($full_path, 'w') or die("can't open file $full_path");
			for($strand=1;$strand<=$maxStrand;$strand++)for($pixel=1;$pixel<=$maxPixel;$pixel++){
				if($matrix[$strand][$pixel]==-1){
					fwrite($fh,sprintf ("%s %d %d 0.0 0.0 0.0 000000 0 0 %d %d\n","t1",$strand,$pixel,$strand_pixel[$strand][$pixel][0],$strand_pixel[$strand][$pixel][1]));
				}
				else{
					$line=$matrix[$strand][$pixel];
					fwrite($fh,sprintf("%s",$line));
				}
			}
			fclose($fh);
			unset($matrix);
		}
	}
}
if(!function_exists('RGBVAL_TO_HSV')){ 
	function RGBVAL_TO_HSV($rgb_val){
		$rgb = $rgb_val;
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;
		$HSL=RGB_TO_HSV ($r, $g, $b);
		return $HSL;
	}
}
//
if(!function_exists('RGB_TO_HSV')){ 
	function RGB_TO_HSV ($R, $G, $B){
		$HSL = array(); 
		$var_R = ($R / 255); 
		$var_G = ($G / 255); 
		$var_B = ($B / 255); 
		$var_Min = min($var_R, $var_G, $var_B); 
		$var_Max = max($var_R, $var_G, $var_B); 
		$del_Max = $var_Max - $var_Min; 
		$V = $var_Max; 
		if($del_Max == 0){
			$H = 0; 
			$S = 0;
		}
		else{ 
			$S = $del_Max / $var_Max; 
			$del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max; 
			$del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max; 
			$del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max; 
			if($var_R == $var_Max) $H = $del_B - $del_G; 
			elseif($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B; 
			elseif($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R; 
			if($H<0) $H++; 
			if($H>1) $H--;
		}
		$HSL['H'] = $H; 
		$HSL['S'] = $S; 
		$HSL['V'] = $V; 
		return $HSL;
	}
}
if(!function_exists('HSV_TO_RGB')){ 
	function HSV_TO_RGB ($H, $S, $V){
	

		$RGB = array(); 
		if($H>1) $H=1;
		if($S>1) $S=1;
		if($V>1) $V=1;
		if($S == 0){
			$R = $G = $B = $V * 255;
		}
		else{ 
			$var_H = $H * 6; 
			$var_i = floor( $var_H ); 
			// M 
			$var_1 = $V * ( 1 - $S ); 
			// N 
			$var_2 = $V * ( 1 - $S * ( $var_H - $var_i ) ); 
			// K
			$var_3 = $V * ( 1 - $S * (1 - ( $var_H - $var_i ) ) ); 
			if($var_i == 0){
				$var_R = $V     ; $var_G = $var_3  ; $var_B = $var_1 ;
			}
			elseif($var_i == 1){
				$var_R = $var_2 ; $var_G = $V      ; $var_B = $var_1 ;
			}
			elseif($var_i == 2){
				$var_R = $var_1 ; $var_G = $V      ; $var_B = $var_3 ;
			}
			elseif($var_i == 3){
				$var_R = $var_1 ; $var_G = $var_2  ; $var_B = $V     ;
			}
			elseif($var_i == 4){
				$var_R = $var_3 ; $var_G = $var_1  ; $var_B = $V     ;
			}
			else{ 
				$var_R = $V     ; $var_G = $var_1  ; $var_B = $var_2 ;
			}
			$R = $var_R * 255; 
			$G = $var_G * 255; 
			$B = $var_B * 255;
		}
		$RGB['R'] = $R + 0.5; 
		$RGB['G'] = $G + 0.5; 
		$RGB['B'] = $B + 0.5; 
		$R=intval($RGB['R']);
		$G=intval($RGB['G']);
		$B=intval($RGB['B']);
		$rgb_val =  $R *256*256 + $G *256 +$B;
		//return $RGB;
		return $rgb_val;
	}
}
if(!function_exists('getWindowArray')){
	function getWindowArray($minStrand,$maxStrand,$degrees){
		$delta = $maxStrand-$minStrand+1;
		if(empty($degrees)) $degrees=360;
		if($degrees<1) $degrees=360;
		$degrees2=$degrees/2;
		$half_window = $delta * $degrees2/360;
		$right_window = intval($half_window);
		$left_window = intval($delta - $half_window);
		// old methof, +/- 90 degrees for a half window.
		/*for ($i=$right_window;$i>=1;$i--)
		$windowStrandArray[]=$i;
		for ($i=$maxStrand;$i>=$left_window;$i--)
		$windowStrandArray[]=$i;*/
		// new method, half megatree always starts at strand 
		$startStrand=1;
		if($degrees>0)		$endStrand=$maxStrand / (360/$degrees);
		else	$endStrand=$startStrand;
		$windowStrandArray=array();
		for($i=$startStrand;$i<=$endStrand;$i++){
			$windowStrandArray[]=$i;
		}
		if($degrees==360){
			$windowStrandArray=array();
			for($i=$minStrand;$i<=$maxStrand;$i++){
				$windowStrandArray[]=$i;
			}
		}
		return $windowStrandArray;
	}
}
if(!function_exists('write_dat')){
	function write_dat($tag,$fh_dat,$arr,$s,$p,$rgb_val){
		$minStrand =$arr[0];  // lowest strand seen on target
		$minPixel  =$arr[1];  // lowest pixel seen on skeleton
		$maxStrand =$arr[2];  // highest strand seen on target
		$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
		$maxI      =$arr[4];  // maximum number of pixels in target
		$tree_rgb  =$arr[5];
		$tree_xyz  =$arr[6];
		if(  ($s>=$minStrand and $s <=$maxStrand) and
			($p>=$minPixel and $p<=$maxPixel)){
			$xyz=$tree_xyz[$s][$p];
			$hex=dechex($rgb_val);
			fwrite($fh_dat,sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d # $hex : \n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val));
			//	printf ("<pre>[%s] %4d %4d %9.3f %9.3f %9.3f %d</pre>\n",$tag,$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val);
		}
	}
}
if(!function_exists('gp_header')){
	function gp_header($fh,$min_max,$target_info){
	
	
		$min_x=$min_max[0];
		$max_x=$min_max[1];
		$min_y=$min_max[2];
		$max_y=$min_max[3];
		$min_z=$min_max[4];
		$max_z=$min_max[5];
		/*echo "<pre>";
		print_r($min_max);
		echo "</pre>\n";*/
		extract($target_info);
		/*	Target Info:
		Array
		(
		[target_name] => AA
		[model_type] => MTREE
		[total_strings] => 16
		[pixel_count] => 105
		[pixel_length] => 3.00
		[pixel_spacing] => 
		[unit_of_measure] => in
		[topography] => 
		)
		*/
		/*echo "<pre>";
		print_r($target_info);
		echo "</pre>\n";*/
		$model_type=$target_info['model_type'];
		fwrite($fh,"set notitle\n" );
		fwrite($fh,"set ylabel\n" );
		fwrite($fh,"set xlabel\n" );
		fwrite($fh,sprintf("unset xtics\n"));
		fwrite($fh,sprintf("unset ytics\n"));
		fwrite($fh,sprintf("unset label\n"));
		fwrite($fh,sprintf("set bmargin 0\n"));
		fwrite($fh,sprintf("set tmargin 0\n"));
		fwrite($fh,sprintf("set lmargin 0\n"));
		fwrite($fh,sprintf("set rmargin 0\n"));
		if($model_type=='SINGLE_STRAND' and $gif_model != 'window'){
			$min_x *= .5;
			$max_x *= .9;
			$min_y *= .8;
			$max_y *= .8;
			fwrite($fh,sprintf("set autoscale\n"));
			fwrite($fh,sprintf("set xrange[0:]\n"));
			fwrite($fh,sprintf("set yrange[0:1]\n"));
			//fwrite($fh,sprintf("set zrange[0:]\n"));
			//	fwrite($fh,sprintf("set arrow from 0,0,0 to 50,0,100 nohead lc rgb 'red'\n"));
			//	fwrite($fh,sprintf("set arrow from 0,0,0 to 50,40,0 nohead lc rgb 'green'\n"));
		}
		else{
			$scale=0.15;
			$xrange=$max_x-$min_x;
			$yrange=$max_y-$min_y;
			$zrange=$max_z-$min_z;
			$min_x = $min_x - $xrange*$scale;
			$max_x = $max_x + $xrange*$scale;
			if($min_x==0) $min_x=-1;
			if($max_x==0) $max_x=1;
			$min_y = $min_y - $yrange*$scale;
			$max_y = $max_y + $yrange*$scale;
			if($min_z==0) $min_z=-1;
			if($max_z==0) $max_z=1;
			$min_z = $min_z - $zrange*$scale;
			$max_z = $max_z + $zrange*$scale;
			if($min_y==0) $min_y=-1;
			if($max_y==0) $max_y=1;
			//	if($min_y<0.001) $min_y=$min_x;
			//	if($max_y<0.001) $max_y=$max_x;
			fwrite($fh,sprintf("set xrange[%5.0f:%5.0f]\n",$min_x,$max_x));
			fwrite($fh,sprintf("set yrange[%5.0f:%5.0f]\n",$min_y,$max_y));
			$top_height = $max_z* 1.1;
			$bottom_height = $max_z* -0.3;
			//	fwrite($fh,sprintf("set zrange[%5.0f:%5.0f]\n",$bottom_height,$top_height));
			fwrite($fh,sprintf("set zrange[%5.0f:%5.0f]\n",$min_z,$max_z));
			/*fwrite($fh,sprintf("set autoscale x\n"));
			fwrite($fh,sprintf("set autoscale y\n"));
			fwrite($fh,sprintf("set autoscale z\n"));*/
			//fwrite($fh,sprintf("set autoscale\n"));
		}
		fwrite($fh,"unset border\n" );
		fwrite($fh,"set angles degrees\n" );
		fwrite($fh,"set object 1 rectangle from screen 0,0 to screen 1,1 fillcolor rgb \"black\" behind\n");
		fwrite($fh,"unset key\n" );
		fwrite($fh,"# model type = $model_type\n" );
		if($model_type=='MTREE'){
			fwrite($fh,"set view 95,270, 2.0, 1\n");  // old: fwrite($fh,"set view 105, 0, 2.0, 1\n");
	
		}
		elseif($model_type=='HORIZ_MATRIX'){
			fwrite($fh,"set view 105, 0, 1.0, 1\n");
		}
		elseif($model_type=='SINGLE_STRAND'){
			fwrite($fh,"set view 90,0, 1, 1\n");
		}
		else{
			fwrite($fh,"set view 105, 180, 2.0, 1\n");
		}
		fwrite($fh,"set style data lines\n" );
		fwrite($fh,"set noxtics\n" );
		fwrite($fh,"set noytics\n" );
		fwrite($fh,"set noztics\n" );
		fwrite($fh,"set style line 1  linetype 1 linecolor rgb \"red\"  linewidth 3.000 pointtype 1 pointsize default\n" );
		fwrite($fh,"set style line 2  linetype 2 linecolor rgb \"green\"  linewidth 3.000 pointtype 2 pointsize default\n");
		fwrite($fh,"set style line 3  linetype 3 linecolor rgb \"blue\"  linewidth 3.000 pointtype 3 pointsize default\n" );
		fwrite($fh,"set style line 4  linetype 4 linecolor rgb \"yellow\"  linewidth 3.000 pointtype 4 pointsize default\n");
		fwrite($fh,"set style line 5  linetype 5 linecolor rgb \"cyan\"  linewidth 3.000 pointtype 5 pointsize default\n");
		fwrite($fh,"set style line 6  linetype 6 linecolor rgb \"orange\"  linewidth 3.000 pointtype 6 pointsize default\n");
		fwrite($fh,"set style line 7  linetype 7 linecolor rgb \"gray\"  linewidth 1.000 pointtype 7 pointsize 0.3\n");
		fflush($fh);
	}
}
if(!function_exists('my_exec')){
	function my_exec($cmd, $input=''){
		$proc=proc_open($cmd, array(0=>array('pipe', 'r'), 1=>array('pipe', 'w'), 2=>array('pipe', 'w')), $pipes); 
		fwrite($pipes[0], $input);fclose($pipes[0]); 
		$stdout=stream_get_contents($pipes[1]);fclose($pipes[1]); 
		$stderr=stream_get_contents($pipes[2]);fclose($pipes[2]); 
		$rtn=proc_close($proc); 
		return array('stdout'=>$stdout, 
			'stderr'=>$stderr, 
			'return'=>$rtn 
		);
	}
}

if(!function_exists('display_gif')){
	function display_gif($batch,$dir,$model,$gp_file,$out_file_array,$frame_delay){
		//
		////	to speed up imagmagick: +dither or -treedepth 4 -colors 256.
		//
		// gp_file=../targets/2/AA+GAR_SPACE4.gp
		//
		$path_parts = pathinfo($gp_file);
		/*echo "<pre>";
		print_r($out_file_array);
		echo "</pre>";*/
		$dat_file_array0=$out_file_array[0];
		$dirname   = $path_parts['dirname']; // workspaces/2
		$basename  = $path_parts['basename']; // AA+CIRCLE1_d_1.dat
		$extension =$path_parts['extension']; // .dat
		$filename  = $path_parts['filename']; //  AA+CIRCLE1_d_1
		$tok=explode("/",$dirname);
		$c=count($tok);
		$member_id=$tok[$c-1];
		$tokens=explode(".",$gp_file);
		$gif_file = $tokens[0] .  ".gif";
		$gif_file = $dirname . "/" . $filename .  ".gif";
		//	$gif_file_th = $tokens[0] .  "_th.gif";
		$debug=0;
		if($debug==1){
			echo "<pre>";
			echo "dirname=$dirname\n";
			echo "basename=$basename\n";
			echo "extension=$extension\n";
			echo "filename=$filename\n";
			echo "member_id=$member_id\n";
			echo "gp_file=$gp_file\n";
			echo "gif_file=$gif_file\n";
			echo "</pre>\n";
		}
		if(file_exists($gp_file)){
			$cwd=getcwd();
			// pcntl_exec($programexe,$programvars); 		
			//   $shellCommmand = "/usr/local/bin/gnuplot '" . $pathToCommandFile .+"'";
			//         $output = system($shellCommmand . " 2>&1");
			// [SERVER_NAME] => nutcracker123.com
			//
			ini_set('include_path', '/home5/nutcrcom/gnuplot/bin');
			$n=strpos($_SERVER['HTTP_HOST'],"cracker123");
			//		if($_SERVER['HTTP_HOST'] == 'nutcracker123.com')
			if($n>0){
				// $shellCommand = $_SERVER['DOCUMENT_ROOT']."/gnuplot/bin/gnuplot " . 
				$shellCommand = "../gnuplot/bin/gnuplot " . 
				$gp_file .  " 2>&1";
			}
			else{
				$shellCommand = "gnuplot " . realpath($gp_file) .  " 2>&1"; // localhost runs
			}
			$return=system($shellCommand,$output); 
			//echo "<pre>cwd=$cwd, shellcommand = $shellCommand, output=$output, return=$return</pre>\n";
		}
		else{
			echo "ERROR! The file $gp_file does not exist\n";
		}
		if($batch<=1) printf ("<img src=\"%s\"/>",$gif_file);
	}
}
if(!function_exists('insert_sequence')){
	function insert_sequence($username,$model_name,$frame,$string,$user_pixel,$c,$rgb){
		echo "<pre>	insert_sequence($username,$model_name,$frame,$string,$user_pixel,$c,$rgb)</pre>\n";
	}
}
/*function get_model_array($username,$model_name)
{
//echo "<pre> get_model_array($username,$model_name)";
//Include database connection details
require_once('../conf/config.php');
//Connect to mysql server
$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
if(!$link)
{
die('Failed to connect to server: ' . mysql_error());
}
//Select database
$db = mysql_select_db(DB_DATABASE);
if(!$db)
{
die("Unable to select database");
}
$model_name_base = basename($model_name,".dat");
$query="SELECT b.total_strings, MAX( user_pixel ) AS max_user_pixel
FROM  `model_dtl` a, models b
WHERE a.username = b.username
AND a.object_name = b.object_name
and a.username='$username'
and a.object_name='$model_name_base'";
$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
while ($row = mysql_fetch_assoc($result))
{
extract($row);
}
$model_array = array('total_strings'=>$total_strings,'max_user_pixel'=>$max_user_pixel);
mysql_free_result($result);
mysql_close();
return ($model_array);
}
*/
if(!function_exists('insert_sequence')){
	function get_strand_pixel($username,$model_name,$string,$user_pixel){
		require_once('../conf/config.php');
		//Connect to mysql server
		$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
		if(!$link){
			die('Failed to connect to server: ' . mysql_error());
		}
		//Select database
		$db = mysql_select_db(DB_DATABASE);
		if(!$db){
			die("Unable to select database");
		}
		$model_name_base = basename($model_name,".dat");
		//	mode_dtl: username	object_name	strand	pixel	string	user_pixel	created	last_upd
		$query="SELECT  a.strand,a.pixel
		FROM `model_dtl` a
		where a.username='$username'
		and a.string=$string
		and a.user_pixel=$user_pixel
		and a.object_name='$model_name_base'";
		$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
		$model_array=array();
		while($row = mysql_fetch_assoc($result)){
			extract($row);
		}
		$strand_pixel_array=array($strand,$pixel);
		mysql_free_result($result);
		mysql_close();
		return ($strand_pixel_array);
	}
}
if(!function_exists('make_amperage_datefile')){
	function make_amperage_datefile($loop,$amperage,$dat_file){
		$fh = fopen($dat_file, 'w') or die("can't open file");
		fwrite($fh,"#	 $dat_file\n");
		$maxStrand=0;
		foreach($amperage as $i => $n1){
			foreach($n1 as $s => $value){
				if($s>$maxStrand) $maxStrand=$s;
			}
		}
		for($s=1;$s<=$maxStrand;$s++){
			$val=$amperage[$loop][$s];
			fwrite($fh,sprintf ("%6d %8.3f\n",$s,$val) );
		}
		fclose($fh);
	}
}
if(!function_exists('rotate')){
	function rotate($arr,$x_dat,$t_dat,$path,$direction,$halfTree,$frame_delay,$sparkles,$window_degrees,$username,$sequence_duration){
		//show_elapsed_time($script_start,"Starting  Rotate image about Z axis");
		// now rotate image that we just made, dat file is $x_dat
		$x_arr=read_file($x_dat,$path); //  target megatree 32 strands, all 32 being used. read data into an array
		$minStrand =$arr[0];  // lowest strand seen on target
		$minPixel  =$arr[1];  // lowest pixel seen on skeleton
		$maxStrand =$arr[2];  // highest strand seen on target
		$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
		$maxI      =$arr[4];  // maximum number of pixels in target
		$tree_rgb  =$arr[5];
		$tree_xyz  =$arr[6];
		$file      =$arr[7];
		$min_max   =$arr[8];
		$tree_user_string_pixel   =$arr[9];
		$file = $arr[7];
		//	define the front half of the megatree. strand=1 is center of tree facing street
		//	get a window of the strands that shoudl be shown.
		//	if window=360 degrees, then we show all
		//	180 drgrees we only show strands that are +/- 90 degrees each side of strand 1
		//
		$window_array=getWindowArray($minStrand,$maxStrand,$window_degrees);
		for($l=1;$l<=$maxStrand;$l++){
			$lxxx = substr($l+1000,1,3);
			$tokens=explode(".",$t_dat);
			$base=$tokens[0];
			$dat_file = $path . "/" . $base . "_r_$lxxx" . ".dat"; // for spirals we will use a dat filename starting "S_" and the tree model
			$dat_file_array[]=$dat_file;
			$fh_dat = fopen($dat_file, 'w') or die("can't open file");
			fwrite($fh_dat,"#    $dat_file\n");
			$full_path = $path . "/" . $x_dat;
			$fh = fopen($full_path, 'r') or die("can't open file $full_path");
			$i=0;
			for($s=1;$s<=$maxStramd;$s++)			$amperage[$l][$s]=0;  // we calcultare amperage for each strand.
			while(!feof($fh)){
				$line = fgets($fh);
				$tok=preg_split("/ +/", $line);
				$cnt = count($tok);
				if($cnt<7) continue;
				$device=$tok[0];	// 0 device name
				$strand=$tok[1];	// 1 strand#
				$pixel=$tok[2];// 2 pixel#	
				$p=$pixel;
				$s=$strand;
				$x=$tok[3]; // 3 X value
				$y=$tok[4];	// 4 Y value
				$z=$tok[5];	// 5 Z value
				if(empty($tok[6]))				$rgb_val=0;
				else			$rgb_val=$tok[6];
				if($direction=="ccw")			$new_s = $strand+$l; // CCW
				else			$new_s = $strand-$l; // CW
				if($new_s>$maxStrand) $new_s = $new_s - $maxStrand;
				if($new_s<$minStrand) $new_s = $new_s + $maxStrand;
				$xyz=$tree_xyz[$new_s][$p];
				if($sparkles>0){
					if($sparkles>100) $sparkles=100;
					$rnd=rand(1,100);
					if($rnd<$sparkles){
						$r=$g=$b=255;		
						$HSL=RGB_TO_HSV ($r, $g, $b);
						$H=$HSL['H']; 
						$S=$HSL['S']; 
						$V=$HSL['V']; 
						$rgb_val=HSV_TO_RGB ($H, $S, $V);
					}
				}
				if(in_array($new_s,$window_array)){
					$tree_rgb[$new_s][$p]=$rgb_val;
					//		fwrite($fh_dat,sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d # %4d %4d %9.3f %9.3f %9.3f\n",$new_s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val,$strand,$pixel,$x,$y,$z));
					//
					//  get the VALUE of the rgb value, this tells how intense the LED is turned on
					$r = ($rgb_val >> 16) & 0xFF;
					$g = ($rgb_val >> 8) & 0xFF;
					$b = $rgb_val & 0xFF;
					$HSL=RGB_TO_HSV ($r, $g, $b);
					$H=$HSL['H']; 
					$S=$HSL['S'];  // Saturation. 1.0 = Full on, 0=off
					$V=$HSL['V']; 
					if($rgb_val>0){
						$amperage[$l][$new_s] += $V*0.060; // assume 29ma for pixels tobe full on
						//printf ( "<pre>S,p=%d,%d rgb=%d,%d,%d HSV=%f,%f,%f amperage=%f</PRE>\n", $s,$p,$r,$g,$b,$H,$S,$V,$amperage);
						fwrite($fh_dat,sprintf ("t1 %4d %4d %9.3f %9.3f %9.3f %d\n",$s,$p,$xyz[0],$xyz[1],$xyz[2],$rgb_val));
					}
				}
			}
		}
		fclose($fh);  // x_dat
		fclose($fh_dat);  // dat_file
		$base_t_dat=$t_dat;
		//show_elapsed_time($script_start,"Finished  Rotate image about Z axis");
		// make_rotate_gp($path,$base_t_dat,$x_dat,$height,$dat_file_array);
		make_gp($batch,$path,$x_dat,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$sequence_duration,$show_frame);
	}
}

function show_srt_file($file,$maxFrame,$frame_delay,$maxPixel,$pixel_count){
	echo "<h2>Channel Preview</h2>";
	echo "<h3>To save space, only the first 5 strings are displayed</h3>";
	/* 1     25     10    589617 # 40  t1    1   26     0.000    18.918    58.493 589617 0 0 1 25
	1     25     15    589617 # 36  t1    1   26     0.000    18.918    58.493 589617 0 0 1 25
	1     25     16    589617 # 34  t1    1   26     0.000    18.918    58.493 589617 0 0 1 25
	1     25     17    589617 # 37  t1    1   26     0.000    18.918    58.493 589617 0 0 1 25
	1     25     20    589617 # 41  t1    1   26     0.000    18.918    58.493 589617 0 0 1 25
	1     25     24    589617 # 33  t1    1   26     0.000    18.918    58.493 589617 0 0 1 25
	1     25     26    589617 # 31  t1    1   26     0.000    18.918    58.493 589617 0 0 1 25
	1     25     27    589617 # 21  t1    1   26     0.000    18.918    58.493 589617 0 0 1 25
	1     25     28    589617 # 19  t1    1   26     0.000    18.918    58.493 589617 0 0 1 25*/
	$fh = fopen($file, 'r') or die("can't open file $file");
	$linecounter=$oldString=$oldPixel=0;
	echo "<table border=1>";
	for($i=1;$i<=$maxFrame;$i++)		$buff[$i]=0;
	while(!feof($fh)){
		$linecounter++;
		$line = fgets($fh);
		$tok=preg_split("/ +/", $line);
		$tokens=count($tok);
		$l=strlen($line);
		$c= substr($line,0,1);
		if($tokens>3){
			/*echo "<pre>";
			print_r($tok);
			echo "</pre>";
			*/
			$string=$tok[1];
			$pixel=$tok[2];
			$frame=$tok[3];
			$rgb=$tok[4];
			$buff[$frame]=$rgb;
			/*echo "<tr><td>$linecounter</td><td>s=$string</td>";
			echo "<td>p=$pixel</td>";
			echo "<td>f=$frame</td></tr>";*/
			if($pixel>=1 and ($string!=$oldString or $pixel!=$oldPixel)){
				if($string<=5){
					if($linecounter==1){
						echo "<tr><th>&nbsp;</th><th>Frame timing = $frame_delay ms</th>";
						echo "<th colspan=$maxFrame>Max Frames = $maxFrame,";
						echo "Max Pixels = $maxPixel,pixel_count=$pixel_count</th></tr>";
						echo "<tr><th><b>Timing</b></th>";
						for($i=1;$i<=$maxFrame;$i++){
							$secs=intval(($i*$frame_delay)/1000);
							echo "<th>$secs</th>";
						}
						echo "</tr>";
					}
					if($string!=$oldString and $oldPixel>$pixel ) // if the first pixel we read is NOT pixel 1, output those missing rows.
					{
						if($oldPixel< $pixel_count ){
							for($p=$oldPixel+1;$p<=$pixel_count;$p++) // take care of pixel 97,98,99,100
							{
								echo "<tr><td><b>S$oldString</td><td>P$p</b></td>";
								for($i=1;$i<=$maxFrame;$i++){
									echo "<td bgcolor=\"#AAAAAA\">&nbsp;</td>";
									$buff[$i]=0;
									/*	$big_buff[$p][$i]=0;*/
								}
								echo "</tr>";
							}
						}
						if($pixel>1){
							for($p=1;$p<$pixel;$p++){
								echo "<tr><td><b>S$string</td><td>P$p</b></td>";
								for($i=1;$i<=$maxFrame;$i++){
									echo "<td bgcolor=\"#CCCCCC\">&nbsp;</td>";
									$buff[$i]=0;
									/*	$big_buff[$p][$i]=0;*/
								}
								echo "</tr>";
							}
						}
					}
					if($string!=$oldString and $string==1 and $pixel>1) // special case, first string missing first pixels
					{
						for($p=1;$p<$pixel;$p++){
							echo "<tr><td><b>S$string</td><td>P$p</b></td>";
							for($i=1;$i<=$maxFrame;$i++){
								echo "<td bgcolor=\"#EEEEEE\">&nbsp;</td>";
								$buff[$i]=0;
								/*$big_buff[$p][$i]=0;*/
							}
							echo "</tr>";
						}
					} 
					if($string==$oldString and ($pixel-$oldPixel)>1) // on same string, missing pixels in middle.Ex: text
					{
						for($p=$oldPixel+1;$p<$pixel;$p++){
							echo "<tr><td><b>S$string</td><td>P$p</b></td>";
							for($i=1;$i<=$maxFrame;$i++){
								echo "<td bgcolor=\"#BBBBBB\">&nbsp;</td>";
								$buff[$i]=0;
								/*	$big_buff[$p][$i]=0;*/
							}
							echo "</tr>";
						}
					} 
					echo "<tr><td><b>S$string</td><td>P$pixel</b></td>";
					for($i=1;$i<=$maxFrame;$i++){
						$rgb_val=$buff[$i];
						$r = ($rgb_val >> 16) & 0xFF;
						$g = ($rgb_val >> 8) & 0xFF;
						$b = $rgb_val & 0xFF;
						$hex = fromRGB($r,$g,$b);
						echo "<td bgcolor=\"$hex\">&nbsp;</td>";
						/*$big_buff[$pixel][$i]=$buff[$i];*/
						$buff[$i]=0;
					}
					echo "</tr>";
					$buff[$frame]=$rgb;
					//	ob_flush();
				}
			}
			/*if($string != $old_string)
			for($p=$pixel_count;$p>=1;$p--)
			{
			for($i=1;$i<=$maxFrame;$i++)
			{
			$rgb_val=$big_buff[$p][$i];
			$r = ($rgb_val >> 16) & 0xFF;
			$g = ($rgb_val >> 8) & 0xFF;
			$b = $rgb_val & 0xFF;
			$hex = fromRGB($r,$g,$b);
			echo "<td bgcolor=\"$hex\">&nbsp;</td>";
			}
			echo "</tr>";
			}
			*/
			$oldString=$string;
			$oldPixel=$pixel;
		}
	}
	echo "</table>";
}

function fromRGB($R, $G, $B){
	$hex = "#";
	$hex.= str_pad(dechex($R), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($G), 2, "0", STR_PAD_LEFT);
	$hex.= str_pad(dechex($B), 2, "0", STR_PAD_LEFT);
	return $hex;
}

function show_array($array,$title){
	echo "<table border=1>";
	echo "<tr><th colspan=2>$title</th></tr>";
	foreach($array AS $key => $value){
		echo "<tr>";
		if(is_array($value))			$color="white";
		elseif(substr($value,0,1)=="#")			$color=$value;
		else		$color="white";
		if(!is_array($value)){
			echo "<td>$key</td>";
			echo "<td bgcolor=$color>$value</td>";
			echo "</tr>\n";
		}
	}
	echo "</table>";
}
/*
(
[username] => f
[user_target] => MT
[effect_class] => spirals
[effect_name] => 44
[number_spirals] => 4
[number_rotations] => 2
[spiral_thickness] => 1
[start_color] => #26FF35
[end_color] => #2E35FF
[frame_delay] => 22
[direction] => 2
[submit] => Submit Form to create your target model
)
*/

function save_user_effect($passed_array){
	//Include database connection details
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	extract($passed_array);
	/*echo "<pre>";
	print_r($passed_array);
	echo "</pre>";*/
	$effect_name = strtoupper($effect_name);
	$effect_name = rtrim($effect_name);
	if(isset($direction)) $direction = strtolower($direction);
	else $direction='';
	$username=str_replace("%20"," ",$username);
	$effect_name=str_replace("%20"," ",$effect_name);
	if(empty($sparkles)) $sparkles=0;
	//	show_array($passed_array,"passed_array");
	//	insert into the header
	// 	effect_class	username	effect_name	effect_desc	created	last_upd
	//
	$effect_desc="desc";
	$effect_id = get_effect_id($username,$effect_name);
	if(!isset($effect_id)){
		$insert = "INSERT into effects_user_hdr( effect_class,username,effect_name,effect_desc,last_upd)
		values ('$effect_class','$username','$effect_name','$effect_desc',now())";
	}
	else{
		$insert = "INSERT into effects_user_hdr( effect_class,username,effect_name,effect_desc,last_upd)
		values ('$effect_class','$username','$effect_name','$effect_desc',now())
		ON DUPLICATE KEY update effect_id='$effect_id',
		effect_class='$effect_class',
		username='$username',
		effect_name='$effect_name',
		effect_desc='$effect_desc',
		last_upd=now()
		";
	}
	//echo "<pre>$insert</pre>\n";
	$result=mysql_query($insert) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . 
		$insert . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	//mysql_free_result($result);
	$query = "select param_name from effects_dtl where effect_class = '$effect_class'";
	//echo "<pre>$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . 
		$query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	$param_name_array=array();
	while($row = mysql_fetch_assoc($result)){
		extract($row);
		$param_name_array[]=$param_name;
	}
	//show_array($param_name_array,"param_name_array");
	/*
	http://dev.mysql.com/doc/refman/5.0/en/insert-on-duplicate.html
	INSERT INTO test (a,b) VALUES ('1','2') ON DUPLICATE KEY UPDATE ID=LAST_INSERT_ID(ID),Dummy = NOT dummy;
	Now, SELECT LAST_INSERT_ID(); will return the correct ID.
	old way
	$insert2 = "REPLACE into effects_user_dtl(effect_id,username,effect_name,param_name,param_value,last_upd) 
	values ($effect_id,'$username','$effect_name','$key','$value',now())";
	INSERT INTO table (a,b,c) VALUES ($product_info[0],$product_info[1],$product_info[2])
	ON DUPLICATE KEY UPDATE a='$product_info[0]', b='$product_info[1]', c='$product_info[2]'
	*/
	mysql_free_result($result);
	$skip_these=array('submit','OBJECT_NAME');
	foreach($passed_array AS $key => $value){
		//	$key=strtolower($key);
		if(in_array($key,$param_name_array)){
			//	login	effect_name	param_name	param_value	created	last_upd
			//
			if(!isset($effect_id)){
				$insert2 = "INSERT into effects_user_dtl(username,effect_name,param_name,param_value,last_upd) 
				values ('$username','$effect_name','$key','$value',now())";
			}
			else{
				$insert2 = "INSERT into effects_user_dtl(effect_id,username,effect_name,param_name,param_value,last_upd) 
				values ($effect_id,'$username','$effect_name','$key','$value',now())
				ON DUPLICATE KEY update effect_id='$effect_id',
				username='$username',
				effect_name='$effect_name',
				param_name='$key',
				param_value='$value',
				last_upd=now()
				";
			}
			mysql_query($insert2) or die ("Error on $insert2");
			//mysql_free_result($result);
		}
	}
	$date_field= date('Y-m-d');
	$time_field= date("H:i:s");
	$query="INSERT into audit_log values ('$username','$date_field','$time_field','effect','$OBJECT_NAME')";
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//mysql_free_result($result);
	mysql_close();
	$_SESSION['SESS_LOGIN'] = $username;
	session_write_close();
	//header("location: target-exec.php?model=$OBJECT_NAME?user=$username");
	//exit();
}
//$target_info=get_info_target($username,$t_dat);

function get_info_target($username,$t_dat){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	// username	object_name	object_desc	model_type	string_type	pixel_count	pixel_first	pixel_last	pixel_length	unit_of_measure	total_strings	direction	orientation	topography	h1	h2	d1	d2	d3	d4
	//
	$base = basename($t_dat,".dat");
	$query ="select * from models where username='$username' and object_name='$base'";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if(!$result){
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if(mysql_num_rows($result) == 0){
		$NO_DATA_FOUND=1;
	}
	$target_info=array();
	if(!$NO_DATA_FOUND){
		// username	object_name	object_desc	model_type	string_type	pixel_count	pixel_first	pixel_last	pixel_length	unit_of_measure	total_strings	direction	orientation	topography	h1	h2	d1	d2	d3	d4
		//
		//
		while($row = mysql_fetch_assoc($result)){
			extract($row);
			$target_info['target_name']=$object_name;
			$target_info['model_type']=$model_type;
			$target_info['total_strings']=$total_strings;
			$target_info['pixel_count']=$pixel_count;
			$target_info['pixel_length']=$pixel_length;
			$target_info['pixel_spacing']=$pixel_spacing; // PIXEL FIX
			$target_info['unit_of_measure']=$unit_of_measure;
			$target_info['topography']=$topography;
		}
	}
	mysql_free_result($result);
	mysql_close();
	return $target_info;
}

function show_elapsed_time($script_start,$description){
	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
	//if($description = 'Total Elapsed time for this effect:')
	//
	//	turn it off for now
	//printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
}

function get_member_id($username){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	$query = "select member_id from members where username='$username'";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	$member_id=0;
	while($row = mysql_fetch_assoc($result)){
		extract($row);
	}
	mysql_close();
	if($member_id==0)		echo "<pre>ERROR: We did not find username [$username]</pre>\n";
	return ($member_id);
}

function get_username($member_id){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	$query = "select username from members where member_id='$member_id'";
	$username='';
	//echo "$query";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	while($row = mysql_fetch_assoc($result)){
		extract($row);
	}
	return ($username);
}

function sparkles($sparkles,$rgb_val){
	if($sparkles<=0) return $rgb_val;
	//	$sparkles should be a number form 0-100. This indicates percentage of pixel to override with white
	if($sparkles>100) $sparkles=100;
	$rnd=rand(1,100);
	if($rnd<$sparkles){
		$r=$g=$b=255;		
		$HSL=RGB_TO_HSV ($r, $g, $b);
		$H=$HSL['H']; 
		$S=$HSL['S']; 
		$V=$HSL['V']; 
		$rgb_val=HSV_TO_RGB ($H, $S, $V);
	}
	return $rgb_val;
}

function make_gp($batch,$arr,$path,$x_dat,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame){
	//	echo "<pre>function make_gp($batch,$path,$x_dat,$t_dat,$dat_file_array,$min_max,$username,$frame_delay,$amperage,$seq_duration,$show_frame)</pre>\n";
	//  make_gp($batch,workspaces/f,AA+SEAN3.dat,AA.dat,Array,Array,f,5,1331320308.6,Array)
	//
	fill_in_zeros($arr,$dat_file_array); // modify dat files so they have missingg data
	$tok = explode(".dat",$t_dat);
	$target=$tok[0];
	$target_info=get_info_target($username,$t_dat);
	$gif_model=get_gif_model($username,$target);
	$target_info['gif_model']=$gif_model;
	/*echo "<pre>Target Info:\n";
	print_r($target_info);
	echo "</pre>";*/
	extract ($target_info);
	/*	Target Info:
	Array
	(
	[target_name] => A1
	[model_type] => SINGLE_STRAND
	[total_strings] => 5
	[pixel_count] => 0
	[pixel_length] => 8.00
	[pixel_spacing] => 
	[unit_of_measure] => 
	[topography] => 
	)
	*/
	$pixel_count=$target_info['pixel_count'];
	//show_elapsed_time($script_start,"Making  gnuplot command file:");
	//	dat_file_array[0]=workspaces/2/AA+CIRCLE1_d_1.dat
	//
	$path_parts = pathinfo($dat_file_array[0]);
	$dat_file_array0=$dat_file_array[0];
	$dirname   = $path_parts['dirname']; // workspaces/2
	$basename  = $path_parts['basename']; // AA+CIRCLE1_d_1.dat
	$extension =$path_parts['extension']; // .dat
	$filename  = $path_parts['filename']; //  AA+CIRCLE1_d_1
	$tok=explode("/",$dirname);
	$member_id=$tok[1];
	$path=$dirname;
	$tokens2=explode("_d_",$filename);
	$base=$tokens2[0];	// AA+CIRCLE1
	$base_tmp=$base;
	$gp_file = $path . "/" .  $base . ".gp";
	$gif_file = $path . "/" .  $base . ".gif";
	$gif_file_th = $path . "/" .  $base . "_th.gif";
	$gp_file_amperage = $path . "/" .  $base . "_amp.gp";
	$fh_gp_file = fopen($gp_file, 'w') or die("can't open file");
	fwrite($fh_gp_file,"#	 $gp_file\n");
	$target_dat = "../targets/$member_id/" . $t_dat;
	$fh_gp_file_amperage = fopen($gp_file_amperage, 'w') or die("can't open file");
	fwrite($fh_gp_file_amperage,"#	 $gp_file_amperage\n");
	gp_header($fh_gp_file,$min_max,$target_info);
	$gif_delay=intval($frame_delay/10);
	//gp_header($fh_gp_file_amperage,$min_max);
	$min_x=$min_max[0];
	$max_x=$min_max[1];
	$min_y=$min_max[2];
	$max_y=$min_max[3];
	$min_z=$min_max[4];
	$max_z=$min_max[5];
	// calculate size of gif
	//
	$height=$max_z; 
	$width=$max_x-$min_x;
	if($width<1) $width=1;
	$aspect_ratio = $height/$width;
	$max=intval(max($height,$width)*5);	// max pixels in the biggest direction for output size
	/*echo "<pre>h=$height,w=$width, aspect=$aspect_ratio,max=$max\n";
	print_r($min_max);
	echo "</pre>\n";*/
	if($model_type=='SINGLE_STRAND' and $gif_model=='arch'){
		$w=640;
		$h=480;
	}
	elseif($aspect_ratio>1){
		$max=800;
		$w=intval($max/$aspect_ratio);
		$h=$max;
		$w=500;
		$h=800;
	}
	else{
		$max=400;
		$h=intval($max*$aspect_ratio);
		$w=$max;
		$w=500;
		$h=800;
	}
	$AMPERAGE=0;  // should we create an amperage graph also? 0=no, 1=yes
	$pointsize="0.6";  // was 0.6
	for($loop=1;$loop<=2;$loop++)  // loop1=1 300x66 file.gif, loop=2 100x200 file_th.gif
	{
		if($loop==1){
			if($batch<=2) // only build big gif if batch mode is 0,1, or 2
			{
				$pointsize="0.6";  // was 0.6
				fwrite($fh_gp_file,sprintf("\n\nset terminal gif  notransparent noenhanced optimize animate  delay %d size %d,%d\n",$gif_delay,$w,$h));
				fwrite($fh_gp_file,sprintf("set output '%s'\n",$gif_file));
				if($AMPERAGE==1){
					fwrite($fh_gp_file_amperage,sprintf("set terminal gif animate transparent opt delay %d size 300,600\n",$gif_delay));
					$gif_file_amperage = "amperage_" . $gif_file;
					fwrite($fh_gp_file_amperage,sprintf("set output '%s'\n",$gif_file_amperage));
				}
			}
		}
		else{
			$pointsize="0.3"; // was 0.3
			fwrite($fh_gp_file,sprintf("\nset output\n"));
			$w2=100;
			$h2=200;
			fwrite($fh_gp_file,sprintf("\n\nset terminal gif animate notransparent noenhanced  delay %d size %d,%d\n",$gif_delay,$w2,$h2));
			fwrite($fh_gp_file,sprintf("set output '%s'\n",$gif_file_th));
		}
		$maxFrame = count( $dat_file_array);
		$maxStrand=$yMax=0;
		if($AMPERAGE==1){
			fwrite($fh_gp_file_amperage,"set yrange[0:]\n");
			fwrite($fh_gp_file_amperage,"set xlabel 'Strand#'\n");
			fwrite($fh_gp_file_amperage,"set ylabel 'Strand Current (amps)'\n");
			foreach($amperage as $i => $n1)foreach($n1 as $s => $val){
				if($s>$maxStrand) $maxStrand=$s;
				if($val>$yMax) $yMax=$val;
			}
			$yMax=$yMax*1.4;
			fwrite($fh_gp_file_amperage,"set yrange[0:$yMax]\n");
		}
		$maxFrame = count( $dat_file_array);
		/*
		(
		[0] => workspaces/f/AA+SEAN2_d_1.dat
		[1] => workspaces/f/AA+SEAN2_d_2.dat
		[2] => workspaces/f/AA+SEAN2_d_3.dat
		[3] => workspaces/f/AA+SEAN2_d_4.dat
		[4] => workspaces/f/AA+SEAN2_d_5.dat
		[5] => workspaces/f/AA+SEAN2_d_6.dat
		[6] => workspaces/f/AA+SEAN2_d_7.dat
		[7] => workspaces/f/AA+SEAN2_d_8.dat
		[8] => workspaces/f/AA+SEAN2_d_9.dat
		[9] => workspaces/f/AA+SEAN2_d_10.dat
		[10] => workspaces/f/AA+SEAN2_d_11.dat
		[11] => workspaces/f/AA+SEAN2_d_12.dat
		[12] => workspaces/f/AA+SEAN2_d_13.dat
		[13] => workspaces/f/AA+SEAN2_d_14.dat
		[14] => workspaces/f/AA+SEAN2_d_15.dat
		[15] => workspaces/f/AA+SEAN2_d_16.dat
		[16] => workspaces/f/AA+SEAN2_d_17.dat
		[17] => workspaces/f/AA+SEAN2_d_18.dat
		[18] => workspaces/f/AA+SEAN2_d_19.dat
		[19] => workspaces/f/AA+SEAN2_d_20.dat
		)
		*/
		//	echo "<pre>";
		//print_r($dat_file_array);
		$target_dat = "../targets/$member_id/" . $t_dat;
		//  [0] => workspaces/2/AA+SEAN3_d_1.dat
		// ../targets/2/M16_50.dat
		fwrite($fh_gp_file,sprintf ("set notitle\n") ); // only on big gif
		for($frame=1;$frame<=$maxFrame;$frame++){
			$dat_file=$dat_file_array[$frame-1];
			$path_parts = pathinfo($dat_file);
			$dirname   = $path_parts['dirname'];
			$basename  = $path_parts['basename'];
			$extension = $path_parts['extension'];
			$filename  = $path_parts['filename'];
			$basen = basename($dat_file,".dat");
			$basen = $filename;
			$out_file = $dirname . "/" . $basen . ".png";
			$out_file_array[] = $out_file;
			//echo "<pre> $dat_file $dirname $basename $filename $out_file</pre>\n";
			$imagick=0;
			//if($loop==1) fwrite($fh_gp_file,sprintf ("set title 'Frame %4d'\n",$frame) ); // only on big gif
			//	echo "<pre> (set title 'Frame %4d',$frame)</pre>\n";
			if($show_frame== 'xY' or $show_frame=='xy'){ // force so never have frame
				fwrite($fh_gp_file,sprintf ("splot '%s' using 4:5:6 with points ls 7 notitle \\\n",$target_dat) );
				fwrite($fh_gp_file,sprintf ("   , '%s' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize $pointsize notitle\n",$dat_file));
			}
			else{
				if(($loop==1 and $batch<=2 ) or ($loop==2)){
					fwrite($fh_gp_file,sprintf ("   splot '%s' using 4:5:6:7 with points lc rgb variable pointtype 7 pointsize $pointsize notitle\n",$dat_file));
				}
			}
			if($AMPERAGE==1){
				$total_amperage=0;
				for($s=1;$s<=$maxStrand;$s++){
					$total_amperage+=$amperage[$frame][$s];
				}
				$out_file_amperage = $basen . "_amp_r_" . $frame . ".png";
				$out_file_array_amperage[] = $out_file_amperage;
				$dat_file_amperage = $basen . "_amp_r_" . $frame . ".dat";
				make_amperage_datefile($frame,$amperage,$dat_file_amperage); // make the file for frame $frame
				//$fh_dat_amperage = fopen($dat_file_amperage, 'r') or die("can't open file");
				fwrite($fh_gp_file_amperage,sprintf ("set output '%s'\n",$out_file_amperage) );
				$buff=sprintf("Total DC Current for frame %2d = %5.2f amps",$frame,$total_amperage);
				fwrite($fh_gp_file_amperage,sprintf ("plot '%s' using 1:2 with lines title '$buff'\n",$dat_file_amperage) );
			}
		}
	}
	fflush($fh_gp_file);
	fclose($fh_gp_file);
	fclose($fh_gp_file_amperage);
	$model="_d_";
	if($batch<=3) display_gif($batch,$path,$model,$gp_file,$out_file_array,$frame_delay);
	//display_gif($batch,$path,$model,$gp_file_amperage,$out_file_array_amperage,$frame_delay,$script_start);
	//
	$full_path=$dat_file_array0;
	$member_id=get_member_id($username);
	$base=$base_tmp;
	//
	//
	//
	if($batch==0){
		echo "<br/><br/><table border=2>";
		echo "<tr><th colspan=3 bgcolor=lightgreen><b>EXPORT OPTIONS: Now That You Have created a sequence, you need to export it</b></th></tr>";
		//	make_vixen
		echo "<tr><th>Sequencer</th><th>Left Click This column to create export file</th><th>Versions</th></tr>";
		printf ( "<tr><td>Vixen</td><td bgcolor=#98FF73><a href=\"make_vixen.php?base=$base&full_path=$full_path&frame_delay=$frame_delay&username=$username&member_id=$member_id&seq_duration=$seq_duration&sequencer=vixen&pixel_count=$pixel_count\">Left Click here to make *.vir and *.vix files</a></td><td>2.1,2.5 maybe 3.0</td></tr>\n");
		//print htmlentities($buff_vixen);
		//
		//	make_lor *.lms
		printf ( "<tr><td>LOR</td><td  bgcolor=#98FF73><a href=\"make_lor.php?base=$base&full_path=$full_path&frame_delay=$frame_delay&username=$username&member_id=$member_id&seq_duration=$seq_duration&sequencer=lors2&pixel_count=$pixel_count\">Left Click here to make *.lms file</a></td><td>S2 and S3</td></tr>\n");
		//print htmlentities($buff_lor);
		printf ("<tr><td>LOR</td><td  bgcolor=#98FF73><a href=\"make_lor.php?base=$base&full_path=$full_path&frame_delay=$frame_delay&username=$username&member_id=$member_id&seq_duration=$seq_duration&sequencer=lor_lcb&pixel_count=$pixel_count\">Left Click here to make *.lcb file</a></td><td>S2 and S3</td></tr>\n");
		//	make_lsp GUI I
		printf ("<tr><td>LSP </td><td  bgcolor=#98FF73><a href=\"make_lsp.php?base=$base&full_path=$full_path&frame_delay=$frame_delay&username=$username&member_id=$member_id&seq_duration=$seq_duration&sequencer=lsp&pixel_count=$pixel_count&type=1\">Left Click here to make UserPatterns.xml file</a></td><td>2.0 Type I gui. This is the normal file. &lt;TrackGuid&gt;60cc0c76-f458-4e67-abb4-5d56a9c1d97c&lt;/TrackGuid&gt;</td></tr>\n");
		//	make_lsp GUI II
		printf ("<tr><td>LSP </td><td  bgcolor=#98FF73><a href=\"make_lsp.php?base=$base&full_path=$full_path&frame_delay=$frame_delay&username=$username&member_id=$member_id&seq_duration=$seq_duration&sequencer=lsp&pixel_count=$pixel_count&type=2\">Left Click here to make UserPatterns.xml file</a></td><td>2.0 Type II gui. Try this file if you are not seeing pattern display when dragging. &lt;TrackGuid&gt;4e2556ac-d294-490c-8b40-a40dc6504946&lt;/TrackGuid&gt;</td></tr>\n");
		//	make_lsp GUI III
		printf ("<tr><td>LSP </td><td  bgcolor=#98FF73><a href=\"make_lsp.php?base=$base&full_path=$full_path&frame_delay=$frame_delay&username=$username&member_id=$member_id&seq_duration=$seq_duration&sequencer=lsp&pixel_count=$pixel_count&type=3\">Left Click here to make UserPatterns.xml file</a></td><td>2.0 Type III gui. Try this file if you are not seeing pattern display when dragging. &lt;TrackGuid&gt;ba459d0f-ce08-42d1-b660-5162ce521997&lt;/TrackGuid&gt;</td></tr>\n");
		//	make_lsp GUI IV
		printf ("<tr><td>LSP </td><td  bgcolor=#98FF73><a href=\"make_lsp.php?base=$base&full_path=$full_path&frame_delay=$frame_delay&username=$username&member_id=$member_id&seq_duration=$seq_duration&sequencer=lsp&pixel_count=$pixel_count&type=4\">Left Click here to make UserPatterns.xml file</a></td><td>2.0 Type IV gui. Try this file if you are not seeing pattern display when dragging. &lt;TrackGuid&gt;a69f7e39-e70d-4f70-8173-b3b2dbeea350&lt;/TrackGuid&gt;</td></tr>\n");
		//	make_hls
		printf ("<tr><td>HLS</td><td  bgcolor=#98FF73><a href=\"make_hls.php?base=$base&full_path=$full_path&frame_delay=$frame_delay&username=$username&member_id=$member_id&seq_duration=$seq_duration&sequencer=hls&pixel_count=$pixel_count\">Left Click here to make *.hlsq file</a></td><td> versions 3a and greater</td></tr>\n");
		echo "</table>\n";
	}
}

function purge_files(){
	//	remove old ong and dat files
	//echo "<pre>purge_files: To limit disk space, everytime you create an effect all previous files are removed</pre>\n";
	//echo "<pre>purge_files: Removing *.png, *.dat,*.vir,*.txt,*.hls,*.gp,*.srt,.*.lms</pre>\n";
	$directory=getcwd();
}

function get_enable_project($username){
	$link=open_db();
	$query="SELECT * FROM  members WHERE username = '$username'";
	$result=mysql_query($query,$link);
	if(!$result){
		echo "Error on $query\n";
		mysql_error();
	}
	while($row = mysql_fetch_assoc($result)){
		extract($row);
	}
	if($_SERVER['HTTP_HOST'] == 'localhost') $enable_project='Y';  // if the user is running Nutcracker on a local machine, always let him create projects
	return $enable_project;
}

function open_db(){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	return $link;
}

function get_effect_user_dtl($username,$effect_name){
	//Include database connection details
	require_once('../conf/config.php');
	// str_replace ( mixed $search , mixed $replace , mixed $subject [, int &$count ] )
	$effect_name=str_replace("%20"," ",$effect_name);
	$username=str_replace("%20"," ",$username);
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	$query ="select * from effects_user_dtl where effect_name='$effect_name' and username = '$username'";
	//echo "<pre>get_effect_user_dtl: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if(!$result){
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if(mysql_num_rows($result) == 0){
		$NO_DATA_FOUND=1;
	}
	$effects_user_dtl=array();
	if(!$NO_DATA_FOUND){
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while($row = mysql_fetch_assoc($result)){
			extract($row);
			$effects_user_dtl[]=$row;
		}
	}
	return $effects_user_dtl;
}

function get_effect_user_dtl2($username,$effect_name){
	//echo "<pre>function get_effect_user_dtl2($username,$effect_name)</pre>\n";
	//Include database connection details
	require_once('../conf/config.php');
	$effect_name=str_replace("%20"," ",$effect_name);
	$username=str_replace("%20"," ",$username);
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	$query ="select * from effects_user_dtl where effect_name='$effect_name' and username = '$username'";
	//echo "<pre>get_effect_user_dtl: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if(!$result){
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if(mysql_num_rows($result) == 0){
		$NO_DATA_FOUND=1;
	}
	$effects_user_dtl=array();
	if(!$NO_DATA_FOUND){
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while($row = mysql_fetch_assoc($result)){
			extract($row);
			/*echo "<pre>";
			print_r($row);
			echo "</pre>\n";*/
			$effects_user_dtl[$param_name]=$param_value;
		}
	}
	return $effects_user_dtl;
}

function get_effect_user_hdr($username,$effect_name){
	//Include database connection details
	require_once('../conf/config.php');
	// str_replace ( mixed $search , mixed $replace , mixed $subject [, int &$count ] )
	$effect_name=str_replace("%20"," ",$effect_name);
	$username=str_replace("%20"," ",$username);
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	$query ="select * from effects_user_hdr where effect_name='$effect_name' and username = '$username'";
	//echo "<pre>get_effect_user_hdr: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if(!$result){
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if(mysql_num_rows($result) == 0){
		$NO_DATA_FOUND=1;
	}
	$effects_user_hdr=array();
	if(!$NO_DATA_FOUND){
		// LSP1_8	LSP2_0	LOR_S2	LOR_S3	VIXEN211	VIXEN25	VIXEN3	OTHER	
		while($row = mysql_fetch_assoc($result)){
			extract($row);
			$effects_user_hdr[]=$row;
		}
	}
	return $effects_user_hdr;
}

function make_buff($username,$member_id,$base,$frame_delay,$seq_duration,$fade_in_secs,$fade_out_secs){
	//echo "<pre>function make_buff($username,$member_id,$base,$frame_delay,$seq_duration)</pr>\n";
	list($usec, $sec) = explode(' ', microtime());
	$script_start = (float) $sec + (float) $usec;
	$tokens=explode("~",$base);
	$target_name=$tokens[0];
	$effect_name=$tokens[1];
	//
	/*
	$target_array=
	(
	[target_name] => AA
	[model_type] => MTREE
	[total_strings] => 16
	[pixel_count] => 100
	[pixel_length] => 3.00
	[pixel_spacing] => 
	[unit_of_measure] => in
	[topography] => 
	)
	*/
	$target_array= get_info_target($username,$target_name);
	$total_strings=$target_array['total_strings'];
	//
	$effect_array=get_effect_user_dtl($username,$effect_name);
	$window_degrees=0;
	foreach($effect_array as $index =>$row_array){
		if($row_array['param_name']=="window_degrees") $window_degrees=$row_array['param_value'];
	}
	//
	/*[11] => Array
	(
	[username] => f
	[effect_name] => FLY_0_0
	[param_name] => window_degrees
	[param_value] => 180
	[created] => 
	[last_upd] => 2012-06-28 00:51:27
	)*/
	//
	$model_array=get_target_model($username,$target_name);
	$maxStrand =$model_array[0];
	$maxPixels =$model_array[1];
	$model_type=$model_array[2];
	/*	$model_array=
	(
	[username] => f
	[object_name] => AA
	[object_desc] => aa
	[model_type] => MTREE
	[string_type] => 
	[pixel_count] => 100
	[folds] => 2
	[start_bottom] => y
	[pixel_first] => 1
	[pixel_last] => 100
	[pixel_length] => 3.00
	[pixel_spacing] => 
	[unit_of_measure] => in
	[total_strings] => 16
	[total_pixels] => 
	[direction] => 
	[orientation] => 
	[topography] => 
	[h1] => 
	[h2] => 
	[d1] => 
	[d2] => 
	[d3] => 
	[d4] => 
	[date_created] => 2012-06-12 11:52:59
	)*/
	//
	$maxString=$target_array['total_strings'];
	$maxPixel=$target_array['pixel_count'];
	$effect_user_dtl_array=get_effect_user_dtl($username,$effect_name);
	$frame_delay=$sequence_duration=0;
	foreach($effect_user_dtl_array as $i=>$effect_array){
		/*echo"<pre>i=$i";
		print_r($effect_array);
		echo "</pre>\n";*/
		if($effect_array['param_name']=='frame_delay') $frame_delay=$effect_array['param_value'];
		if($effect_array['param_name']=='seq_duration') $seq_duration=$effect_array['param_value'];
	}
	//print_r($target_array);
	/*echo"<pre>";
	echo "get_info_target: 	maxString,maxPixel  $maxString,$maxPixel\n";
	//	print_r($effect_user_dtl_array);
	//
	echo "frame_delay,sequence_duration  $frame_delay,$seq_duration \n";
	echo "</pre>\n";*/
	$maxLoop=1;
	//
	$full_path= "../effects/workspaces/$member_id/$base";
	$path_parts = pathinfo($full_path);
	$dirname   = $path_parts['dirname'];
	$basename  = $path_parts['basename'];
	//$extension =$path_parts['extension'];
	//echo "<pre>";
	//---------------------------------------------------------------------------------------
	//	Lets find all the file names that are under the base. TARGET+EFFECT_d_nn.date
	//
	//	When we exit this loop we have filled the dat_file_array[].
	//---------------------------------------------------------------------------------------
	$dir = opendir ($dirname); 
	$dat_file_array=array();
	while(false !== ($file = readdir($dir))){
		$dat=strpos($file, '.dat',1);
		$m=strpos($file,$base,0);
		//	echo "<pre>file =$file. dat,m=$dat,$m </pre>\n"; 
		if(strpos($file, '.dat',1) and strpos($file,$base,0)===0 ){
			$basen=basename($file,".dat");
			$tokens=explode("_d_",$basen);
			$c=count($tokens);
			if($c>1){
				$i=$tokens[1];
				$dat_file_array[$i]=$file;
				//	echo "<pre>dat_file_array[$i]=$file;</pre>\n";
			}
		}
	}
	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
	$maxFrame=count($dat_file_array); // max frames in one animation sequence
	/*
	#    workspaces/f/AA+SEAN3_18.dat
	t1   20    1    -0.225     0.692   113.090 16716932 18 18
	t1    5    1     0.692     0.225   113.090 16716932 18 38
	t1   15    1    -0.692    -0.225   113.090 16716932 18 78
	t1   20    1    -0.225     0.692   113.090 16716932 18 98
	t1    5    1     0.692     0.225   113.090 16716932 18 118
	t1   15    1    -0.692    -0.225   113.090 16716932 18 158
	t1   19    2    -0.855     1.177   110.179 16716942 18 178
	t1    4    2     1.177     0.855   110.179 16716942 18 198
	t1   18    3    -1.766     1.283   107.269 16716696 18 258
	t1    3    3     1.283     1.766   107.269 16716696 18 278
	*/
	//$TotalFrames = 
	/*	echo "<pre>";
	print_r($dat_file_array);
	echo "</pre>\n";*/
	$seq_file = $dirname . "/" . $base . ".dat";
	$seq_srt = $dirname . "/" . $base . ".srt";
	$gp_file = $dirname . "/" . $base . ".gp";
	$amp_gp_file = $dirname . "/" . $base . "_amp.gp";
	$fh_seq=fopen($seq_file,"w") or die ("unable to open $seq_file");
	//---------------------------------------------------------------------------------------
	//	Loop thru all dat files, and process them 1 by 1 and append them into seq_file. 
	//	In other words read every dat file and make one huge file called TARGET+EFFECT.dat
	//---------------------------------------------------------------------------------------
	//
	$minStrand=1; // we always start at 1
	$window_array = getWindowArray(1,$total_strings,$window_degrees);
	/*echo "<pre>";
	print_r($window_array);
	echo "</pre>\n";*/
	for($frame=1;$frame<=$maxFrame;$frame++){
		if(isset($dat_file_array[$frame])) $filename=$dat_file_array[$frame];
		else{
			echo "<pre>";
		}
		$full_path = $dirname . "/" . $filename;
		$fh = fopen($full_path, 'r') or die("can't open file $full_path");
		//if($batch==0) echo "<pre>processing $filename</pre>\n";
		/*
		#    workspaces/f/AA+SEAN3_d_8.dat
		t1    6    1    -0.589     0.428   113.090 16712526 0 0 9 41
		t1    5    1    -0.692     0.225   113.090 16712526 0 0 9 40
		t1    4    1    -0.728     0.000   113.090 16712526 0 0 8 41
		t1   10    1     0.225     0.692   113.090 16712526 0 0 1 41
		t1    9    1     0.000     0.728   113.090 16712526 0 0 1 40
		t1    8    1    -0.225     0.692   113.090 16712526 0 0 10 41
		t1   13    1     0.692     0.225   113.090 16712526 0 0 3 40
		*/
		$loop=$maxPixel=0;
		$line_counter=0;
		while(!feof($fh)){
			$line = fgets($fh);
			//echo "<pre>frame=$frame  $line\n</pre>\n";
			$tok=preg_split("/ +/", $line);
			$l=strlen($line);
			$c= substr($line,0,1);
			$line_counter++;
			//echo "<pre>frame=$frame l=$l, c=$c line=$line</pre>\n";
			if($l>20 and $c !="#"){
				$i++;
				$loop++;
				$s=$tok[1];	// 1 strand#
				$p=$tok[2];// 2 pixel#	
				$rgb=intval($tok[6]);	// 5 Z value
				$string=intval($tok[9]);
				$user_pixel=intval($tok[10]);
				if($user_pixel>$maxPixel) $maxPixel=$user_pixel;
				if(empty($rgb))					$rgb=0;
				else				$rgb=$tok[6];
				//
				//	$string_array[$string][$user_pixel][$frame]=$rgb;
				//	echo "<pre>string,s,p=$string,$s,$p</pre>\n";
				//if(in_array($string,$window_array)) // Is this strand in our window?, 
				{
					fwrite($fh_seq,sprintf("%6d %6d %6d %9d # $loop  %s",$string,$user_pixel,$frame,$rgb,$line));
					//			printf("%6d %6d %6d %9d # $loop  %s",$string,$user_pixel,$frame,$rgb,$line);
				}
			}
		}
		fclose($fh);
		flush();
		$unlink_true=0; // if =1 then we unlink temp files, *.dat, *.srt
		//echo "<pre>unlink($full_path) has $line_counter lines</pre>\n";
		$full_path=realpath($full_path);
		if(file_exists($full_path)){
			//echo "<pre>Purging $full_path</pre>\n";
			if($unlink_true==1)	unlink($full_path);
		}
	}
	//if (file_exists($gp_file)) unlink($gp_file);
	if(file_exists($amp_gp_file)) unlink($amp_gp_file);
	fclose($fh_seq);
	//
	//---------------------------------------------------------------------------------------
	//	Ok, now that $basename.dat exists (the concatenated big file of all the dat's
	//	sort it so we get the strings togeter. We will output this to TARGET+EFFECT.srt
	//---------------------------------------------------------------------------------------
	//
	$seq_srt = $dirname . "/" . $base . ".srt";
	if($_SERVER['HTTP_HOST']!='meighan.net') // If this is a windows server, 
	{
		$data = file($seq_file); // we will do a memory sort
		natsort($data);
		file_put_contents($seq_srt, implode("\n", $data));
	}
	else // we are on a linux bos, so we can use the linux sort
	{
		$shellCommand = "cat $seq_file | awk '{if(NF>5) print $0;
		}
		' | sort -bn -o $seq_srt"; 
		$output = system($shellCommand . " 2>&1"); 
		//echo "<pre>cmd= $shellCommand </pre>\n";
		//echo "<pre>output= $output </pre>\n";
	}
	//
	//
	/*//	remove_blanks($seq_file);
	//
	$seq_srt = $dirname . "/" . $base . ".srt";
	$seq_srt1 = $dirname . "/" . $base . ".srt1";
	if (!copy($seq_srt, $seq_srt1))
	{
	echo "failed to copy $seq_srt...\n";
	}
	$fh1 = fopen($seq_srt1, 'r') or die("can't open file $seq_srt");
	$fh_seq=fopen($seq_srt,"w") or die ("unable to open $seq_file");
	while (!feof($fh1)) // read *.srt file
	{
	$line = fgets($fh1);
	$tok=preg_split("/ +/", $line);
	$l=strlen($line);
	//echo "<pre>zz: $line</pre>\n";
	$c=count($tok);
	if($c>2)
	{
	//	printf("<pre>%s</pre>\n",$line);
	fwrite($fh_seq,sprintf("%s",$line));
	}
	}
	unlink ($seq_srt1);
	fclose($fh_seq);*/
	//
	//
	$channel=0;
	/*		AA+SEAN.srt file format.
	*		col 1: string
	*		col 2: pixel
	*		col 3: frame
	*		col 4: rgb
	1      2      9    917261 # 1186  t1   10   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     10    917261 # 1186  t1   11   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     11    917261 # 1184  t1   12   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     24  16777215 # 1158  t1   25   79     0.000    57.481     2.076 16777215 0 0 1 2
	1      2     25    917261 # 1157  t1   26   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     26    917261 # 1156  t1   27   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     39    917261 # 1158  t1   40   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     40    917261 # 1157  t1   41   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     41    917261 # 1156  t1   42   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     54    917261 # 1172  t1   55   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     55    917261 # 1181  t1   56   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     56    917261 # 1188  t1   57   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     69    917261 # 1181  t1   70   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     70    917261 # 1182  t1   71   79     0.000    57.481     2.076 917261 0 0 1 2
	1      2     71    917261 # 1182  t1   72   79     0.000    57.481     2.076 917261 0 0 1 2
	1      3     10    917273 # 1178  t1   11   78     0.000    56.753     4.987 917273 0 0 1 3
	1      3     10  16777215 # 1169  t1   11   78     0.000    56.753     4.987 16777215 0 0 1 3
	1      3     11    917273 # 1167  t1   12   78     0.000    56.753     4.987 917273 0 0 1 3
	1      3     11    917273 # 1176  t1   12   78     0.000    56.753     4.987 917273 0 0 1 3
	1      3     12    917273 # 1167  t1   13   78     0.000    56.753     4.987 917273 0 0 1 3
	*/
	$fh = fopen($seq_srt, 'r') or die("can't open file $seq_srt");
	$loop=$hlsnc_loop=0;
	$outBuffer=array();
	$old_string=-1;
	if($frame_delay<1) $frame_delay=100;
	if($frame_delay>0)		$TotalFrames= ($seq_duration*1000)/$frame_delay;
	else	die ("frame_delay = 0, unable to create any output");
	//	should we show 5 string mini channel preview?
	$preview=0; // for now, no
	if($preview==1){
		echo "<h3>$seq_duration seconds of animation with a $frame_delay ms frame timing = $TotalFrames ($maxFrame) frames of animation</h3>\n";
		show_srt_file($seq_srt,$maxFrame,$frame_delay,$maxPixel,$pixel_count);
	}
	//---------------------------------------------------------------------------------------
	//
	//	Now we read the sorted file TARGET+EFFECT.srt and produce and output file
	//	for LOR lms file
	/*  1      5      1     26367 # 46  t1    1   46     0.000    33.470     0.284 26367 0 0 1 5
	1      5      2      2559 # 46  t1    1   46     0.000    33.470     0.284 2559 0 0 1 5
	1      5      3   5112063 # 46  t1    1   46     0.000    33.470     0.284 5112063 0 0 1 5
	1      5      4  10354943 # 46  t1    1   46     0.000    33.470     0.284 10354943 0 0 1 5
	1      5      5  15139071 # 46  t1    1   46     0.000    33.470     0.284 15139071 0 0 1 5
	1      5      6  16711894 # 46  t1    1   46     0.000    33.470     0.284 16711894 0 0 1 5
	1      5      7  16711837 # 46  t1    1   46     0.000    33.470     0.284 16711837 0 0 1 5
	1      5      8  16711789 # 46  t1    1   46     0.000    33.470     0.284 16711789 0 0 1 5
	1      5      9  16711749 # 46  t1    1   46     0.000    33.470     0.284 16711749 0 0 1 5
	1      5     10  16711718 # 46  t1    1   46     0.000    33.470     0.284 16711718 0 0 1 5
	1      5     11  16711697 # 46  t1    1   46     0.000    33.470     0.284 16711697 0 0 1 5
	*/
	//---------------------------------------------------------------------------------------
	for($f=1;$f<=$maxFrame;$f++){
		$outBuffer[$f]=0;
	}
	if($TotalFrames<$maxFrame){
		$MaxFrameLoops=$TotalFrames;
	}
	elseif($maxFrame>0){
		$MaxFrameLoops = intval(($TotalFrames/$maxFrame)+1);
	}
	else{
		$MaxFrameLoops=1;
	}
	//echo "<pre>$MaxFrameLoops = intval(($TotalFrames/$maxFrame)+0.5);</pre>\n";
	//echo "<pre>MaxFrameLoops = $MaxFrameLoops</pre>\n";
	$Sec2=-1;
	$i=0;
	$frame=0;
	$filename_buff= $dirname . "/" . $base . ".nc";
	$fh_buff=fopen($filename_buff,"w") or die("Unable to open $filename_buff");
	fwrite ($fh_buff,sprintf("# window_degrees %s\n",$window_degrees));
	fwrite ($fh_buff,sprintf("# target_name %s\n",$target_name));
	fwrite ($fh_buff,sprintf("# effect_name %s\n",$effect_name));
	//
	//
	$fade_in=$fade_out=0;
	if($fade_in_secs>0) $fade_in  =intval((1000*$fade_in_secs)/$frame_delay);
	if($fade_out_secs>0) $fade_out=intval((1000*$fade_out_secs)/$frame_delay);
	//	echo "<pre>fade_in_secs=$fade_in_secs,fade_in=$fade_in</pre>\n";
	$echo=0; // echo=1, see the buff file on screen
	$old_string=$old_pixel=0;
	while(!feof($fh)) // read *.srt file
	{
		$line = fgets($fh);
		$tok=preg_split("/ +/", $line);
		$l=strlen($line);
		//echo "<pre>zz: $line</pre>\n";
		$c= substr($line,0,1);
		if($l>20 and $c !="#"){
			$i++;
			/*echo "<pre>tok:";
			print_r($tok);
			echo "</pre>\n";*/
			//print_r($outBuffer);
			$string=$tok[1];	// string#
			$pixel=$tok[2];	// pixel#
			$frame=$tok[3];	// frame#
			$rgb=$tok[4];	// rgb#
			$rgbhex=dechex($rgb);
			if($string>0 and $old_pixel>0 and ($string!=$old_string or $pixel!=$old_pixel )){
				//	echo "<pre>if(string>0 and old_pixel>0 and (string!=old_string or pixel!=old_pixel )) = if($string>0 and $old_pixel>0 and ($string!=$old_string or $pixel!=$old_pixel ))</pre>\n";
				fwrite ($fh_buff,sprintf("S %d P %d ",$old_string,$old_pixel),11);
				$frameCounter=0;
				for($loop=1;$loop<=$MaxFrameLoops;$loop++){
					for($f=1;$f<=$maxFrame;$f++){
						$frameCounter++;
						if($frameCounter<=$TotalFrames){
							$rgb2=$outBuffer[$f];
							$rgb_val=$rgb2;
							$fade_out_start = $TotalFrames-$fade_out+1;
							if(($fade_in>0 and $frameCounter<=$fade_in) or ($fade_out>0 and $frameCounter>=$fade_out_start))								$rgb2=fade($fade_in,$fade_out,$frameCounter,$TotalFrames,$rgb_val);
							//echo "<pre>loop,f = $loop,$f  rgb_val,rgb $rgb_val,$rgb</pre>\n";
							fwrite ($fh_buff,sprintf(" %d",$rgb2));
							if($echo==1)printf(" %d",$rgb2);
						}
					}
				}
				fwrite ($fh_buff,sprintf("\n"));
				if($echo==1)printf("\n");
				for($f=1;$f<=$maxFrame;$f++){
					$outBuffer[$f]=0;
				}
			}
			$old_string=$string;
			$old_pixel=$pixel;
			$outBuffer[$frame]=$rgb;
			/*	printf ("<pre>string pixel = $string,$pixel: ");
			for ($i=1;$i<=$frame;$i++)
			printf("%d ",$outBuffer[$i]);
			printf ("$line </pre>\n");*/
		}
	}
	fclose($fh);
	fwrite ($fh_buff,sprintf("S %d P %d ",$old_string,$old_pixel),11);
	//printf("<pre>S %d P %d ",$old_string,$old_pixel);
	$frameCounter=0;
	for($loop=1;$loop<=$MaxFrameLoops;$loop++)for($f=1;$f<=$maxFrame;$f++){
		$frameCounter++;
		if($frameCounter<=$TotalFrames)			fwrite ($fh_buff,sprintf(" %d",$outBuffer[$f]));
		//printf(" %d",$outBuffer[$f]);
	}
	fwrite ($fh_buff,sprintf("\n"));
	//printf("</pre>\n");
	//  $seq_file = $dirname . "/" . $base . ".dat";
	//	$seq_srt = $dirname . "/" . $base . ".srt";
	if($unlink_true==1) unlink($seq_file);
	if($unlink_true==1)unlink ($seq_srt);
	fclose($fh_buff);
	//
	//
	/*# window_degrees 180
	# target_name 0
	# effect_name BARBERPOLE
	S 1 P 1  16777215 16777215 16777215 16777215 
	S 1 P 1  16777215 16777215 16777215 16777215 
	S 1 P 1  16777215 16777215 16777215 16777215 */
	if($model_type=="HORIZ_MATRIX"){
		$fh=fopen($filename_buff,"r") ;
		while(!feof($fh)) // read *.srt file
		{
			$line = fgets($fh);
			$tok=preg_split("/ +/", $line);
			$l=strlen($line);
			//echo "<pre>zz: $line</pre>\n";
			$c= substr($line,0,1);
			if($l>20 and $c !="#"){
				$i++;
				/*tok:Array
				[0] => S
				[1] => 1
				[2] => P
				[3] => 6
				[4] => 16777215
				[5] => 16711680
				[6] => 16711680
				[7] => 16711680*/
				if($tok[0]=="S" and $tok[2]=="P"){
					$string=$tok[1];	// string#
					$pixel=$tok[3];	// pixel#
					$cnt=count($tok);
					echo "<pre>";
					print_r($tok);
					printf("S %d P %d",$pixel,$string);
					for($i=4;$i<$cnt;$i++){
						printf(" %s",$tok[$i]);
					}
					//printf("\n");
					echo "</pre>";
					if($string>0 and $old_pixel>0 and ($string!=$old_string or $pixel!=$old_pixel )){
						//	fwrite ($fh_new,sprintf("S %d P %d ",$old_string,$old_pixel),11);
					}
				}
			}
		}
		fclose($fh);
	}
	return ($filename_buff);
}

function fade($fade_in,$fade_out,$f,$maxFrame,$rgb){
	$fade_out_start = $maxFrame-$fade_out+1;
	$per=1.0;
	$rgb_val=$rgb;
	if($fade_in>0 and $f<=$fade_in){
		$per=$f/$fade_in;
		if($f==1) $per=0.0;
	}
	if($fade_out>0 and $f>=$fade_out_start){
		$per=($maxFrame-$f+1)/$fade_out;
		if($f==$maxFrame) $per=0.0;
	}
	$HSV=RGBVAL_TO_HSV($rgb);
	$H=$HSV['H'];
	$S=$HSV['S'];
	$V0=$HSV['V'];
	$V=$V0*$per; // we adjust brightness
	$rgb_val=HSV_TO_RGB ($H, $S, $V);
	//echo "<pre>HSV V0 $H,$S,$V V0=$V0  per=$per  rgb=$rgb : rgb_val=$rgb_val</pre>\n";
	return $rgb_val;
}

function get_target_model($username,$model_name){
	//Include database connection details
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	/*
	
	function get_models(f,ZZ)
	query=select * from models where username='f' and object_name='ZZ'
	Array
	(
	[username] => f
	[object_name] => ZZ
	[object_desc] => newest test mar 1
	[model_type] => MTREE
	[string_type] => 
	[pixel_count] => 50
	[folds] => 50
	[pixel_first] => 1
	[pixel_last] => 50
	[pixel_length] => 200.00
	[unit_of_measure] => in
	[total_strings] => 24
	[direction] => 
	[orientation] => 0
	[topography] => UP_DOWN_NEXT
	[topography] => BOT_TOP
	[h1] => 120.00
	[h2] => 0.00
	[d1] => 40.00
	[d2] => 0.00
	[d3] => 0.00
	[d4] => 0.00
	)
	*/
	//	
	$query ="select * from models where username='$username' and object_name='$model_name'";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if(!$result){
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if(mysql_num_rows($result) == 0){
		$NO_DATA_FOUND=1;
	}
	$query_rows=array();
	// While a row of data exists, put that row in $row as an associative array
	// Note: If you're expecting just one row, no need to use a loop
	// Note: If you put extract($row); inside the following loop, you'll
	//       then create $userid, $fullname, and $userstatus
	//
	if(!isset($folds)) $folds=1;
	if($folds<1) $folds=1;
	while($row = mysql_fetch_assoc($result)){
		extract($row);
	}
	$pixel_count_even=$folds * intval($pixel_count/$folds); // this is the total pixels that are evenly divisible.
	if($folds==1){
		$maxPixels=$pixel_count;
		$maxStrands=$total_strings;
	}
	else{
		$maxPixels = intval($pixel_count/$folds); // 
		$maxStrands=intval(0.5+($total_strings*$pixel_count)/$maxPixels);
		if(strtoupper($start_bottom)=='Y'){
			$maxStrands=intval(0.5 + ($total_strings*$pixel_count_even)/$maxPixels);
		}
	}
	//	echo "pixel_count=$pixel_count, pixel_count_even=$pixel_count_even, maxStrands=$maxStrands, maxPixels=$maxPixels</pre>";
	$return_array[0]=$maxStrands;
	$return_array[1]=$maxPixels;
	$return_array[2]=$model_type;
	return $return_array;
}

function get_window_degrees($username,$model_name,$in_window_degrees){
	//Include database connection details
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	//	
	$query ="select * from models where username='$username' and object_name='$model_name'";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error()); 
	if(!$result){
		$message  = 'Invalid query: ' . mysql_error() . "\n";
		$message .= 'Whole query: ' . $query;
		die($message);
	}
	$NO_DATA_FOUND=0;
	if(mysql_num_rows($result) == 0){
		$NO_DATA_FOUND=1;
	}
	$query_rows=array();
	// While a row of data exists, put that row in $row as an associative array
	// Note: If you're expecting just one row, no need to use a loop
	// Note: If you put extract($row); inside the following loop, you'll
	//       then create $userid, $fullname, and $userstatus
	//
	$window_degrees=0;
	while($row = mysql_fetch_assoc($result)){
		extract($row);
	}
	if($in_window_degrees!=$window_degrees){
		//	this message confuses Jim Nealand, not really needed, more for information to us
		//echo "<pre>Overriding your effect window_degrees of $in_window_degrees degrees\n";
		//echo "Setting it to $window_degrees degrees to match your target model $model_name</pre>\n";
		if(!isset($window_degrees)) $window_degrees=360;
		$query ="update models set window_degrees=$window_degrees
		where username='$username' and object_name='$model_name'";
		$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	}
	return $window_degrees;
}

function  create_sparkles($sparkles,$maxStrand,$maxPixel){
	if($sparkles==0) return array();
	$totalPixels=$maxPixel*$maxStrand;
	$pixels_to_allocate = $totalPixels * ($sparkles/100);
	for($i=1;$i<=$pixels_to_allocate;$i++){
		srand();
		$s=rand(1,$maxStrand);
		$p=rand(1,$maxPixel);
		$sparkles_array[$s][$p]=rand(1,100);
	}
	return $sparkles_array;
}

function calculate_sparkle($s,$p,$cnt,$rgb_val,$sparkle_count){
	$orig=$rgb_val;
	//echo "<pre>function calculate_sparkle($s,$p,$cnt,$rgb_val,$sparkle_count)</pre>\n";
	if($sparkle_count<1) return $rgb_val;
	$v=intval($cnt%$sparkle_count);
	/*	Sparkle is a twinkle down over 7 frames.
	frame 1, dark gray (#444444)
	frame 2, Lighter gray #888888
	frame 3, almost white #BBBBBB
	frame 4, pure white #FFFFFF
	frame 5, almost white #BBBBBB
	frame 6, Lighter gray #888888
	frame 7, dark gray (#444444)*/
	if($v==1){
		$rgb_val=4473924; // #444444
	}
	if($v==2){
		$rgb_val=8947848; // #888888
	}
	if($v==3){
		$rgb_val=12303291; // #BBBBBB
	}
	if($v==4){
		$rgb_val=16777215; // #FFFFFF
	}
	if($v==5){
		$rgb_val=12303291; // #BBBBBB
	}
	if($v==6){
		$rgb_val=8947848; // #888888
	}
	if($v==7){
		$rgb_val=4473924; // #444444
	}
	$hex=dechex($rgb_val);
	//	echo "<pre>s,p=$s,$p cnt=$cnt v=$v, orig=$orig, rgb_val=$rgb_val, $hex</pre>\n";
	return $rgb_val;
}
//Function to sanitize values received from the form. Prevents SQL injection

function clean($str){
	$str = @trim($str);
	if(get_magic_quotes_gpc()){
		$str = stripslashes($str);
	}
	return mysql_real_escape_string($str);
}

function get_user_effects($target,$effect_name,$username){
	//Include database connection details
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	//
	$clean_effect_name=mysql_real_escape_string ($effect_name);
	$query = "SELECT hdr.effect_class,hdr.username,hdr.effect_name,
	hdr.effect_desc,hdr.music_object_id,
	hdr.start_secs,hdr.end_secs,hdr.phrase_name,
	dtl.segment,dtl.param_name,dtl.param_value
	FROM `effects_user_hdr` hdr, effects_user_dtl dtl
	where hdr.username = dtl.username
	and hdr.effect_name = dtl.effect_name
	and hdr.username='$username'
	and upper(hdr.effect_name)=upper('$clean_effect_name')";
	//echo "<pre>count_gallery: $query<br />$effect_name=$clean_effect_name</pre>\n";
	//
	//
	$result = mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$cnt=0;
	$string="";
	while($row = mysql_fetch_assoc($result)){
		extract($row);
		//	if(strncmp($param_name,"color",5)==0 and strncmp($param_value,"#",1)==0) $param_value=hexdec($param_value);
		//	if(strncmp($param_name,"background_color",strlen("background_color"))==0 and strncmp($param_value,"#",1)==0) $param_value=hexdec($param_value);
		$string = $string . "&" . $param_name . "=" . $param_value;
		$get[$param_name]=$param_value;
	}
	// we also need teh effect class from teh header
	$get['effect_class']=$effect_class;
	return $get;
}

function get_segments($username,$object_name){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	//
	//
	$query = "select * from models_strand_segments where username='$username' and  object_name='$object_name'
	order by segment";
	//echo "<pre>update_segments: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$segment_array=array();
	while($row = mysql_fetch_assoc($result)){
		extract($row);
		$segment_array[$segment]=$starting_pixel;
	}
	return $segment_array;
}

function get_gif_model($username,$user_target){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	//
	$query = "select * from  models where username='$username' and object_name='$user_target' ";
	//echo "<pre>get_number_segments: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . 
		$query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	while($row = mysql_fetch_assoc($result)){
		extract($row);
	}
	return $gif_model;
}

function get_effect_id($username,$effect_name){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	//
	$query = "select * from  effects_user_hdr where username='$username'
	and effect_name='$effect_name' ";
	//echo "<pre>get_number_segments: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . 
		$query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	//
	while($row = mysql_fetch_assoc($result)){
		extract($row);
	}
	if(isset($effect_id)){
		return $effect_id;
	}
	else{
		return NULL;
	}
}

function is_ani($filename){
	if(!($fh = @fopen($filename, 'rb')))		return false;
	$count = 0;
	//an animated gif contains multiple "frames", with each frame having a 
	//header made up of:
	// * a static 4-byte sequence (\x00\x21\xF9\x04)
	// * 4 variable bytes
	// * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)
	// We read through the file til we reach the end of the file, or we've found 
	// at least 2 frame headers
	while(!feof($fh) && $count < 2){
		$chunk = fread($fh, 1024 * 100); //read 100kb at a time
		$count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
	}
	fclose($fh);
	return $count > 1;
}

function get_start_channel($username,$model){
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	//
	//
	$query = "select * from models where username='$username' and  object_name='$model'";
	//echo "<pre>update_segments: query=$query</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
	$segment_array=array();
	while($row = mysql_fetch_assoc($result)){
		extract($row);
	}
	return $start_channel;
}

function create_twinkle($get,$arr,$number_frames_per_blink){
	extract ($get);
	$minStrand =$arr[0];  // lowest strand seen on target
	$minPixel  =$arr[1];  // lowest pixel seen on skeleton
	$maxStrand =$arr[2];  // highest strand seen on target
	$maxPixel  =$arr[3];  // maximum pixel number found when reading the skeleton target
	$maxI      =$arr[4];  // maximum number of pixels in target
	$tree_rgb  =$arr[5];
	$tree_xyz  =$arr[6];
	$file      =$arr[7];
	$min_max   =$arr[8];
	$strand_pixel=$arr[9];
	if($color3 == null or !isset($color3)) $color3="#FFFFFF";
	if($color4 == null or !isset($color4)) $color4="#FFFFFF";
	if($color5 == null or !isset($color5)) $color5="#FFFFFF";
	if($speed == null or !isset($speed)) $speed=0.5;
	//
	$get['maxStrand']=$maxStrand;
	$get['maxPixel']=$maxPixel;
	//
	//
	srand();
	$two_blinks = 2*$number_frames_per_blink;
	for($s=1;$s<=$maxStrand;$s++){
		for($p=1;$p<=$maxPixel;$p++){
			$twinkle[$s][$p]=0;
			$twinkle_counter[$s][$p]=mt_rand(1,$two_blinks);
		}
	}
	$f=1;
	$line=$seq_number=0;
	$color_array = array(hexdec("#FF0000"), // RED
		hexdec("#00FF00"), // GREEN
		hexdec("#FFFF00"), // YELLOW
		hexdec("#FF00FF"), // PURPLE
		hexdec("#0000FF")); // GREEN
	$color_array = array(hexdec($color1),
		hexdec($color2),
		hexdec($color3),
		hexdec($color4),
		hexdec($color5));
	for($s=1;$s<=$maxStrand;$s++){
		for($p=1;$p<=$maxPixel;$p++){
			$icolor=mt_rand(0,4);
			$r = mt_rand(1,100);
			if($r<=$sparkles)				$twinkle[$s][$p]=$color_array[$icolor];
		}
		//	if($batch==0) echo "<pre>twinkle[s][p]=rgb_val; = twinkle[$s][$p]=$rgb_val;</pre>\n";
	}
	$twinkle_array=array($twinkle,$twinkle_counter);
	return $twinkle_array;
}

function audit($username,$action,$object_name){
	//Include database connection details
	require_once('../conf/config.php');
	//Connect to mysql server
	$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	if(!$link){
		die('Failed to connect to server: ' . mysql_error());
	}
	//Select database
	$db = mysql_select_db(DB_DATABASE);
	if(!$db){
		die("Unable to select database");
	}
	$query = "INSERT into audit_log( username,date_field,time_field,action,object_name)
	values ('$username',curdate(),curtime(),'$action','$object_name')";
	//echo "<pre>$insert</pre>\n";
	$result=mysql_query($query) or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . 
		"<br />\nError: (" . mysql_errno() . ") " . mysql_error());
}

function elapsed_time($script_start){
	$description ="Total Elapsed time for this effect:";
	list($usec, $sec) = explode(' ', microtime());
	$script_end = (float) $sec + (float) $usec;
	$elapsed_time = round($script_end - $script_start, 5); // to 5 decimal places
	printf ("<pre>%-40s Elapsed time = %10.5f seconds</pre>\n",$description,$elapsed_time);
}
function CustomError($errno, $errmsg, $filename, $linenum, $vars){
	// timestamp for the error entry
	$dt = date("Y-m-d H:i:s (T)");

	// define an assoc array of error string
	// in reality the only entries we should
	// consider are E_WARNING, E_NOTICE, E_USER_ERROR,
	// E_USER_WARNING and E_USER_NOTICE
	$errortype = array (
		E_ERROR              => 'Error',
		E_WARNING            => 'Warning',
		E_PARSE              => 'Parsing Error',
		E_NOTICE             => 'Notice',
		E_CORE_ERROR         => 'Core Error',
		E_CORE_WARNING       => 'Core Warning',
		E_COMPILE_ERROR      => 'Compile Error',
		E_COMPILE_WARNING    => 'Compile Warning',
		E_USER_ERROR         => 'User Error',
		E_USER_WARNING       => 'User Warning',
		E_USER_NOTICE        => 'User Notice',
		E_STRICT             => 'Runtime Notice',
		E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
	);
	// set of errors for which a var trace will be saved
	$user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
    
	$err = "<errorentry>\n";
	$err .= "\t<datetime>" . $dt . "</datetime>\n";
	$err .= "\t<errornum>" . $errno . "</errornum>\n";
	$err .= "\t<errortype>" . $errortype[$errno] . "</errortype>\n";
	$err .= "\t<errormsg>" . $errmsg . "</errormsg>\n";
	$err .= "\t<scriptname>" . $filename . "</scriptname>\n";
	$err .= "\t<scriptlinenum>" . $linenum . "</scriptlinenum>\n";

	if(in_array($errno, $user_errors)){
		$err .= "\t<vartrace>" . wddx_serialize_value($vars, "Variables") . "</vartrace>\n";
	}
	$err .= "</errorentry>\n\n";
    
	// for testing
	// echo $err;

	// save to the error log, and e-mail me if there is a critical user error
	error_log($err, 3, "error.log");
	if($errno == E_USER_ERROR){
		echo "<pre>Critical User Error  $err>/pre>\n";
		die();
	}
}