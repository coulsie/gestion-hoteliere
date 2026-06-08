<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Hôtel Horizon - Gestion</title>

    <!-- Fonts & Icons -->
    <link href="{{ asset('template/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Styles SB Admin 2 (Bootstrap 4) -->
    <link href="{{ asset('template/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <script src="{{ asset('template/vendor/jquery/jquery.min.js') }}"></script>
</head>

<body id="page-top" class="bg-gradient-primary">

    <!-- Conteneur principal fluide pour s'adapter à SB Admin 2 -->
    <div class="container">
        @yield('content')
    </div>

    <!-- Scripts de base obligatoires pour Bootstrap 4 / SB Admin -->
    <script src="{{ asset('template/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('template/js/sb-admin-2.min.js') }}"></script>
</body>
</html>
