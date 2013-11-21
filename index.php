<?php include('includes/mydb-functions.php');?>
<!DOCTYPE html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Monthlee</title>
<script type='text/javascript' src='//code.jquery.com/jquery-1.10.2.min.js'></script>
<link href='css/hwb.css' rel="stylesheet" type="text/css" />


</head>

<body>

<header>
    	<h1><a href="index.php">Monthlee</a></h1>
        <div class="clock">Today is <?php echo date('M d, Y') ?></div>
        <div class="view-choice">
        	<span id="list" class="view-icon"></span><span id="layout" class="view-icon selected"></span>
        </div>
        <div class="legend">
            <span class="legend-item legend-homework">Regular</span>
            <span class="legend-item legend-important">Important</span>
        </div>
</header>

<!-- calendar -->

<div id='calendar'>

<?php
	connect_to_db();
	// Calendar Part!!
	$cur = date("Y-m-j");// current date formatted to yyyy-mm-dd
	$cury = date("Y");
	$curm = date("m");
	$curj = date("j");
	$curwd = date("w"); // 0 - SUN, 6 - SAT
	$thisweekstart = date("Y-m-j", strtotime((-1*$curwd)." day"));
	
	$one = strtotime("+1 week"); // the last day of the month two month in future
	$nom = array("January","February","March","April","May","June","July","August","September","October","November","December");
	
	$leftdays = (21-$curwd)-1;
	$oneweekend = date("Y-n-j", strtotime($leftdays." day", $one));
	
	$sql = "SELECT * FROM homework WHERE due BETWEEN '".$thisweekstart."' AND '".$oneweekend."'";
	$result = query($sql);
	if(!$result){
		die("query fail : ".mysqli_error($link));
	} else {
?>
			<div class="monthBox">
				<div class="calMonth"><h2><?php echo $nom[($curm-1)]; ?></h2></div>
                <div id="days">
                    <div class='calweek left'>Sun</div>
                    <div class='calweek left'>Mon</div>
                    <div class='calweek left'>Tue</div>
                    <div class='calweek left'>Wed</div>
                    <div class='calweek left'>Thu</div>
                    <div class='calweek left'>Fri</div>
                    <div class='calweek left'>Sat</div>
                </div>
             </div>     

<?php		
		$pastflag = 0;
		while($row = mysqli_fetch_array($result)){
			$theDay = $row['due'];
			$theDay = explode("-",$theDay);
			$theYear = $theDay[0];
			$theMonth = $theDay[1];
			$theDate = $theDay[2];
			
			if(substr($theDate,0,1) == 0){
				$theDate = substr($theDate,1,1);
			}
			if(substr($theMonth,0,1) == 0){
				$theMonth = substr($theMonth,1,1);
			}
			
			$innersql = "SELECT hwid, hwname, detail FROM detail WHERE due = '".$row['due']."' ";
			$result2 = query($innersql);
			
			
			$hwlist = array();
			
			$testing = array(array('hello','bye'),array('yes','no'),array('tomorrow','today'),array());
			
			
			
			if(!$result2){
				die("Failed to query".mysqli_error($link));
			} else {
				
				while($row2 = mysqli_fetch_array($result2)){
					array_push($hwlist, array($row2['hwid'],$row2['hwname'],$row2['detail']));
				}
			}
			
			
			if($theMonth != $curm){
				$curm++;
				
				$fdnm;
				if($curm > 12){
					$fdnm = mktime(0, 0, 0, 1, 1, substr(($cury+1),2,2));
				} else {
					$fdnm = mktime(0, 0, 0, $curm, 1, substr($cury,2,2));
				}
?>
				<div class="cal left calday <?php if($pastflag < $curwd) echo 'past'; ?>" data-date="<?php echo $row['due']; ?>"><span class="thedate"><?php echo $theMonth." / ".$theDate; ?></span>
                    <ul class="event-names">
                    <?php
                        if($row['hwnum'] > 0){
                            for($z=0; $z < $row['hwnum']; $z++){
                                echo "<li class='hw'>".$hwlist[$z][1]."</li>";
                            }
                        } else {
                            echo "";
                        }
                    ?>
                    </ul>
                </div>
<?php
			} else {
?>
				<div class="cal left calday <?php if($theDate == $curj) echo 'today'; if($pastflag < $curwd) echo 'past'; ?>" data-date="<?php echo $row['due']; ?>"><span class="thedate"><?php echo $theDate; ?></span>
                    <ul class="event-names">
                    <?php
                        if($row['hwnum'] > 0){
                            for($z=0; $z < $row['hwnum']; $z++){
								if(!empty($hwlist[$z][1])) {
								?><li class='hw'><?php echo $hwlist[$z][1]; ?></li><?php
								}
                            }
                        } else {
                            echo "";
                        }
                    ?>
                    </ul>
                </div>
<?php
			}
		$pastflag++;
		}
	}
