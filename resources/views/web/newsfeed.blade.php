<html>
<head>
    <meta charset="utf-8">
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="max-age=60, public">
        <title>What's new on  Go2Top Panel</title>
        <meta name="description" content="Just Another Panel. Changelog and newsfeed.">
        <meta name="keywords" content="What's, Beamer, changelog, newsfeed, feed, news, roadmap">
        <meta name="referrer" content="always">

        <link href="{{ asset('notify-assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('notify-assets/css/style.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('notify-assets/css/theme.css') }}" media="all" rel="stylesheet" type="text/css">

        <style type="text/css">
            .ps.ps--active-y>.ps__scrollbar-y-rail{
                display:none !important;
                background-color:transparent;
            }
            .featureTitle a, .featureAction a, .featureContent a {
                color:  !important;
            }
            body:not(.inapp) .header, body:not(.inapp) .news .cover {
                background-color: #041930 !important;
            }
            .headerTitle, .headerTitle a, .headerMention a, .headerSubtitle {
                color: #ffffff !important;
            }
            .headerClose svg, .headerNav svg {
                fill: #ffffff !important;
            }
            .headerTitle, .headerSubtitle, .featureTitle {
                font-size: 20px !important;
            }
            .feature, .feedbackSubmited {
                color: #424242 !important;
            }
            .feature {
                background: #ffffff !important;
            }
            .feature.hasReadmore .featureContent:after {
                background: transparent;
                background: -webkit-gradient(linear, left top, left bottom, from(transparent), to(#ffffff));
                background: -webkit-linear-gradient(top, transparent, #ffffff);
                background: -moz-linear-gradient(top, transparent, #ffffff);
                background: -o-linear-gradient(top, transparent, #ffffff);
                background: linear-gradient(top, transparent, #ffffff);
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ffffff', GradientType=0);
                -ms-filter: "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ffffff', GradientType=0);";
            }
            .news, .poweredFooter {
                background-color: #f7f7f7 !important;
            }
            .featureTitle a, .featureAction a {
                color:  !important;
            }
            .featureAction svg {
                fill:  !important;
            }
            .backButton {
                color:  !important;
            }
            .backButtonIcon {
                fill:  !important;
            }
            .featureDate .date, .featureDate span {
                color: #8da2b5 !important;
            }
            .category.categoryNew, .catItem .ico.new {
                background: #3ec25f !important;
            }
            .category.categoryImprovement, .catItem .ico.improvement {
                background: #71c4ff !important;
            }
            .category.categoryFix, .catItem .ico.fix {
                background: #fa4b4b !important;
            }
            .category.categoryComingSoon, .catItem .ico.comingsoon {
                background: #59d457 !important;
            }
            .category.categoryAnnouncement, .catItem .ico.announcement {
                background: #ffae1b !important;
            }
            .featureContent, .featureAction {
                font-size: 16px !important;
            }
            .featureAction svg {
                width: 16px !important;
                height: 16px !important;
            }
            body.inapp .header, body.inapp .news .cover {
                background-color: #f05326 !important;
            }
        </style>
        </head>

<body class="embed beamer_active win">
<div>
    <div class="header dark solid">
        <div class="headerTitle">
            <a href="<?php echo base_url();?>" target="_blank">What's new on Go2Top Panel</a>
        </div>

        <div class="headerNav" onclick="BeamerEmbed.showSearch();">
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="26px" height="26px" viewBox="0 0 26 26" enable-background="new 0 0 26 26" xml:space="preserve">
                    <path d="M23.849,22.147l-5.218-5.217c-0.097-0.099-0.225-0.15-0.365-0.15H17.7c1.354-1.568,2.174-3.609,2.174-5.844 C19.874,6,15.874,2,10.937,2C6,2,2,6,2,10.937c0,4.938,4,8.938,8.937,8.938c2.234,0,4.275-0.82,5.844-2.174v0.565 c0,0.141,0.058,0.269,0.15,0.365l5.217,5.218c0.2,0.201,0.527,0.201,0.73,0l0.971-0.971C24.05,22.675,24.05,22.348,23.849,22.147z M10.937,17.812c-3.798,0-6.875-3.076-6.875-6.875c0-3.798,3.077-6.875,6.875-6.875c3.799,0,6.875,3.077,6.875,6.875 C17.812,14.735,14.735,17.812,10.937,17.812z"></path>
                </svg>
        </div>

        <div id="header_Close" attribute='text' class="headerClose" onclick="close();">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                <path d="M0 0h24v24H0z" fill="none" />
            </svg>
        </div>
    </div>

    <div class="streamSearch">
        <div class="streamSearchBack" onclick="clearSearch();">
            <a href="<?php echo base_url();?>">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="26px" height="26px" viewBox="0 0 26 26" enable-background="new 0 0 26 26" xml:space="preserve">
                        <path d="M7.756,1.935L6.75,2.94c-0.238,0.238-0.238,0.624,0,0.861L15.927,13L6.75,22.198 c-0.238,0.237-0.238,0.624,0,0.861l1.006,1.006c0.238,0.237,0.624,0.237,0.861,0l10.635-10.634c0.237-0.238,0.237-0.625,0-0.862 L8.617,1.935C8.379,1.697,7.994,1.697,7.756,1.935z"></path>
                    </svg>
            </a>
        </div>

        <input type="text" class="streamSearchInput" onfocus="BeamerEmbed.showSearch();" placeholder="Search in this feed">

        <div class="streamSearchIcon">
            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="26px" height="26px" viewBox="0 0 26 26" enable-background="new 0 0 26 26" xml:space="preserve">
                    <path d="M23.849,22.147l-5.218-5.217c-0.097-0.099-0.225-0.15-0.365-0.15H17.7c1.354-1.568,2.174-3.609,2.174-5.844 C19.874,6,15.874,2,10.937,2C6,2,2,6,2,10.937c0,4.938,4,8.938,8.937,8.938c2.234,0,4.275-0.82,5.844-2.174v0.565 c0,0.141,0.058,0.269,0.15,0.365l5.217,5.218c0.2,0.201,0.527,0.201,0.73,0l0.971-0.971C24.05,22.675,24.05,22.348,23.849,22.147z M10.937,17.812c-3.798,0-6.875-3.076-6.875-6.875c0-3.798,3.077-6.875,6.875-6.875c3.799,0,6.875,3.077,6.875,6.875 C17.812,14.735,14.735,17.812,10.937,17.812z"></path>
                </svg>

            <svg onclick="clearSearchInput();" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path>
                <path d="M0 0h24v24H0z" fill="none"></path>
            </svg>
        </div>
    </div>

    <div class="panel" id="panel-nav">
        <nav class="nav streamNav">
            <div class="streamCats singleCategory">
                <ul class="streamCatsList">
                    <li class="catItem">
                        <a class="catItemLink" href="<?php echo base_url('/');?>"><i class="ico" style="background:#0bb6b4 !important;"></i> <span class="catItemName">All</span></a>
                    </li>
                    <?php
                    if(!empty($categoryMenu)){
                    foreach ($categoryMenu as $menu) {
                    $class = ($menu->categoryId!='')?'class="catItem"':'';
                    if ($menu->home_link==1) {
                        $navLink = base_url();
                    } elseif ($menu->disable_link==1) {
                        $navLink = '#';
                    } else {
                        $navLink = base_url('category/'.$menu->category_id.'/'.niceUrl($menu->category_name));
                    }
                    ?>

                    <li class="catItem">
                        <a class="catItemLink" href="<?php echo $navLink;?>"><i class="ico" style="background:<?php echo $menu->category_color;?> !important;"></i> <span class="catItemName"><?php echo $menu->category_name?></span></a>
                        <?php
                        if ($menu->categoryId!='') {
                            $menuIdArr = explode('|',$menu->categoryId);
                            $menuNameArr = explode('|',$menu->categoryName);
                            $sortArr = explode('|',$menu->sort);

                            echo '<ul class="sub-menu">';
                            asort($sortArr);
                            foreach ($sortArr as $mk => $mn) {
                                echo '<li class="catItem">
                                            <a class="catItemLink" href="'.base_url('category/'.$menuIdArr[$mk].'/'.niceUrl($menuNameArr[$mk])).'"><i class="ico" style="background: <?php echo $menu->category_color;?> !important;"></i> <span class="catItemName">'.$menuNameArr[$mk].'</span></a>
                                        </li>';
                            }
                            echo '</ul>';
                        }
                        ?>
                    </li>
                    <?php
                    }
                    }
                    ?>

                    <li class="catItem watermark">
                        <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 20 20" enable-background="new 0 0 20 20" xml:space="preserve"><g><g><path d="M2.012,17.198c-0.537,0-1.047-0.316-1.265-0.841c-0.287-0.693,0.046-1.486,0.744-1.772L17.47,8.05 c0.698-0.285,1.498,0.045,1.785,0.739c0.287,0.694-0.046,1.488-0.743,1.773L2.532,17.097C2.362,17.166,2.186,17.198,2.012,17.198z" /></g><g><path d="M2.012,12.021c-0.537,0-1.047-0.316-1.265-0.841C0.46,10.487,0.793,9.693,1.491,9.408L17.47,2.873 c0.698-0.286,1.498,0.045,1.785,0.738c0.287,0.694-0.046,1.487-0.743,1.773L2.532,11.919C2.362,11.989,2.186,12.021,2.012,12.021z" /></g><g><path d="M2.012,6.675c-0.54,0-1.051-0.319-1.267-0.847C0.46,5.133,0.797,4.341,1.497,4.059L11.299,0.1 c0.699-0.283,1.497,0.051,1.781,0.747c0.284,0.694-0.052,1.487-0.752,1.769L2.526,6.575C2.358,6.643,2.184,6.675,2.012,6.675z" /></g><g><path d="M8.165,20c-0.54,0-1.051-0.32-1.267-0.848c-0.285-0.694,0.052-1.486,0.751-1.77l9.802-3.958 c0.699-0.282,1.497,0.052,1.781,0.746s-0.052,1.487-0.751,1.77l-9.803,3.959C8.51,19.967,8.336,20,8.165,20z" /></g></g></svg>
                        Go2Top Panel feed
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <div class="news">
        <div id="post-data">
            <div id="load_data">
                foreach ($posts as $post)
                <div data-description-id="217001 " data-post-title=" " data-redirect-links="false " data-links-in-new-window="true " class="feature improvement november unread hasShare " id="feature211799 " data-initialized="true ">
                    <div class="featureDate ">
                        <div class="category categoryImprovement improvement " style="background-color: '.$post->category_color.'!important;">
                            '.$post->category_name.'
                        </div>
                        <span>'.dateFormat($post->created_at).'</span>
                    </div>

                    <div class="featureControls ">
                        <div class="social featureControl " data-action="share ">
                            <div class="socialShare " onclick="BeamerEmbed.showSocialMenu(this) " title="Share "></div>
                            <div class="socialOverlay "></div>
                            <div class="socialList ">
                                <ul>
                                    <li><a target="_blank " href="https://www.facebook.com/dialog/share?app_id=213351392791601&amp;display=page&amp;href=https%3A%2F%2Fnews.justanotherpanel.com%2Fen%2F211799-217001 " class="shareLink redirected " data-social="facebook ">Facebook</a></li>
                                    <li><a target="_blank " href="https://twitter.com/intent/tweet?via=getbeamer&amp;text=&amp;url=https%3A%2F%2Fnews.justanotherpanel.com%2Fen%2F211799-217001 " class="shareLink redirected " data-social="twitter ">Twitter</a></li>
                                    <li><a target="_blank " href="https://www.linkedin.com/shareArticle?source=Beamer&amp;mini=true&amp;title=&amp;summary=OUR IG FOLLOWERS REAL PACKAGES are working SUPER FINE ! WE also enabled a new lower package 1919 - Real Instagram Followers [10k to 25k] [149$ Price] [ADS] Waiting for your Orders ! - JAP Team !&amp;url=https%3A%2F%2Fnews.justanotherpanel.com%2Fen%2F211799-217001 " class="shareLink redirected " data-social="linkedin ">LinkedIn</a></li>
                                    <li>
                                        <div onclick="BeamerEmbed.copyLink(this) " class="shareLink " data-social="copy ">
                                    <span class="copyLink ">
                                    Copy link
                                    </span>
                                            <span class="copiedLink " style="display: none; ">
                                    Copy link
                                    <i></i>
                                    </span>
                                            <textarea>https://news.justanotherpanel.com/en/211799-217001</textarea>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <h3 class="featureTitle ">
                        <a href="https://news.justanotherpanel.com/en/211799-217001 " class="redirected "></a>
                    </h3>

                    <div class="featureContent ">
                        <h2><b>'.$post->news_heading.'</b></h2>
                        '.$post->news_details .'
                    </div>';
                    echo '<div id="feedback211799 " class="featureFeedback ">
                        <div id="reactions " class="emojis ">';
                            echo '<span class="emojiContainer ">';
                echo '<img src="'.base_url().'assets/images/emojiNeg.svg " id="negativeemoji211799 " class="emoji " style="padding: 2px; " alt="emoji negtive reaction " title="Negative ">';
                echo '</span>
                            <span class="emojiContainer ">';
                echo '<img src="'.base_url().'assets/images/emojiNeut.svg " id="neutralemoji211799 " class="emoji " style="padding-bottom: 0px; " alt="emoji neutral reaction " title="Neutral ">';
                echo '</span>
                            <span class="emojiContainer ">';
                    echo '<img src="'.base_url().'assets/images/emojiPos.svg" id="positiveemoji211799 " class="emoji " style="padding-bottom: 0px; " alt="emoji positive reaction " title="Positive ">';
                    echo '</span></div></div></div>';
                }
            </div>
        </div>
    </div>
</div>

<script src=" {{ asset('notify-assets/js/jquery-3.3.1.min.js') }}"></script>
<script src="{{ asset('notify-assets/js/tether.min.js') }}"></script>
<script src="{{ asset('notify-assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('notify-assets/js/jquery.cookie.min.js') }}"></script>
<script src="{{ asset('notify-assets/js/scroll.jquery.js') }}"></script>
<script src="{{ asset('notify-assets/js/script.min.js') }}"></script>
<script src="{{ asset('notify-assets/js/beamer.js') }}"></script>
</body>
</html>
