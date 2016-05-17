<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since sir community 1.0
 */

get_header(); ?>

    <div id="primary" class="content-area">
        
        <div id="idx_banner">
            <h2>이벤트 및 광고 배너</h2>
            <ul class="bxslider">
                <li><a href="#"><img src="<?php echo get_template_directory_uri();?>/img/banner01.png" alt="" /></a></li>
                <li><a href="#"><img src="<?php echo get_template_directory_uri();?>/img/banner01.png" alt="" /></a></li>
            </ul>
        </div>
        <script>
            jQuery('.bxslider').bxSlider({
                auto: true,
                autoControls: true
            });
        </script>

        <div id="idx_shortcut">
            <ul>
                <li class="sc_notice"><a href="#"><span>공지</span></a></li>
                <li class="sc_latest"><a href="#"><span>최근글</span></a></li>
                <li class="sc_g5"><a href="#"><span>그누보드5</span></a></li>
                <li class="sc_yc5"><a href="#"><span>영카트5</span></a></li>
                <li class="sc_data"><a href="#"><span>회원자료</span></a></li>
            </ul> 
            <ul class="ul-margin">
                <li class="sc_gallery"><a href="#"><span>갤러리</span></a></li>
                <li class="sc_nquiryt"><a href="#"><span>1:1문의</span></a></li>
                <li class="sc_contact"><a href="#"><span>오시는 길</span></a></li>
                <li class="sc_tip"><a href="#"><span>강좌/팁</span></a></li>
                <li class="sc_customer"><a href="#"><span>고객센터</span></a></li>
            </ul>
        </div>


        <div id="idx-new-content">
            

            <div class="new-content">
                <h2><a href="#"><i class="fa fa-comment" aria-hidden="true"></i> 커뮤니티</a></h2>
                <ul>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 1 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 2 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 3 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 4 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 5 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 6 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                </ul>
                <a href="#" class="new-content-more">더보기</a>
            </div>
            <div class="new-content new-content-nomargin">
                <h2><a href="#"><i class="fa fa-floppy-o" aria-hidden="true"></i> 회원자료실</a></h2>
                <ul>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 1 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 2 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 3 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 4 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 5 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 6 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                </ul>
                <a href="#" class="new-content-more">더보기</a>
            </div>
            <div class="new-content">
                <h2><a href="#"><i class="fa fa-newspaper-o" aria-hidden="true"></i> 그누보드 5</a></h2>
                <ul>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 1 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 2 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 3 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 4 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 5 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 6 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                </ul>
                <a href="#" class="new-content-more">더보기</a>
            </div>
            <div class="new-content new-content-nomargin">
                <h2><a href="#"><i class="fa fa-shopping-cart" aria-hidden="true"></i> 영카트 5</a></h2>
                <ul>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 1 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 2 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 3 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 4 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 5 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                    <li>
                        <a href="http://www.naver.com/">
                        <span class="new-cont-title"> 게시글 6 입니다 </span>
                        <span class="new-comment"><b>3</b></span>
                        <span class="new-date">12:05</span>
                        </a>
                    </li>
                </ul>
                <a href="#" class="new-content-more">더보기</a>
            </div>

            <div id="idx-new-gallery">
                <h2><a href="#"><i class="fa fa-picture-o" aria-hidden="true"></i> 갤러리</a></h2>
                <ul>
                    <li>
                        <a href="#none">
                        <img src="<?php echo get_template_directory_uri();?>/img/no_img.png" alt="노 이미지" /></a><br>
                        <a href="#none" class="new-title">이미지 없음</a>
                        <span class="new-comment"><b>6</b></span>
                        <br>
                        <span class="new-name">닉네임</span>
                        <span class="new-date">03:35</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none">
                        <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" /></a><br>
                        <a href="#none" class="new-title">게시글 제목</a>
                        <span class="new-comment"><b>6</b></span>
                        <br>
                        <span class="new-name">닉네임</span>
                        <span class="new-date">03:35</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none">
                        <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" /></a><br>
                        <a href="#none" class="new-title">게시글 제목</a>
                        <span class="new-comment"><b>6</b></span>
                        <br>
                        <span class="new-name">닉네임</span>
                        <span class="new-date">03:35</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none">
                        <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" /></a><br>
                        <a href="#none" class="new-title">게시글 제목</a>
                        <span class="new-comment"><b>6</b></span>
                        <br>
                        <span class="new-name">닉네임</span>
                        <span class="new-date">03:35</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none">
                        <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" /></a><br>
                        <a href="#none" class="new-title">게시글 제목</a>
                        <span class="new-comment"><b>6</b></span>
                        <br>
                        <span class="new-name">닉네임</span>
                        <span class="new-date">03:35</span>
                        </a>
                    </li>
                    <li class="new-content-nomargin">
                        <a href="#none">
                        <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" /></a><br>
                        <a href="#none" class="new-title">게시글 제목</a>
                        <span class="new-comment"><b>6</b></span>
                        <br>
                        <span class="new-name">닉네임</span>
                        <span class="new-date">03:35</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none">
                        <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" /></a><br>
                        <a href="#none" class="new-title">게시글 제목</a>
                        <span class="new-comment"><b>6</b></span>
                        <br>
                        <span class="new-name">닉네임</span>
                        <span class="new-date">03:35</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none">
                        <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" /></a><br>
                        <a href="#none" class="new-title">게시글 제목</a>
                        <span class="new-comment"><b>6</b></span>
                        <br>
                        <span class="new-name">닉네임</span>
                        <span class="new-date">03:35</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none">
                        <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" /></a><br>
                        <a href="#none" class="new-title">게시글 제목</a>
                        <span class="new-comment"><b>6</b></span>
                        <br>
                        <span class="new-name">닉네임</span>
                        <span class="new-date">03:35</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none">
                        <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" /></a><br>
                        <a href="#none" class="new-title">게시글 제목</a>
                        <span class="new-comment"><b>6</b></span>
                        <br>
                        <span class="new-name">닉네임</span>
                        <span class="new-date">03:35</span>
                        </a>
                    </li>
                    <li>
                        <a href="#none">
                        <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" /></a><br>
                        <a href="#none" class="new-title">게시글 제목</a>
                        <span class="new-comment"><b>6</b></span>
                        <br>
                        <span class="new-name">닉네임</span>
                        <span class="new-date">03:35</span>
                        </a>
                    </li>
                    <li class="new-content-nomargin">
                        <a href="#none">
                        <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" /></a><br>
                        <a href="#none" class="new-title">게시글 제목</a>
                        <span class="new-comment"><b>6</b></span>
                        <br>
                        <span class="new-name">닉네임</span>
                        <span class="new-date">03:35</span>
                        </a>
                    </li>
                   
                    <a href="#" class="new-content-more">더보기</a>
                </ul>    
            </div>
            
            <div id="idx-new-tip">
                <h2><a href="#"><i class="fa fa-smile-o" aria-hidden="true"></i> 강좌/팁</a></h2>
                <ul>
                    <li>
                        <div>
                            <a href="#none">
                                <img src="<?php echo get_template_directory_uri();?>/img/no_img.png" alt="이미지 없음" /></a><br>
                            </a>   
                        </div>
                        <div class="new-con-txt">
                            <span class="new-title"><a href="#">이미지 없음</a></span>
                            <span class="new-comment"><b>6</b></span>
                            <br>
                            <span class="new-txt"><a href="#">반갑습니다 sir입니다. 반갑습니다 sir입니다. 반갑습니다 sir입니다. 반갑습니다 sir입니다. 반갑습니다 sir입니다.</a></span>
                            <br>
                            <span class="new-name">닉네임</span>
                            <span class="new-date">03:35</span>
                        </div>
                    </li>
                    <li class="new-content-nomargin">
                        <div>
                            <a href="#none">
                                <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" />
                            </a>   
                        </div>
                        <div class="new-con-txt">
                            <span class="new-title"><a href="#">게시글 제목</a></span>
                            <span class="new-comment"><b>6</b></span>
                            <br>
                            <span class="new-txt"><a href="#">반갑습니다 sir입니다. 반갑습니다 sir입니다. 반갑습니다 sir입니다. 반갑습니다 sir입니다. 반갑습니다 sir입니다.</a></span>
                            <br>
                            <span class="new-name">닉네임</span>
                            <span class="new-date">03:35</span>
                        </div>
                    </li>
                    <li>
                        <a href="#none">
                            <div>
                            <a href="#none">
                                <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" />
                            </a>   
                        </div>
                        <div class="new-con-txt">
                            <span class="new-title"><a href="#">게시글 제목</a></span>
                            <span class="new-comment"><b>6</b></span>
                            <br>
                            <span class="new-txt"><a href="#">반갑습니다 sir입니다. 반갑습니다 sir입니다. 반갑습니다 sir입니다. 반갑습니다 sir입니다. 반갑습니다 sir입니다.</a></span>
                            <br>
                            <span class="new-name">닉네임</span>
                            <span class="new-date">03:35</span>
                        </div>
                        </a>
                    </li>
                    <li class="new-content-nomargin">
                        <div>
                            <a href="#none">
                                <img src="<?php echo get_template_directory_uri();?>/img/ex_img.png" alt="" />
                            </a>   
                        </div>
                        <div class="new-con-txt">
                            <span class="new-title"><a href="#">게시글 제목</a></span>
                            <span class="new-comment"><b>6</b></span>
                            <br>
                            <span class="new-txt"><a href="#">반갑습니다 sir입니다. 반갑습니다 sir입니다. 반갑습니다 sir입니다. 반갑습니다 sir입니다. 반갑습니다 sir입니다.</a></span>
                            <span class="new-name">닉네임</span>
                            <span class="new-date">03:35</span>
                        </div>
                    </li>
                    <a href="#" class="new-content-more">더보기</a>
                </ul>    
            </div>

        </div> <?php // end html idx-new-content ?>
    </div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
