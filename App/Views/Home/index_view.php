<!-- Locator -->
<div class="container-fluid p-1 mb-2 border" id="locator">
    <div class="container-fluid">
        <h1 class="display-4 text-center">Vitajte na stránke slovenských fanúšikov letectva!</h1>
        <p class="lead text-center">
            Na tejto stránke sa môžete dozvedieť rôzne informácie o letectve, a aj zdieľať svoje letecké zážitky alebo otázky s ostatnými fanúšikmi letectva.
        </p>
    </div>
</div>

<!-- Content -->
<div class="container-fluid">

    <div class="container-xl">
        <?php session_start(['read_and_close' => true]); ?>
        <?=
            @$_SESSION['logged_in'] ? '
                <div>
                    <label class="switch" for="article-switch" id="article-switch-label">
                        <input type="checkbox" id="article-switch">
                        <span class="slider"></span>
                        <span id="first">Oficálne novinky</span>
                        <span id="second">Používateľské príspevky</span>
                    </label>
                </div>
            ' : ''
        ?>

        <div class="post" id="post-container" title="article">

        </div>
    </div>

</div>