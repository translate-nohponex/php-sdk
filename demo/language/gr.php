<?php
require_once( '../src/translate.php' );
global $strings;

//Use global object
global $translate;
global $project_id; 

//Overwrite global variables strings
$strings = $translate->fetch( $project_id, 'gr' );

?>