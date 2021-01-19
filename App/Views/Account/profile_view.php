<!-- News post loader script -->
<script src="/public/scripts/imagePath.js"></script>
<script src="/public/scripts/postHandler.js"></script>

<?php /** @var array $data */ ?>

<!-- Obsah -->
<div class="container-fluid">

    <div class="container-xl">

        <div class="jumbotron border border m-4">
            <h1 class="display-4"><?= $data['firstname'] . ' ' . $data['lastname'] ?></h1>
            <?=
                $data['id'] == $_SESSION['uid'] ? '
                    <p class="lead">Toto je váš profil</p>
                    <hr class="my-4">
                    <a class="btn btn-info" href="?c=account&a=edit_profile">Editovať profil</a>
                ' : ''
            ?>
        </div>

        <div class="post" id="post-container" title="userpost" data-uid=<?= @$data['id'] ?>>

        </div>

    </div>

</div>