<?php /** @var array $data */ ?>

<!-- Obsah -->
<div class="container-fluid">
    <div class="container-xl">
        <div class="jumbotron border shadow-sm mt-4 ml-0 mr-0">

            <h1 class="display-4">Vymazanie účtu</h1>
            <p class="lead">Naozaj si prajete vymazať svoj účet?</p>

            <hr class="my-4">

            <p>Po stlačení tlačidla vymazať sa už nebudete môcť prihlásiť. Váš profil bude vymazaný aj s vašimi dátami. Po vymazaní účtu si budete môcť znova vytvoriť nový účet.</p>

            <form class="pt-2 pb-4 mt-2 rounded-lg shadow-sm border bg-light" method="post">
                <div class="form-row mt-2">
                    <label class="col-form-label ml-4 mr-4" for="password">Zadajte vaše heslo:</label>
                </div>

                <div class="form-row ml-2 mr-2">
                    <div class="col-sm-6">
                        <div class="m-2">
                            <input type="password" name="password" id="password" class="form-control <?= empty($data['error_password']) ? '' : 'is-invalid'?>" placeholder="password" aria-describedby="password-warning" required>
                            <?= empty($data['error_password']) ? '' : '<small id="password-warning" class="form-text invalid-feedback">' . $data['error_password'] . '</small>' ?>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="m-2">
                            <input type="submit" class="form-control btn btn-outline-danger" value="Vymazať">
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="m-2">
                            <a class="form-control btn btn-outline-info" href="?c=account&a=edit_profile">Zrušiť</a>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>

</div>
