<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo isset($title) ? '2007 Tracker - ' . $title : '2007 Tracker' ; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="keywords" content="runescape,old school,hiscores,tracker,joshua f" />
        <?php echo isset($description) ? "<meta name=\"description\" content=\"{$description}\" />\n\t\t" : "<meta name=\"description\" content=\"2007 Tracker is a website designed to analyze and track yours and other players' levels.\" />\n\t\t" ; ?>
<meta name="author" content="Joshua F" />
        <!-- Le styles -->
        <link rel="stylesheet" type="text/css" href="<?=base_url();?>assets/css/bootstrap.spacelab.css">
        <link href="<?=base_url();?>assets/css/bootstrap-select.min.css" rel="stylesheet" />
        <style type="text/css">
            body {
            padding-top: 60px;
            padding-bottom: 40px;
            }
        </style>
        <link href="<?=base_url();?>assets/css/bootstrap-responsive.css" rel="stylesheet" />
        <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
        <script src="<?=base_url();?>assets/js/html5shiv.js"></script>
        <![endif]-->
        <script src="<?=base_url();?>assets/js/jquery.js"></script>
        <link rel="shortcut icon" href="<?=base_url();?>assets/favicon.ico" />
        <script type="text/javascript">

          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-38842941-1']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();

        </script>
    </head>
    <body>
        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="brand" href="<?=base_url();?>">2007 Tracker</a>
                    <div class="nav-collapse collapse">
                        <ul class="nav">
                            <li <?php echo $this->uri->segment(1, "") === "" || $this->uri->segment(1) === "home" ? "class=\"active\"" : null ; ?>><a href="<?=base_url();?>">Home</a></li>
                            <li class="dropdown <?php echo $this->uri->segment(1) === "top" ? "active" : null ; ?>">
                                <a href="<?=site_url('top');?>" class="dropdown-toggle" data-toggle="dropdown">Top 50 <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?=site_url('top');?>">Daily Overall Top 50</a></li>
                                    <li><a href="<?=site_url('top/history');?>">Top 50 History</a></li>
                                </ul>
                            </li>
                            <li <?php echo $this->uri->segment(1) === "records" ? "class=\"active\"" : null ; ?>><a href="<?=site_url('records');?>">Records</a></li>
                            <li <?php echo $this->uri->segment(1) === "playertrack" ? "class=\"active\"" : null ; ?>><a href="<?=site_url('playertrack');?>">Player Count</a></li>
                            <li <?php echo $this->uri->segment(1) === "signature" ? "class=\"active\"" : null ; ?>><a href="<?=site_url('signature');?>">Signatures</a></li>
                            <li><a href="http://www.2007hq.com">2007HQ</a></li>
                            <!-- <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Action</a></li>
                                    <li><a href="#">Another action</a></li>
                                    <li><a href="#">Something else here</a></li>
                                    <li class="divider"></li>
                                    <li class="nav-header">Nav header</li>
                                    <li><a href="#">Separated link</a></li>
                                    <li><a href="#">One more separated link</a></li>
                                </ul>
                            </li> -->
                        </ul>
                        <form action="<?=site_url('track');?>" method="post" class="navbar-form pull-right">
                            <div class="input-append">
                                <input class="span2" type="text" name="rsname" placeholder="Username" value="<?=$curUser;?>" />
                                <button type="submit" value="submit" class="btn"><i class="icon-search" style="margin-top:3px"></i></button>
                            </div>
                        </form>
                    </div>
                    <!--/.nav-collapse -->
                </div>
            </div>
        </div>
        <div class="container">