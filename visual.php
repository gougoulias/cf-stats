<?php
// call the array counted to assign the values so it can be viewable in the script adding label and y keys START
foreach ($counted as $parrent_group => $grouped1) {
	//echo '<p>'.$parrent_group.'</p>';
	foreach ($grouped1 as $groupped_value => $grouped2 ){
		//echo '<p>' . $groupped_value. '</p>';
		$group_count=$group_count+1;
		foreach ($grouped2 as $onegroup => $onegroupvalue) {
			$i=0;//counter for the answers
			foreach ($onegroupvalue as $label => $y) {
				//preg replace special characters with space to prevent the brake of the script bellow
					$dataPoints[$groupped_value][$onegroup][$i]["label"]=preg_replace('/[\'?\/\(\)\[\];]/', '', $label);
					$dataPoints[$groupped_value][$onegroup][$i]["y"]=preg_replace('/[\'?\/\(\)\[\];]/', '', $y);
					$i=$i+1;
			}
		}
	}
}
// call the array counted to assign the values so it can be viewable in the script adding label and y keys ENDS

//create arrays for groups and questions START
foreach ($dataPoints as $dPkey => $dPvalue) {
	//echo "- 1." . $dPkey . $dPvalue; 
	$all_groups[]=$dPkey;
	foreach ($dPvalue as $groupnamekey => $groupnamevalue) {
		//echo "- 2." .$groupnamekey;
		$allquestions[]=$groupnamekey;

	}
}
//create arrays for groups and questions ENDS

//unique the groups
$all_groups_unique=array_unique($all_groups);
//unique the questions
$all_questions_unique=array_unique($allquestions);


// testing purposes only STARTS
//create the arguments to get all forms ids STARTS
$args =array(
	'post_type'=> 'wpcf7_contact_form',
	'fields'=> 'ids',
	'nopaging'=> true,
);
$all_forms=get_posts($args);
//create the arguments to get all forms ids ENDS

//get all the formtags from the form that is called in shortcode name STARS
foreach ($all_forms as $cf7form) {
	//echo get_the_title($cf7form);
	if(get_the_title($cf7form)==$name){
		$ContactForm = WPCF7_ContactForm::get_instance( $cf7form );
		$form_fields = $ContactForm->scan_form_tags();
	}
}
//get all the formtags from the form that is called in shortcode name ENDS

//create store fields array and store inside all the names and the possible anwsers START
$store_fields= array();
foreach ($form_fields as $ffkey => $ffvalue) {
	//print_r($ffvalue->name);
	//print_r($ffvalue->labels);
	$store_fields[$ffvalue->name]=$ffvalue->labels;
}
//create store fields array and store inside all the names and the possible anwsers ENDS


$number_of_questions=count($allstats);
// echo $number_of_questions;
// echo "<br>";

// echo "<br>unique questions <br>";
// print_r($all_questions_unique);
// echo "<br>";
// echo "<br>all stats<br>";
// print_r($allstats);
// echo "<br>";
// echo "<br>unique groups <br>";
// print_r($all_groups_unique);
// echo "<br>";


// foreach ($allstats as $questionkey => $questionvalue) {
// 	//echo 'edw arxizei to chart' ;
	
// 	echo '<hr><hr><br>1. question : '. $questionvalue;
// 	foreach ($all_groups_unique as $groupkey => $groupvalue) {
// 		//echo "edw arxizei to kathe group loop  ";
// 		echo '<br>2. group : '. $groupvalue;
// 		//echo '<br> this is the final: <br> ' ;
// 		//print_r($dataPoints[$groupvalue][$questionvalue]);
// 		//edw paizw mpala
// 		foreach ($store_fields[$questionvalue] as $possibleanswerskey => $possibleanswersvalue) {
// 			(string)$possibleanswersvalue_fin='"';
// 			(string)$possibleanswersvalue_fin.=$possibleanswersvalue;
// 			(string)$possibleanswersvalue_fin.='"';
// 			echo '<hr><br>3. possible answers value: '. $possibleanswersvalue_fin;
// 			$found=false;
// 			foreach ($dataPoints[$groupvalue][$questionvalue] as $real_answer_key => $real_answer_value) {
// 				echo '<br>4. real answers : ';
// 				print_r ($real_answer_value['label']);
// 				if ($possibleanswersvalue_fin==$real_answer_value['label']){
// 					echo '<br> this is the final: <br> ' ;
// 					print_r($real_answer_value);
// 					$found=true;
// 				}else{
// 					//echo '<br> this is the final: <br> ' ;
// 					//echo 'Array([label]=>'.$possibleanswersvalue_fin.' [y]=0)';
// 				}
// 			}
// 			if ($found==false){
// 				echo '<p style="color:red">den to brikame</p>';
// 				echo 'Array ( [label]=>'.$possibleanswersvalue_fin.' [y]=>0 )';
// 			}else{
// 				echo '<p style="color:green">to brikame</p>';

