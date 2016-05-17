<?php
if( ! defined( 'GC_NAME' ) ) exit;

if( is_user_logged_in() ) return;   //로그인 했으면 리턴

gc_not_permission_page();
gc_skin_load('check_user_orderform.skin.php');
?>