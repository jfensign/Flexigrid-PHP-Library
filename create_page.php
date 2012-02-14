<?php
//require scripts
require_once('flexigridPHP/flexigrid.php');
require_once('flexigridPHP/flexigridObserver.php');

//new Observer (OPTIONAL)
$Observer = new UI_Observer();

//new Subjects
$u = new flexigrid("users");
$p = new flexigrid("pages");

//attach subjects to observer(s) (OPTIONAL)
$u->attach($Observer);
$p->attach($Observer);

//output json.
$u->output_json("id", array("userid", "firstname", "lastname", "notes", "admin"));
?>