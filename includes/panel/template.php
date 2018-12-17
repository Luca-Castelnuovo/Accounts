<?php

function page_header($title = 'Unknown')
{
    echo <<<HTML
    <!DOCTYPE html>
    <html>

    <head>
        <title>{$title}</title>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

        <link rel="shortcut icon" href="/images/favicon.ico">
        <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
        <link rel="mask-icon" href="/images/safari-pinned-tab.svg" color="#2962ff">
        <link rel="manifest" href="/site.webmanifest">
    </head>

    <body>
        <nav>
            <div class="nav-wrapper blue accent-4">
                <a href="/home" class="brand-logo" style="padding-left: 15px">{$title}</a>
                <a href="#" data-target="sidenav" class="right sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul class="right hide-on-med-and-down">
                    <li><a href="/user/settings">Settings</a></li>
                    <li><a href="/?logout">Logout</a></li>
                </ul>
            </div>

            <ul class="sidenav" id="sidenav">
                <li><a href="/home">Home</a></li>
                <li><a href="/user/settings">Settings</a></li>
                <li class="divider"></li>
                <li><a href="/?logout">Logout</a></li>
            </ul>
        </nav>
        <div class="section">
            <div class="container">
HTML;
}

function page_footer()
{
    echo <<<HTML
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
        <script src="/js/init.js"></script>
HTML;
    alert_display();
    echo <<<HTML
    </body>

    </html>
HTML;
}
