<?php
if( !defined('GC_NAME') ) exit; // 개별 페이지 접근 불가

GC_VAR()->add_inline_scripts("$(\"body\").append('<div id=\"kakaopay_layer\"  style=\"display: none\"></div>')");
?>