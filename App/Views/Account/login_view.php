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
                    <input type="password" name="password" class="form-control" placeholder="password">
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