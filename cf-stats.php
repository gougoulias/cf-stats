<?php
/**
 * Plugin Name: CF stats
 * Plugin URI: https://github.com/gougoulias
 * Description: Contact form 7 submitions by flamingo statistics view.
 * Version: 1.0
 * Author: Giannis Gougoulias
 * Author URI: https://github.com/gougoulias
 */

function cf_stats_plugin($atts){
 	// built the shorcode attributes
 		// name = the name of the contactform
		// group = the fileds that need to be grouped
	extract(shortcode_atts(array(
		'name'=> '',
		'group'=>'',	
	),$atts));

	$allgroups=explode(',', $group);
	//echo "all groups: ";
	//create empty array for each group that is reported in the shortcode parameter
	foreach ($allgroups as $sgroup) {
		//echo $sgroup . ', ';
		$groups_array=array();
		$groups_array[$sgroup]=array();
	}


	// check if the name parameter is given in the shortcode
	if ($name!=null){

		// set up the post arguments to get all the flamingo inbounds port types IDs and store them to flamingo_post variable
		$args =array(
			'post_type'=> 'flamingo_inbound',
			'fields'=> 'ids',
		);

		$flamingo_posts=get_posts($args);
		// echo "<br>flamingo posts :<br>";
		// print_r($flamingo_posts);
		// echo "<br>flamingo posts ends :<br>";

		//prepare regular expression
		$pattern = "/_field_/i";

		//$temp_group= array();
		//get the fields values
		foreach ($flamingo_posts as $flp) {
			//echo "<br>flamingo post start<br>";
			// echo "<br>flamingo posts meta :<br>";
			// print_r(get_post_meta($flp));
			// echo "<br>flamingo posts meta ends :<br>";
			// echo '</br>-----<br>';

			//echo "<br>get taxonomies: <br> ";
			$flamingo_taxonomy=wp_get_object_terms($flp,'flamingo_inbound_channel');
			//print_r($flamingo_taxonomy);
			foreach ($flamingo_taxonomy as $fltax) {
			 	$flamingo_taxonomy_name=$fltax->name;
			 } 
			//echo "this post belongs to form with name : " . $flamingo_taxonomy_name ;
			//echo "<br>get taxonomies ends<br>";
			
			if ($flamingo_taxonomy_name==$name){
				echo "<br>Flamingo post start<br>";
				$get_the_keys=array_keys(get_post_meta($flp));
				//print_r($get_the_keys);
				//$isgroup=false;
				foreach ($get_the_keys as $fkey) {
					if (preg_match('/_field_/i', $fkey) ){
						
						//start grouping
						foreach ($allgroups as $sgroup) {
							$group_field='_field_';
							$group_field.=(string)$sgroup;
							//echo $group_field;
							if($fkey==$group_field){
								echo " einai group <br>";
								echo "<br>--------group starts HERE--------------<br>";
								$group_name=substr($fkey,7);
								echo "GROUP NAME = ". $group_name . '<br>';
								$value=get_post_meta($flp)[$fkey][0];
								$regex_value='(.*:")';
								$replacement = '$1';
								$newvalue= preg_replace($regex_value, $replacement, $value);
								$regex_value='(".*)';
								$newvalue= preg_replace($regex_value, $replacement, $newvalue);
								echo 'kathari timi = ' . $newvalue .'<br>';
								//second loop starts here
								foreach ($get_the_keys as $fkeygrouped) {
									if (preg_match('/_field_/i', $fkeygrouped) ){
										echo 'to kleidi einai : ' .$fkeygrouped . '<br>';
										print_r(get_post_meta($flp)[$fkeygrouped]);
										echo '</br>';
										$valuegrouped=get_post_meta($flp)[$fkeygrouped][0];
											// $regex_value='(.*:")';
											// $replacement = '$1';
											// $newvaluegrouped= preg_replace($regex_value, $replacement, $valuegrouped);
											// $regex_value='(".*)';
											// $newvaluegrouped= preg_replace($regex_value, $replacement, $newvaluegrouped);
											// echo 'kathari timi new value= ' . $newvaluegrouped .'<br>';
										//keep the values to the array with the newvalue
										//$groups_array[$group_name][$newvalue][$fkeygrouped][]=get_post_meta($flp)[$fkeygrouped];
										//$groups_array[$group_name][$newvalue][$fkeygrouped][]=$newvaluegrouped;
										$groups_array[$group_name][$newvalue][$fkeygrouped][]=$valuegrouped;
									}
								}
								echo "<br>--------group ENDS HERE--------------<br>";
							}
						}//end grouping

						echo 'to kleidi einai : ' .$fkey . '<br>';
						print_r(get_post_meta($flp)[$fkey]);
						echo '</br>';
						$value_ungrouped=get_post_meta($flp)[$fkey][0];
							// $regex_value='(.*:")';
							// $replacement = '$1';
							// $newvalue_ungrouped= preg_replace($regex_value, $replacement, $value_ungrouped);
							// $regex_value='(".*)';
							// $newvalue_ungrouped= preg_replace($regex_value, $replacement, $newvalue_ungrouped);
							// echo 'kathari timi ungrouped = ' . $newvalue_ungrouped .'<br>';
						// keeps the values to the 'ungrouped' array
						//$groups_array['ungrouped']['ungrouped'][$fkey][]=get_post_meta($flp)[$fkey];
						// $groups_array['ungrouped'][$fkey][]=$newvalue_ungrouped;
						$groups_array['ungrouped']['ungrouped'][$fkey][]=$value_ungrouped;
						echo "<br>";
						
					}
				}
				echo "<br>Flamingo post Ends<br>";
			}
		}
	}else{
		echo "<br>The shortcode parameter 'name=' is required <br>";
	}
	echo 'groups arrays is <br>';
	echo'<pre>';
	print_r($groups_array);
	echo'</pre>';
	// how to count 1st try starts
	echo '<br>counting starts<br><pre>';
	foreach ($groups_array as $ga) {
		//print_r($ga)
		foreach ($ga as $cgroups){
			//print_r($cgroups);
			foreach ($cgroups as $cvalues) {
				//print_r($cvalues);
				print_r(array_count_values($cvalues));
				// foreach ($cvalues as $cv) {
				// 	//print_r($cv);
				// 	print_r(array_count_values($cv));
				// }
			}	
		}	
	}

	//print_r(array_count_values($groups_array));
	echo '</pre><br>counting ends<br>';
	// how to count 1st try ends
	echo "<br>------------<br>";
	//print_r($groups_array);
	$json_records=json_encode($groups_array);
	echo 'this is the json :<br>' . $json_records .'<br>';
}

add_shortcode('cf-stats','cf_stats_plugin');