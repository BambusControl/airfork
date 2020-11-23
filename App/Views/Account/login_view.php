<!-- Obsah -->
<div class="container-fluid">

    <div class="container-xl">

        <div class="m-auto p-3">

            <div class="jumbotron border shadow-sm m-4">

                <h1 class="display-4">Prihlásenie</h1>

                <!-- Form -->
                <form method="post">

                    <div class="form-row">
                        <div class="col-sm">
                            <div class="form-group mb-3">
                                <label for="username" class="col-form-label">Používateľské meno</label>
                                <input type="text" name="username" id="username" class="form-control" value="<?= empty($data['username']) ? "" : $data['username'] ?>" placeholder="username" pattern="^\S+" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-sm">
                            <div class="form-group mb-3">
                                <label for="password" class="col-form-label">Heslo</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="password" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-sm">
                            <div class="form-group mb-3">
                                <input type="submit" class="form-control btn btn-primary" value="submit">
                            </div>
                        </div>
                    </div>

                </form>

            </div>

        </div>

    </div>

</div>