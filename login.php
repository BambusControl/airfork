<?php ?>

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

    <!-- Môj stylesheet -->
    <link rel="stylesheet" href="stylesheet.css">

    <link rel="shortcut icon" href="visuals/favicon.ico" type="image/x-icon"/>
    <title>Letci Slovenska</title>

</head>

<body>

    <!-- Navbar -->
    <div class="container-fluid shadow-sm p-4">
        <nav class="navbar navbar-expand-sm bg-light navbar-light border rounded">

            <div class="container-md p-2 border-left border-right">
                <img class="navbar-brand ml-3 logo" src="visuals/logo_airplane.svg" alt="logo">

                <div class="collapse navbar-collapse" id="navbar-collapse">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="index.html">Novinky</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="airplanes.html">Lietadlá</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="airfields.html">Letiská</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle active" href="#" id="navbardrop" data-toggle="dropdown">Users</a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item active" href="login.php">Login</a>
                                <a class="dropdown-item" href="register.php">Register</a>
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

    <!-- Obsah -->
    <div class="container-fluid">

        <div class="container-xl">

            <div class="container-fluid p-3">

                <!-- Form -->
                <form method="post">
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="email">
                    </div>

                    <div class="input-group mb-3">
                        <input type="text" name="password" class="form-control" placeholder="password">
                    </div>

                    <div class="input-group mb-3">
                        <input type="submit" class="form-control btn btn-primary" value="submit">
                    </div>
                </form>

                <?php if (isset($_POST['email'])) { ?>
                    <div class="container-fluid p-3">
                        <p>Email: <?php echo $_POST['email']?></p>
                        <p>Password: <?php echo $_POST['password']?></p>
                    </div>
                <?php } ?>

            </div>

        </div>

    </div>

</body>

</html>