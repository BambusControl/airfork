<!-- Obsah -->
<div class="container-fluid">
    <div class="container-xl">
        <div class="jumbotron border shadow-sm mt-4 ml-0 mr-0">

            <h1 class="display-4">Prihlásenie</h1>
            <p class="lead">Zadajte svoje prihlasovacie údaje</p>

            <hr class="my-4">

            <!-- Form -->
            <form method="post">

                <div class="form-row">
                    <div class="col-sm">
                        <div class="form-group mb-3">
                            <label for="username" class="col-form-label">Používateľské meno</label>
                            <input type="text" name="username" id="username" class="form-control <?= empty($data['error_credentials']) ? '' : 'is-invalid'?>" value="<?= empty($data['username']) ? "" : $data['username'] ?>" placeholder="username" pattern="^\S+" required>
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="form-group mb-3">
                            <label for="password" class="col-form-label">Heslo</label>
                            <input type="password" name="password" id="password" class="form-control <?= empty($data['error_credentials']) ? '' : 'is-invalid'?>" placeholder="password" aria-describedby="credentials-warning" required>
                            <?= empty($data['error_credentials']) ? '' : '<small id="credentials-warning" class="form-text invalid-feedback">' . $data['error_credentials'] . '</small>' ?>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-sm">
                        <div class="form-group mt-3 mb-3">
                            <input type="submit" class="form-control btn btn-outline-primary" value="Submit">
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-sm">
                        <div class="form-group mt-3 mb-3">
                            <label for="registration-link">Nemáte ešte účet?</label>
                            <a class="btn-link font-weight-bold" href="?c=account&a=register">Registrujte sa</a>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>