// 			}
// 		}
// 		//echo "edw teleiwnei to kathe group loop  ";

// 	}
// 	//echo 'edw teleiwnei  to chart' ;
	
// }

// testing purposes only ENDS
?>



<!--starting the script-->
<script>
	window.onload = function () {
<?php 

//foreach ($all_questions_unique as $questionkey => $questionvalue) {
foreach ($allstats as $questionkey => $questionvalue) {
	if (in_array($questionvalue, $allstats)){ ////checks if the question is going to be viewable or if it is used only for groupping
		?>
		 
		var chart<?php echo str_replace('-','',$questionvalue) ;?> = new CanvasJS.Chart("chart-<?php echo $questionvalue ;?>", {
			animationEnabled: true,
			theme: "light2",
			title:{
				text: "<?php echo $questionvalue; ?>"
			},
			axisY:{
				includeZero: true
			},
			legend:{
				cursor: "pointer",
				verticalAlign: "center",
				horizontalAlign: "right",
				itemclick: toggleDataSeries<?php echo str_replace('-','',$questionvalue) ;?>
			},
			data: [
				<?php
				foreach ($all_groups_unique as $groupkey => $groupvalue) {
					if ($groupvalue=='ungrouped'){
						$groupvaluetitle='Συνολικές απαντήσεις';
					}else{
						$groupvaluetitle=$groupvalue;
					}
			 		 ?>
					{
						type: "column",
						name: "<?php echo $groupvaluetitle ; ?>",
						indexLabel: "{y}",
						yValueFormatString: "#0.##",
						showInLegend: true,
						//if the group value title is not the total make it non visible by default
						<?php if  ($groupvaluetitle!='Συνολικές απαντήσεις'){ ?>
							visible: false,
						<?php } ?>
						dataPoints: [<?php 
						//echo json_encode($dataPoints[$groupvalue][$questionvalue], JSON_NUMERIC_CHECK);
							foreach ($store_fields[$questionvalue] as $possibleanswerskey => $possibleanswersvalue) {
								(string)$possibleanswersvalue_fin='"';
								(string)$possibleanswersvalue_fin.=$possibleanswersvalue;
								(string)$possibleanswersvalue_fin.='"';
								$found=false;
								foreach ($dataPoints[$groupvalue][$questionvalue] as $real_answer_key => $real_answer_value) {
									if ($possibleanswersvalue_fin==$real_answer_value['label']){
										echo json_encode($real_answer_value, JSON_NUMERIC_CHECK);
										echo ",";
										$found=true;
									}
								}
								if ($found==false){
									if ($excludezero!='yes'){ //check shortcode value to include or not the zero values
										$temparray=array("label"=> $possibleanswersvalue_fin, "y"=> "0");
										echo json_encode($temparray, JSON_NUMERIC_CHECK);
										echo ",";
									}
								}
							}
						 ?>
					]},
					<?php		
				} ?>
			]
		});
		chart<?php echo str_replace('-','',$questionvalue) ;?>.render();
		 
		function toggleDataSeries<?php echo str_replace('-','',$questionvalue) ;?>(e){
			// create loop for each group so se visible only the selected one and hide others
			<?php foreach ($all_groups_unique as $groupnumber => $groupname) { ?>
				//console.log("epanalipsi" + <?php echo $groupnumber ?> );
				if (e.dataSeries.name=== e.chart.options.data[<?php echo $groupnumber; ?>].name){
					e.chart.options.data[<?php echo $groupnumber; ?>].visible = true;
				}else{
					e.chart.options.data[<?php echo $groupnumber ; ?>].visible = false;
				}
			<?php } ?>
			chart<?php echo str_replace('-','',$questionvalue) ;?>.render();
		}
		 
		<?php	
	}
}

?>
}
</script>
<!-- script end here -->
<?php


//create the loop for all divs the charts will be shown STARTS
//foreach ($all_questions_unique as $questionkey => $questionvalue) {
foreach ($allstats as $questionkey => $questionvalue) {
	if (in_array($questionvalue, $allstats)){ //checks if the question is going to be viewable or if it is used only for groupping
		?>
		<!-- create the div for each chart -->
		<div id="chart-<?php echo $questionvalue ; ?>" style="height: 370px; width: 100%;"></div>
		<?php
	}
}
//create the loop for all divs the charts will be shown ENDS
?>

<!--call the canvas js script-->
<!--<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>-->
<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


<?php