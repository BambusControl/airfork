<!-- My script -->
<script src="/public/scripts/ImageHandler.js"></script>
<script src="/public/scripts/UserHandler.js"></script>
<script src="/public/scripts/PostHandler.js"></script>
<script src="/public/scripts/VoteHandler.js"></script>
<script src="/public/scripts/script.js"></script>

<!-- Locator -->
<div class="container-fluid p-1 mb-3 border" id="locator">
    <div class="container-fluid">
        <h1 class="display-4 text-center">Vitajte na stránke slovenských fanúšikov letectva!</h1>
        <p class="lead text-center container-xl">
            Na tejto stránke sa môžete dozvedieť rôzne informácie o letectve, a aj zdieľať svoje letecké zážitky alebo otázky s ostatnými fanúšikmi letectva.
        </p>
    </div>
</div>

<!-- Content -->
<div class="container-xl">

    <?php session_start(['read_and_close' => true]); ?>
    <?=
    @$_SESSION['logged_in'] ? '
                <div class="mt-3 mb-2">
                    <label class="a-switch" for="article-switch" id="article-switch-label">
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

    <!--<div class="container-xl">

    </div>-->

</div>