?>

<!--END CALENDAR-->
</div>
<div id="overlay"></div>
<footer>
    <p>Have a nice day :)</p>
</footer>

<script>


	
	
	function getData(day) { 
		var eventName = $('#name').val();
		var description = $('#desc').val(); //Solution: no array, function is repeating how many ever times i've clicked
		
		$.ajax({
		  url: "update-homework-handler.php",
		  type:"post",
		  data: { hwname:eventName, desc:description, due:day },
		  success: function(response) {
			console.log(response);
			$('#name').val("");
			$('#desc').val("");
			$('form').fadeOut(300);
			$('#overlay').hide();
			$('.calday').each( function(i,v) {
				if( $(this).attr('data-date') == day ) {
					console.log(this);
					console.log(i);
					$(this).append('<li class="hw left" style="padding-left: 10px;">'+response+'</li>');
				}
			});
		  }
		});
	}


$('.calday:not(.past)').click( function() {
	
	var $thisDay = $(this).attr('data-date'); //Problem: somehow storing the previous days clicked on?
	
	$('input[type="button"]').click( function() {
		
		getData($thisDay);	
		/*$(document).keypress(function(e) {
			if(e.which == 13) {
				getData();
			}
		});*/
		
	});
	
	$('form').fadeIn(300);	
	$('#overlay').fadeIn(300);
	$('#name').focus();
});
$('#list,#layout').click( function() {
	
	$(".view-icon").each(function(index, value) { 
	
		$(this).removeClass('selected');
		
	});
	
	if( $(this).hasClass('selected') ) {
		$(this).removeClass('selected');
	} else {
		$(this).addClass('selected');
	}
});

$('#list').click( function() {
	$('.calday').css({'width':'100%'});
	$('#days').css({
		'position':'absolute',
		'left':'0'	
	});
	
	$('.calweek').each(function(i,v) {
		$(this).removeClass('left');
	});
	
	$('.calweek').css({
		'display':'block',
		'margin':'3px 5px 146px',
		'padding-left':'27px'
	});	
});

$('#layout').click( function() {
	$('.calday').css({'width':'14.28%'});
	$('#days').css({
		'position':'static'
	});
	$('.calweek').addClass('left');
	$('.calweek').css({
		'display':'inline',
		'margin':'auto',
		'width':'14.28%',
		'padding-bottom':'10px',
		'padding-left':'0'
	})
})

$('#overlay').click(function() {
	$('#input').fadeOut(300);
	$(this).hide(300);
});
</script>
<form id="input">
<h5>Schedule Date<span class="close"></span></h5> 
<?php
connect_to_db();
?>
	<ul>
		<li>
			<input type="text" name="hwname" placeholder="Name" id="name">
		</li>
		<li>
			<textarea name="desc" placeholder="Description" id="desc"></textarea>
		</li>
      
		<li><input type="button" value="Add"></li>
	</ul> 
</form>
</body>

</html>