<html>
<head>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<link rel="stylesheet" href="css/mainsite.css">
<link rel="stylesheet" href="tablesorter/css/theme.blue.css">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>DC @ MNNIT Allahabad	</title>
<style>
#inames option{
	height:40px;
	background-color:#F00;	
}
label.no-styl{
	font-weight:normal;
}
</style>
<script src="js/jquery-1.11.2.js"></script>
<script type="text/javascript" src="tablesorter/js/jquery.tablesorter.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/search.js"></script>
<script type="text/javascript">
$(document).ready(function(e) {
	
    $("input[type=checkbox]").each(function() {
		$(this).attr('checked','checked');        
    });
	$("#reqtab").tablesorter();
	
	$("#sr").keyup(function(e) {
        str=$(this).val();
		$("table tbody tr").each(function() {			
			if($(this).children("td:nth-child(2)").html().search(str)>=0)
				$(this).show();
			else
				$(this).hide();			
		});
    });
	$("#sr").change(function(e) {
        str=$(this).val().toUpperCase();
		$("table tbody tr").each(function() {
			if($(this).children("td:nth-child(2)").html().toUpperCase().search(str)>=0)
				$(this).show();
			else
				$(this).hide();			
		});
    });
});


function fun3(cat)
{
	if($("#c_"+cat).is(':checked'))
	{
		fl=true;
		//$("#c_"+cat).attr('checked','checked');
		//alert('check');
	}
	else{
		fl=false;//$("#c_"+cat).attr('checked','checked');
		//alert('uncheck');
	}
	$("table tbody tr").each(function() {
		//alert(cat+" -> "+$(this).children("td:first").html());
        if($(this).children("td:first").html()===cat){
			if(fl)
				$(this).show();
			else
				$(this).hide();
		}
    });
}
function fun4(cat)
{
	
	if($("#f_"+cat).is(':checked'))
	{
		fl1=true;
		//$("#c_"+cat).attr('checked','checked');
		//alert('check');
	}
	else{
		fl1=false;//$("#c_"+cat).attr('checked','checked');
		//alert('uncheck');
	}
	if(cat==="AlreadyPresent")
		cat="Already Present";
	$("table tbody tr").each(function() {
		//alert(cat+" -> "+$(this).children("td:first").html());
		
        if($(this).children("td:nth-child(3)").html().substring(0,cat.length)==cat){
			if(fl1)
				$(this).show();
			else
				$(this).hide();
		}
    });
}
</script>
</head>
<body>
<?php
$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
session_start();
include_once('function.php');
getHeader("request.php");
foreach($_POST as $a=>$b)
	$_POST[$a]=validate($b);
foreach($_GET as $a=>$b)
	$_GET[$a]=validate($b);
$mysqli=dbconnect();
if(isset($_POST['submit'])){
	$errval=0;
	$name=$_POST['inames'];
	if(!isset($_POST['inames'])|| empty($_POST['inames']) ||!isset($_POST['category'])|| empty($_POST['category'])){
			//error message
			$errmsg="Invalid Response";
			$errval++;
			//die('invalid response');
	}
	else if(strlen($name)>250){
		$errmsg="Name cannot be more that 250 characters";
		$errval++;
	}
	else if(strlen($_POST['category'])>90){
		$errmsg="Category cannot be more that 90 characters";
		$errval++;
	}
	else {
		$chkq="select * from dcrequests where name like '$name'";// and category like '$_POST[category]'
		$res=$mysqli->query($chkq);
		if($res->num_rows>0){
			$errval++;
			$errmsg="Request already exist!!!";
		}
		else {
			$insertq="INSERT INTO `dcp`.`dcrequests` (`category`, `name`, `status`,`ip`) VALUES (?, ?,'Pending',?);";
			$stmt=$mysqli->prepare($insertq);
			$stmt->bind_param('sss',$_POST['category'],$_POST['inames'],$ip);
			$stmt->execute();
			$stmt->bind_result($res);
			//success message
		}
	}
}
?>
<h3 class="text-info col-md-offset-3"><span class="glyphicon glyphicon-question-sign"></span> Not available on DC++? Request here</h3>
<div id="request" class="col-md-6 col-md-offset-3">
<hr style="margin-top:-5px"/>
	<form class="form-horizontal" method="post" action="">
