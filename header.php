<?php ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?PHP echo $page_title . " - " . $project_name ?>">
        <title><?PHP echo $page_desc . " - " . $project_name ?></title>

        <!-- Theme CSS -->
        <link href="/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link href="/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
        <link href="/bower_components/startbootstrap-sb-admin-2/dist/css/sb-admin-2.css" rel="stylesheet">

        <!-- Project Base CSS -->
        <link href="/css/base.css" rel="stylesheet">

        <!-- Report Page CSS -->
        <link href="/css/report.css" rel="stylesheet">
        <?php if (isset($_GET['pdf']) && !empty($_GET['pdf'])) { ?>
            <link href="/css/reportpdf.css" rel="stylesheet">
        <?php } ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>

        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php"><img src="/img/logo-30px.png" width="30" height="30" style="display: inline;" alt="<?PHP echo $project_name ?>" /> <?PHP echo $project_name ?></a>
                </div>
                <!-- /.navbar-header -->

                <ul class="nav navbar-top-links navbar-right">

                    <!-- /.dropdown -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i> <?PHP echo (!empty($erapi_accountinfo) && isset($erapi_accountinfo->email)) ? $erapi_accountinfo->email : "Unknown User" ?> <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li>
                                <a  href="/index.php?p=accountinfo" <?PHP echo (strcmp('accountinfo', $page) === 0) ? 'class="active"' : '' ?>><i class="fa fa-user fa-fw"></i> Settings</a>
                            </li>     
<!--                            <li class="divider"></li>-->
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    <!-- /.dropdown -->
                </ul>
                <!-- /.navbar-top-links -->

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">

                            <li>
                                <a href="/index.php?p=home" <?PHP echo (strcmp('home', $page) === 0) ? 'class="active"' : '' ?>><i class="fa fa-home fa-fw"></i> Home</a>
                            </li>
                            <li>
                                <a href="/index.php?p=createreport" <?PHP echo (strcmp('createreport', $page) === 0) ? 'class="active"' : '' ?>><i class="fa fa-plus-circle fa-fw"></i> Create Report</a>
                            </li>
                            <li>
                                <a href="/index.php?p=reports" <?PHP echo (strcmp('reports', $page) === 0) ? 'class="active"' : '' ?>><i class="fa fa-file fa-fw"></i> Latest Reports</a>
                            </li>
                            <li>
                                <a href="/index.php?p=accountinfo" <?PHP echo (strcmp('accountinfo', $page) === 0) ? 'class="active"' : '' ?>><i class="fa fa-user fa-fw"></i> Account Info</a>
                            </li>

                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>

            <div id="page-wrapper">
                <?PHP if (!empty($page_title)) { ?>                    
                    <div class="row">
                        <div class="col-lg-12">
                            <h1 class="page-header"><?PHP echo $page_title ?></h1>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                <?PHP } else { ?>
                    <br /> 
                <?PHP } ?>