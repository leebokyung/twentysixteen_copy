<?php
if( !defined('GC_NAME') ) exit;

if(!isset($it) || !is_array($it)){
    return;
}

//사용후기

$status_map = array(
    'moderated' => 'hold',
    'approved' => 'approve',
    'all' => '',
);

$args = array(
    'post_id'=>$it['ID'],
    'status'=>$status_map,
);

$comments = get_comments($args);

gc_skin_load('itemuse.skin.php', array('it'=>$it, 'comments'=>$comments));
?>