<fieldset>

<!-- Form Name -->
<?php
	if(isset($errval)){
		if($errval>0)
			echo "<span class=\"text-danger\"><span class=\" glyphicon glyphicon-alert\"></span> $errmsg</span><br><br>";
		else echo "<span class=\"text-success\"><span class=\" glyphicon glyphicon-ok\"></span> request will be entertained soon.</span><br><br>";
	}
?>
<!-- Select Basic -->
<div class="form-group">
  <label class="col-md-4 control-label" for="category">Category</label>
  <div class="col-md-6">
    <select id="category" name="category" class="form-control">
	<?php
		$res=$mysqli->query("select * from item_category");
		while($arr=$res->fetch_array())
      		echo "<option value=\"$arr[category]\">$arr[category]</option>";
		?>
    </select>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="inames">Name</label>  
  <div class="col-md-6">
  <input list="inames" name="inames" onKeyUp="return search(this);" class="form-control input-md" required placeholder="e.g. Friends S01 720p">
  <datalist id="inames" >
  </datalist>
  <!--<span class="help-block">e.g. Friends S01 720p </span>-->  
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="submit"></label>
  <div class="col-md-4" style="margin:-5px 0 0 55px">
    <button id="submit" name="submit" class="btn btn-primary" type="submit">Request</button>
  </div>
</div>

</fieldset>
</form>
<hr style="margin-top:-15px"/>
</div>
<br />
<form class="form-inline" style="float:left;margin-left:17%;" id="fil_cat" onSubmit="return false;">
  	<b class="col-md-1">Category:</b>
  	<?php
    $res=$mysqli->query("select * from item_category");
		while($arr=$res->fetch_array())
      		echo "<div class='form-group checkbox' style='margin-left:30px'><label><input type='checkbox' id='c_$arr[category]' value='$arr[category] ' onclick='fun3(\"$arr[category]\")' >&nbsp;&nbsp;$arr[category] </label></div>";
	echo '<br/><br/><b class="col-md-1">Status:</b>';
	$stat_ar=array('AlreadyPresent','Fulfilled','Downloading','Pending','Invalid');
	foreach($stat_ar as $k)
	echo "<div class='form-group checkbox' style='margin-left:30px'><label><input type='checkbox' id='f_$k' value='$k' onclick='fun4(\"$k\")'>&nbsp;&nbsp;$k </label></div>";
	?><br/><br/>
  <b class="col-md-1">Search:</b><div class='col-lg-10' style='margin-left:30px'><input list='show_req' class='form-control' id='sr' placeholder="Press enter to search..." style="width:100%"/></div>
</form>

<div id="requesteditem" class="col-lg-10 col-lg-offset-1" style="margin-bottom:50px;">
	<table  class=" col-md-10 table  table-striped table-bordered table-hover tablesorter" id="reqtab" style="border-radius:5px 5px 0 0">
	<thead style="background-color:#203D77;color:#fff;"><th>Category</th><th width="65%">Name</th><th>Status</th></thead>
    <tbody>
	<?php
	$datasrch="<datalist id='show_req'>";
	$requestq="select * from dcrequests where status!='r' order by timeofreq desc";
	$res=$mysqli->query($requestq);
	while($req=$res->fetch_array()){
		$datasrch.="<option value='$req[name]'></option>";
		echo "<tr id='$req[id]'><td class=\"col-md-2\">$req[category]</td><td class=\"col-md-6\">$req[name]";
		if(!empty($req['link'])){
			echo "<a href='$req[link]'> <span class=\"glyphicon glyphicon-magnet\"></span> </a>";	
		}
		echo "</td><td class=\"col-md-4\" style=\"word-wrap: break-word;\">$req[status]";
		if(in_array($req['status'],array("Downloading","Fulfilled","Already Present")))
			echo " by <b>$req[fulfilledby]</b>";
		echo "</td></th>";
	}
	$datasrch.="</datalist>";
	
	?>
	</tbody></table>
</div>
<br>
<br>
<br>
<br>

<?php 
echo $datasrch;
getFooter(); ?>
</body>
</html>