<?php
    session_start(['read_and_close' => true]);
?>

<!-- Obsah -->
<div class="container-fluid">
    <div class="container-xl">
        <div class="jumbotron border shadow-sm mt-4 ml-0 mr-0">

            <h1 class="display-4">Zmena údajov</h1>
            <p class="text-muted">Tu si môžete upraviť svoje profilové údaje.</p>

            <hr class="my-4">

            <h4 class="font-weight-normal mb-4 mt-2">Profilové údaje</h4>
            <?php include "common/register_form_view.php" ?>
            <small class="text-muted">Pre zmenu hesla označte štvorček</small>

            <hr class="my-4">

            <h4 class="font-weight-normal mb-4 mt-2">Pokročilé</h4>
            <div class="form-row">
                <div class="form-group">
                    <a class="btn btn-outline-danger" href="?c=account&a=delete_account">Vymazať účet</a>
                    <small class="text-muted p-2">Vymazanie účtu je nenávratná operácia!</small>

                </div>
            </div>

        </div>
    </div>

</div>