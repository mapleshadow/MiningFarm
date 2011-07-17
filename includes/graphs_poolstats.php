<?php
															//Get credentials
																$getCredientials->getStats();
															
																$getCredientials->getAdminSettings();	
																
															//generate graph information
																//Get this pool/user mhash graph
																	$fifteenMinutesAgo = time();
																	$fifteenMinutesAgo -= 60*15;
																	$userHashHistoryQ = mysql_query("SELECT `id` FROM `stats_userMHashHistory` WHERE `username` LIKE '".$getCredientials->username.".%' AND `timestamp` >= '$fifteenMinutesAgo' AND `mhashes` > 0 LIMIT 1");
																	$minersOnline = mysql_num_rows($userHashHistoryQ);
																	
																//Predefine graphing arrays
																	$poolArray		= '';
																	$userTotalArray	= '';
																	$timeHashArray = '';
																	
																//Generate pool array from last fifteen mintutes of data
																	$i = 0;
																	$poolTotalQuery = mysql_query('SELECT `totalMhash`, `timestamp` FROM `stats_poolMHashHistory` WHERE `timestamp` >= "'.$fifteenMinutesAgo.'" ORDER BY `id` ASC');
																	while($poolStat = mysql_fetch_array($poolTotalQuery)){
											
																		//add divider if neccesarry
																			$i++;
																			if($i > 1){
																				$poolArray .= ',';
																				$timeHashArray .= ',';
																			}
																			
																		//add data to graph
																			$poolArray .= $poolStat["totalMhash"];
																			$timeHashArray .= "'".date("G:i", $poolStat["timestamp"])."'";
																	
														
																		//Generate user total mhash array for display(If they are logged in)
																			if($loginValid == 1){
																				
																					//Is there any miners connected
																						if($minersOnline > 0){
																						
																							//Generate output for total mhash display
																								
																								//Add divider(if neccessary)
																									if($i > 1){
																										$userTotalArray .= ',';
																									}
																							
																								//Query total mining powa at this timestamp
																									$miningPowa = mysql_query('SELECT SUM(mhashes) AS `UserTotal` FROM `stats_userMHashHistory` WHERE `username` LIKE "'.$getCredientials->username.'.%" AND `timestamp` = "'.$poolStat["timestamp"].'"');
																									$miningPowaObj = mysql_fetch_object($miningPowa);
																									
																							//Add data to graph
																								$userTotalArray .= $miningPowaObj->UserTotal;
																								
																						
																						}
																							
																			}
																	}
																	
															
														?>
														<script type="text/javascript">
															var chart1; // globally available
															$(document).ready(function() {
																chart1 = new Highcharts.Chart({
																	chart: {
																		renderTo: 'graph',
																		defaultSeriesType: 'spline',
																		width:750,
																		height:250
																	},
																	title: {
																		text: 'Overall Network Status - <?php echo date("e", time());?> timezone'
																	},
																	xAxis: {
																		categories: [<?php echo $timeHashArray;?>]
																	},
																	yAxis: {
																		title: {
																			text: 'Mega-Hashes'
																		}
																	},
																	 plotOptions: {
																		line: {
																			dataLabels: {
																				enabled: true
																			},
																			enableMouseTracking: false
																		}
																		},
																	series: [{
																		name: 'Pool ',
																		data: [<?php echo $poolArray; ?>]
																		},
																		{
																		name: 'Pool Valid Shares',
																		data: [<?php echo $poolArray; ?>]
																		}
																		<?php if($userTotalArray != ''){?>
																		,{
																		name: 'Total User',
																		data: [<?php echo $userTotalArray;?>]
																		}
																		<?php } ?>]
																});
															});
														</script>
														<div id="graph" align="center">
														</div><br/><br/>
