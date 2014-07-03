<?php

function linuxUptime() {
    $ut = strtok( exec( "cat /proc/uptime" ), "." );
    $days = sprintf( "%2d", ($ut/(3600*24)) );
    $hours = sprintf( "%2d", ( ($ut % (3600*24)) / 3600) );
    $min = sprintf( "%2d", ($ut % (3600*24) % 3600)/60  );
    $sec = sprintf( "%2d", ($ut % (3600*24) % 3600)%60  );
    return array( $days, $hours, $min, $sec );
}

$ut = linuxUptime();

$ua = get_browser();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>DNS Tools</title>
        <style type="text/css">
            body {
                padding-top: 15px;
            }
        </style>
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
        <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">

            <nav class="navbar navbar-inverse" role="navigation">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#">Tools</a>
                    </div>
                </div>
            </nav>

            <div class="col-lg-5">
                <div class="well well">
                    <p>You are on server <?php echo php_uname('n');?></p>
                    <p>Your IP address is <?php echo $_SERVER["REMOTE_ADDR"];?></p>
                    <p>Your host name is <?php echo gethostbyaddr($_SERVER["REMOTE_ADDR"]);?></p>
                    <p>Your browser is <?php echo $ua->parent; ?></p>
                    <p>Your operating system is <?php echo $ua->platform; ?></p>
                    <p>Server uptime is <?php echo "$ut[0] days, $ut[1] hours, $ut[2] minutes, $ut[3] seconds."; ?></p>
                </div>
            </div>
            <div class="col-lg-6">

                <form class="row" action="/cgi-bin/ping" method="GET" role="form">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="ip" class="form-control" placeholder="IP Address or Hostname">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">Ping</button>
                            </span>
                        </div>
                    </div>
                </form>

                <form class="row" action="/cgi-bin/whois" method="GET" role="form">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="ip" class="form-control" placeholder="IP Address or Hostname">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">Who Is</button>
                            </span>
                        </div>
                    </div>
                </form>

                <form class="row" action="/cgi-bin/traceroute" method="GET" role="form">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="ip" class="form-control" placeholder="IP Address or Hostname">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">Trace Route</button>
                            </span>
                        </div>
                    </div>
                </form>

                <form class="row" id="dnsform" action="/cgi-bin/dig" method="GET" role="form">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="ip" class="form-control" placeholder="IP Address or Hostname">
                            <input type="hidden" name="type" id="dnstype" value="ANY"/>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">DNS Lookup <span class="caret"></span></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a href="#">ANY</a></li>
                                    <li><a href="#">A</a></li>
                                    <li><a href="#">AAAA</a></li>
                                    <li><a href="#">CNAME</a></li>
                                    <li><a href="#">MX</a></li>
                                    <li><a href="#">NS</a></li>
                                    <li><a href="#">PTR</a></li>
                                    <li><a href="#">SOA</a></li>
                                    <li><a href="#">SPF</a></li>
                                    <li><a href="#">SRV</a></li>
                                    <li><a href="#">TXT</a></li>
                                    <li><a href="#">DNSSEC</a></li>
                                </ul>
                            </span>
                        </div>
                    </div>
                    <script type="text/javascript" charset="utf-8">
                        $('.dropdown-menu li').click(function(e){
                            e.preventDefault();
                            var selected = $(this).text();
                            $('#dnstype').val(selected);
                            $("#dnsform").submit();
                        });
                    </script>
                </form>

                <form class="row" action="/cgi-bin/reversedig" method="GET" role="form">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="ip" class="form-control" placeholder="IP Address or Hostname">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">Reverse DNS</button>
                            </span>
                        </div>
                    </div>
                </form>

                <form class="row" action="/cgi-bin/smtp" method="GET" role="form">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="ip" class="form-control" placeholder="IP Address or Hostname">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">TCP/SMTP Port Test</button>
                            </span>
                        </div>
                    </div>
                </form>

                <form class="row" action="/cgi-bin/heartbleed" method="GET" role="form">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" name="ip" class="form-control" placeholder="IP Address or Hostname">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">Heartbleed Test</button>
                            </span>
                        </div>
                    </div>
                </form>

            </div>
        <?php include('ga.inc') ; ?>
        </div>
        <div class="container">
            <hr>
            <p>Questions? Issues? Want a feature? Let me know at <a href="https://github.com/thedonkdonk/tools/issues">https://github.com/thedonkdonk/tools/issues</a>.</p>
        </div>
    </body>
</html>
