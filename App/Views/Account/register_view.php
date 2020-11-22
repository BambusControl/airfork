<!-- Obsah -->
<div class="container-fluid">

    <div class="container-xl">

        <div class="container-fluid p-3">

            <!-- Form -->
            <form method="post">
                <div class="input-group mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Jon Doe">
                </div>

                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="email">
                </div>

                <div class="input-group mb-3">
                    <input type="text" name="email" class="form-control" placeholder="email">
                </div>

                <div class="input-group mb-3">
                    <input type="date" name="date" class="form-control" placeholder="date">
                </div>

                <div class="input-group mb-3">
                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="optradio">Male
                        </label>
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="optradio">Female
                        </label>
                    </div>
                    <div class="form-check-inline disabled">
                        <label class="form-check-label hoverable">
                            <input type="radio" class="form-check-input" name="optradio" disabled>Other
                            <span class="tooltip">This option is not availible in the country of your residence</span>
                        </label>
                    </div>
                </div>

                <div class="input-group mb-3">
                    <label class="form-check-label">How blue?</label>
                    <input min="0" max="255" type="range" class="form-control-range">
                </div>

                <div class="input-group mb-3">
                    <input type="submit" class="form-control btn btn-primary" value="submit">
                </div>
            </form>

        </div>

    </div>

</div>