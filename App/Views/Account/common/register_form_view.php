<!-- Form -->
<form method="post">

    <div class="form-row">

        <div class="col-sm-3">
            <div class="form-group mb-3">
                <label for="firstname" class="col-form-label">Meno</label>
                <input type="text" name="firstname" id="firstname" class="form-control" value="<?= empty($data['firstname']) ? "" : $data['firstname'] ?>" pattern="([A-Z]).+" title="Meno musí začínať veľkým písmenom" required>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group mb-3">
                <label for="surname" class="col-form-label">Priezvisko</label>
                <input type="text" name="lastname" id="lastname" class="form-control" value="<?= empty($data['lastname']) ? "" : $data['lastname'] ?>" pattern="([A-Z]).+" title="Priezvisko musí začínať veľkým písmenom" required>
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group mb-3 is-valid">
                <label for="email" class="col-form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control <?= empty($data['error']['email']) ? '' : 'is-invalid' ?>" placeholder="someone@here.com" aria-describedby="password-warning" value="<?= empty($data['email']) ? "" : $data['email'] ?>" required>
                <?= empty($data['error']['email']) ? '' :
                    '<small id="password-warning" class="form-text invalid-feedback">' . $data['error']['email'] . '</small>'
                ?>
            </div>
        </div>

    </div>
    <div class="form-row">

        <div class="col-md">
            <div class="form-group mb-3">
                <label for="date_of_birth" class="col-form-label">Dátum narodenia</label>
                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" aria-describedby="date-comment" max="<?=
                date_format(
                    date_sub(
                        date_create(
                            date("Y-m-d")
                        ),
                        date_interval_create_from_date_string("16 years")
                    ),
                    "Y-m-d"
                )
                ?>" value="<?= empty($data['date_of_birth']) ? '' : $data['date_of_birth'] ?>" required>
            </div>
        </div>

        <div class="col-md">
            <div class="form-group mb-3">

                <label for="gender" class="col-form-label">Pohlavie</label>
                <div class="col-form-label" id="gender">

                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="gender" value="male" <?= @$data['gender'] == 'male' ? "checked" : "" ?> required>Muž
                        </label>
                    </div>

                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="gender" value="female" <?= @$data['gender'] == 'female' ? "checked" : "" ?> required>Žena
                        </label>
                    </div>

                    <div class="form-check-inline disabled">
                        <label class="form-check-label hoverable">
                            <input type="radio" class="form-check-input" name="gender" value="other" disabled <?= @$data['gender'] == 'other' ? "checked" : "" ?> required>Iné
                            <span class="tooltip">Táto možnosť vám nie je prístupná</span>
                        </label>
                    </div>

                </div>

            </div>
        </div>

        <div class="col-sm">
            <div class="form-group mb-3">
                <label for="username" class="col-form-label">Používateľské meno</label>
                <input type="text" name="username" id="username" class="form-control <?= empty($data['error']['username']) ? '' : 'is-invalid'?>" aria-describedby="username-warning" value="<?= empty($data['username']) ? "" : $data['username'] ?>" pattern="^\S+" title="Meno nemôže obsahovať medzeru" required>
                <?= empty($data['error']['username']) ? '' : '<small id="username-warning" class="form-text invalid-feedback">' . $data['error']['username'] . '</small>' ?>
            </div>
        </div>

        <div class="col-sm">

            <div class="form-group mb-3">
                <label for="password" class="col-form-label">Heslo</label>
                <div class="input-group mb-3">
                    <?= @$data['disable_password_checkbox'] ? '' :
                        '
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <input type="checkbox" name="password-checkbox" onchange="password.disabled = !this.checked;" value="checked">
                                </div>
                            </div>
                        '
                    ?>
                    <input <?= @$data['disable_password_checkbox'] ? '' :'disabled' ?> type="password" name="password" class="form-control" pattern="^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])[^\n\r]{7,255}$" placeholder="Aspoň 7 znakov" value="<?= empty($data['password']) ? "" : $data['password'] ?>" required title="Heslo musí obsahovať aspoň 7 znakov, z toho aspoň jedno veľké písmeno, malé písmeno a číslo)">
                </div>
            </div>
        </div>

    </div>
    <div class="form-row">

        <div class="col-sm">
            <div class="form-group mb-3">
                <input type="submit" class="form-control btn btn-outline-primary" value="Submit">
            </div>
        </div>

        <div class="col-sm">
            <div class="form-group mb-3">
                <input type="reset" class="form-control btn btn-outline-dark" value="Reset">
            </div>
        </div>

    </div>

</form>
