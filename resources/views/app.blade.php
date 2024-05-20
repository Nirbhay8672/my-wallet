<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="My Wallet">
    <meta name="author" content="My Wallet">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="url" content="{{ url('/') }}" />

    <link rel="shortcut icon" href="{{ asset('/images/favicon.png') }}" />

    @inertiaHead

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <link href="{{ asset('/css/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/bootstrap-icons/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/custom.css') }}" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>

<body>
    @inertia
    @vite('resources/js/app.js')

    <script src="{{ asset('/js/bootstrap-bundle.js') }}"></script>
    <script src="{{ asset('/js/app.js') }}"></script>
</body>

</html>
