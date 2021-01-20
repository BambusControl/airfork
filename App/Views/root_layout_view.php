<?php /** @var array $data */ ?>
<?php /** @var string $contentHTML */ ?>
<?php
session_start(['read_and_close' => true]);
?>


<!DOCTYPE html>
<html lang="sk">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Font Awesome icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">

    <!-- My stylesheet -->
    <link rel="stylesheet" href="../../public/stylesheet.css">

    <!-- My script -->
    <script src="/public/scripts/ImagePath.js"></script>
    <script src="/public/scripts/PostHandler.js"></script>
    <script src="/public/scripts/VoteHandler.js"></script>
    <script src="/public/scripts/script.js"></script>


    <!-- Tab title and logo -->
    <link rel="shortcut icon" href="../../public/visuals/favicon.ico" type="image/x-icon"/>
    <title>Letci Slovenska</title>

</head>

<body>

    <!-- Navbar -->
    <div class="container-fluid shadow-sm p-4">
        <nav class="navbar navbar-expand-sm bg-light navbar-light border rounded">

            <div class="container-md p-2 border-left border-right">
                <img class="navbar-brand ml-3 logo" <?= @$_GET['a'] == '' ? 'id="landing-page"' : '' ?> src="../../public/visuals/logo_airplane.svg" alt="logo">

                <div class="collapse navbar-collapse" id="navbar-collapse">
                    <ul class="navbar-nav">

                        <li class="nav-item">
                            <a class="nav-link <?= @$_GET['a'] == 'index' ? 'active' : '' ?>" href="?c=home">Príspevky</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= @$_GET['a'] == 'airplanes' ? 'active' : '' ?>" href="?c=home&a=airplanes">Lietadlá</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?= @$_GET['a'] == 'airfields' ? 'active' : '' ?>" href="?c=home&a=airfields">Letiská</a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?= @$_GET['c'] == 'account' ? 'active' : '' ?>" href="#" id="navbardrop" data-toggle="dropdown">
                                <?= @$_SESSION['logged_in'] ? $_SESSION['username'] : 'Účet' ?>
                            </a>
                            <div class="dropdown-menu">
                                <?=
                                    @$_SESSION['logged_in'] ?
                                        '
                                            <a class="dropdown-item" href="?c=account&a=profile">Profilové údaje</a>
                                            <a class="dropdown-item" href="?c=account&a=logout">Odhlásiť sa</a>
                                            <a class="dropdown-item" href="?c=home&a=add_article">Pridať príspevok</a>
                                        '
                                        :
                                        '
                                            <a class="dropdown-item " href="?c=account&a=login">Prihlásenie</a>
                                            <a class="dropdown-item " href="?c=account&a=register">Registrácia</a>
                                        '
                                ?>
                            </div>
                        </li>

                    </ul>
                </div>

                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

        </nav>
    </div>

    <!-- Content -->
    <div class="web-content">
        <?= $contentHTML ?>
    </div>

</body>

</html>
