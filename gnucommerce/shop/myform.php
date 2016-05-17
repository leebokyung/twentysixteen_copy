<?php
if( !defined('GC_NAME') ) exit;

$user = get_userdata( get_current_user_id() );

gc_skin_load('myform.skin.php', array('user'=>$user) );
?>