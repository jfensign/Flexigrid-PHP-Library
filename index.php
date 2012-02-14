<!DOCTYPE html>
<html>

<?php
function url(){
  $protocol = $_SERVER['HTTPS'] ? "https" : "http";
  return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
?>

<head>
  <title>Flexigrid Class</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	
	<LINK rel="stylesheet" href="<?php echo 'css/flexigrid.css';?>" media="screen" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo 'js/flexigrid.js';?>"></script>
	
	<script type="text/javascript">
$(document).ready(function(){

	$("#flex1").flexigrid({
		url: 'http://localhost/table/create_page.php',
		dataType: 'json',
		colModel : [
			{display: 'User Id', name : 'userid', width : 180, sortable : true, align: 'left'},
			{display: 'Firstname', name : 'firstname', width : 120, sortable : true, align: 'left'},
			{display: 'Lastname', name : 'lastname', width : 130, sortable : true, align: 'left', hide: true},
			{display: 'Notes', name : 'notes', width : 130, sortable : true, align: 'left', hide: true}
			],
		sortname: 'userid',
		sortorder: "asc",
		usepager: true,
		title: 'Users',
		useRp: true,
		rp: 15,
		showTableToggleBtn: true,
		width: 700,
		height: 200
	});   
	
});
	</script>

</head>
<body>

  <table id="flex1"></table>

</body>
</html>