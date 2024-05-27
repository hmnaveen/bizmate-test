<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SUMBv2 Template">
    <meta name="author" content="Dhon Collera">
    <meta name="keywords" content="SUMBv2 Template">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title Page-->
    <title>[B]izMate | @if (!empty($pagetitle)) {{ $pagetitle }} @else Dashboard @endif </title>

    <link rel="icon" type="image/x-icon" href="/img/sumb-icon2.png">

    <!-- Fontfaces CSS-->
    <link href="/css/font-face.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <link href="/vendor/font-awesome-4.7/css/font-awesome.min.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <link href="/vendor/font-awesome-5/css/fontawesome-all.min.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <link href="/vendor/font-awesome-6/css/all.min.css?v={{ config('app.version') }}" rel="stylesheet" media="all">

    <link href="/vendor/mdi-font/css/material-design-iconic-font.min.css?v={{ config('app.version') }}" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="/vendor/bootstrap-4.1/bootstrap.min.css?dcc=3&v=1" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="/vendor/animsition/animsition.min.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <link href="/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <link href="/vendor/wow/animate.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <link href="/vendor/css-hamburgers/hamburgers.min.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <link href="/vendor/slick/slick.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <link href="/vendor/select2/select2.min.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <link href="/vendor/perfect-scrollbar/perfect-scrollbar.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <link href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css" rel="stylesheet">

    <!-- FullCalendar -->
    <link href="/vendor/fullcalendar-3.10.0/fullcalendar.css?v={{ config('app.version') }}" rel='stylesheet' media="all" />

    <!-- Main CSS-->
    <link href="/css/theme.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <link href="/css/sumb.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <link href="/css/system.css?v={{ config('app.version') }}" rel="stylesheet" media="all">


    <!--- Invoice JS----->
    <script src="/vendor/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="/js/invoice.js?v={{ config('app.version') }}"></script>

    <!--- Basic Invoice JS----->
    <script type="text/javascript" src="/js/basic_invoice.js?v={{ config('app.version') }}"></script>

    <!--- Reconciliation JS----->
    <script type="text/javascript" src="/js/reconciliation.js?v={{ config('app.version') }}"></script>

    <!-- Docfiles CSS-->
    <link href="/css/docfiles.css?v={{ config('app.version') }}" rel="stylesheet" media="all">
    <style>
    </style>
</head>

<body class="<?php if(strpos($_SERVER['REQUEST_URI'], "acct")) {echo 'sumb--accountant';} ?>" >
<div id="pre-loader" class="">
    <div class="pre-container">
        <span class="loader" style="display:block;"></span>
    </div>
</div>
<div id="thispage">



