<?php
session_start(['read_and_close' => true]);
?>

<!-- Obsah -->
<div class="container-fluid">

    <div class="container-xl">

        <div class="jumbotron border border mt-4 ml-0 mr-0">
            <h1 class="display-4">Profilové údaje</h1>
            <p class="lead">Tu si môžete upraviť svoje osobné údaje.</p>
            <hr class="my-4">
            <?php include "common/register_form_view.php" ?>
            <hr class="my-4">
            <div class="form-group">
                <a class="btn btn-outline-danger">Vymazať účet</a>
                <small class="text-muted p-2">Vymazanie účtu je nenávratná operácia!</small>
            </div>
        </div>

    </div>

</div>