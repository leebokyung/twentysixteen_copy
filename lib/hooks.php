<?php

add_filter('gc_load_common_css', 'sir_comm_load_shop_css', 10, 3);

function sir_comm_load_shop_css($load_common_css, $post, $pages){

if(! isset($post->it_skin) ){
return $load_common_css;
}

if( empty($post->it_skin) ){
$post->it_skin = 'basic';
}

foreach( $load_common_css as $key=>$v ){
if( empty($v) ) continue;

if( $v['handle'] == GC_NAME.'-'.$post->it_skin.'-css' ){
$tmp = explode(GC_NAME.'/', $v['src']);

$file_check = get_template_directory().'/'.GC_NAME.'/'.$tmp[1];

if( file_exists($file_check) ){
$load_common_css[$key]['src'] = get_template_directory_uri().'/'.GC_NAME.'/'.$tmp[1];
}
}
}

return $load_common_css;

}

?>