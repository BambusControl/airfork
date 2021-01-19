<!-- News post loader script -->
<script src="/public/scripts/imagePath.js"></script>
<script src="/public/scripts/postHandler.js"></script>

<!-- Locator -->
<div class="container-fluid p-1 mb-2 border">
    <div class="container-xl">
        <h1 class="display-3 justify-content-center">Vitajte na stránke slovenských letcov!</h1>
    </div>
</div>

<!-- Obsash -->
<div class="container-fluid">

    <div class="container-xl">

        <p class="lead">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas neque justo, pulvinar ac mauris sed, sagittis bibendum diam. Vestibulum semper odio at dolor porttitor condimentum. Ut metus lorem, accumsan a auctor vitae, commodo malesuada urna. Donec venenatis orci vehicula feugiat cursus. Vestibulum nec mauris felis. Vivamus a molestie erat. Praesent bibendum efficitur commodo. Quisque luctus aliquet dictum. Nunc cursus porta ullamcorper. Pellentesque lobortis lectus nec mi ultrices, id varius purus molestie. Etiam lobortis ipsum et nisl tristique accumsan. Nullam tincidunt sollicitudin felis, sed ornare magna mattis quis. Integer lorem sem, tempor vitae risus tempor, pulvinar faucibus nunc. Phasellus vitae augue commodo, bibendum erat ultrices, suscipit erat. Aliquam scelerisque nulla quis diam suscipit vehicula. Mauris auctor varius nulla, quis vulputate nisl.
        </p>

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

        <!--<div>
            <label class="switch" for="article-switch" id="article-switch-label">
                <input type="checkbox" id="article-switch">
                <span class="slider"></span>
                <span id="first">Oficálne novinky</span>
                <span id="second">Používateľské príspevky</span>
            </label>
        </div>-->

        <div class="post" id="post-container" title="article">

        </div>
    </div>

</div>