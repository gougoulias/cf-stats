<?php

$group_count=0;
$max_final_questions=0;
foreach ($counted as $parrent_group => $grouped1) {
	//echo '<h3>'.$parrent_group.'</h3>';
	foreach ($grouped1 as $groupped_value => $grouped2 ){
		//echo '<p>' . $groupped_value. '</p>';
		$group_count=$group_count+1;
		$final_questions=0;
		foreach ($grouped2 as $onegroup => $onegroupvalue) {
			//echo '<small>'.$onegroup.'</small> | ';
			$final_questions=$final_questions+1;
			$i=0;//counter for the answers
			(string)$group_name='groupname';
			(string)$group_name=(string)$group_name.(string)$group_count;
			// echo 'group name is : ' .$group_name;
			// echo ' | onegroup is : ' . $onegroup;
			foreach ($onegroupvalue as $label => $y) {
					$dataPoints[$group_name][$parrent_group][$groupped_value][$onegroup][$i]["label"]=$label;
					$dataPoints[$group_name][$parrent_group][$groupped_value][$onegroup][$i]["y"]=$y;
					$dataPointsdemo[$groupped_value][$onegroup][$i]["label"]=$label;
					$dataPointsdemo[$groupped_value][$onegroup][$i]["y"]=$y;
					$i=$i+1;
			}
		}
		if($max_final_questions<$final_questions){
		$max_final_questions=$final_questions;
		}
	}
}


foreach ($dataPointsdemo as $dPkey => $dPvalue) {
	//echo "- 1." . $dPkey . $dPvalue; 
	$all_groups[]=$dPkey;
	foreach ($dPvalue as $groupnamekey => $groupnamevalue) {
		//echo "- 2." .$groupnamekey;
		$allquestions[]=$groupnamekey;
		foreach ($groupnamevalue as $gnvkey => $gnvvalue) {
			//echo "- 3." .$gnvkey . $gnvvalue;
			foreach ($gnvvalue as $lastkey => $lastvalue) {
				//	echo "<hr>";
				//echo '-4 ' . $lastkey . $lastvalue;
				//echo "- 1." . $dPkey ." - 2." .$groupnamekey. " - 3." .$gnvkey." - 4." .$lastvalue;
				//echo "<br>";
			}	
		}

	}
}
//echo "<br> all groups : ";
//print_r($allgroups);
$all_groups_unique=array_unique($all_groups);
//echo "<br> all groups unique: ";
//print_r($all_groups_unique);
$all_questions_unique=array_unique($allquestions);
//print_r($all_questions_unique);


// // echo "<br>unique groups";
// // print_r($all_groups_unique);
// foreach ($all_questions_unique as $questionkey => $questionvalue) {
// 	//echo 'edw arxizei to chart' ;
// 	echo '<br>1. question : '. $questionvalue;
// 	foreach ($all_groups_unique as $groupkey => $groupvalue) {
// 		//echo "edw arxizei to kathe group loop  ";
// 		echo '<br>2. group : '. $groupvalue;
// 		echo '<br> this is the final: <br> ' ;
// 		print_r($dataPointsdemo[$groupvalue][$questionvalue]);
// 		//echo "edw teleiwnei to kathe group loop  ";

// 	}
// 	//echo 'edw teleiwnei  to chart' ;
// }

//try 2 ends
?>



<!--starting the script-->
<script>
	window.onload = function () {
<?php 

foreach ($all_questions_unique as $questionkey => $questionvalue) {
	?>
	 
	var chart = new CanvasJS.Chart("chart-<?php echo $questionvalue ;?>", {
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
			itemclick: toggleDataSeries
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
					<?php if  ($groupvaluetitle!='Συνολικές απαντήσεις'){ ?>
						visible: false,
					<?php } ?>
					dataPoints: <?php echo json_encode($dataPointsdemo[$groupvalue][$questionvalue], JSON_NUMERIC_CHECK); ?>
				},
				<?php		
			} ?>
		]
	});
	chart.render();
	 
	function toggleDataSeries(e){
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
			e.dataSeries.visible = false;
		}
		else{
			e.dataSeries.visible = true;
		}
		chart.render();
	}
	 
	<?php	
}

?>
}
</script>
<!-- script end here -->
<?php

foreach ($all_questions_unique as $questionkey => $questionvalue) {
	?>
	<!-- create the div for each chart -->
	<div id="chart-<?php echo $questionvalue ; ?>" style="height: 370px; width: 100%;"></div>
	<?php
}
?>

<!--<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>-->
<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


<?php
