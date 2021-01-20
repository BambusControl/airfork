
<div class="container-xl">

    <div class="jumbotron border shadow-sm mt-4 ml-0 mr-0">

        <h1 class="display-4">Pridať príspevok</h1>
        <p class="lead">Vyplňte informácie príspevku</p>

        <hr class="my-4">

        <form method="post" enctype="multipart/form-data">

            <?= // TODO user admins and not
                @$_SESSION['uid'] == 1 ? '
                        <div class="form-group custom-control custom-switch">
                            <label class="custom-control-label" for="article_switch">Vytvoriť novinový článok</label>
                            <input type="checkbox" class="custom-control-input" name="article_switch" value="true" id="article_switch">
                        </div>
                    ' : ''
            ?>

            <div class="form-group">
                <label for="title">Nadpis:</label>
                <input type="text" class="form-control form-control-lg <?= empty($data['error']['title']) ? '' : 'is-invalid' ?>" id="title" name="title" aria-describedby="title-warning" value="<?= empty($data['title']) ? "" : $data['title'] ?>" required>
                <?= empty($data['error']['title']) ? '' :
                    '<small id="title-warning" class="form-text invalid-feedback">' . $data['error']['title'] . '</small>'
                ?>
            </div>

            <div class="form-group">
                <label for="text">Text:</label>
                <textarea class="form-control <?= empty($data['error']['text']) ? '' : 'is-invalid' ?>" rows="5" id="text" name="text" aria-describedby="title-warning" required><?= empty($data['text']) ? "" : $data['text'] ?></textarea>
                <?= empty($data['error']['text']) ? '' :
                    '<small id="text-warning" class="form-text invalid-feedback">' . $data['error']['text'] . '</small>'
                ?>
            </div>

            <div class="form-group">
                <label for="image">Obrázok:</label>
                <input type="file" class="image-input btn p-4 border<?= empty($data['error']['image']) ? '' : 'is-invalid' ?>" name="image" id="image" aria-describedby="image-warning" accept="image/*">
                <?= empty($data['error']['image']) ? '' :
                    '<small id="image-warning" class="form-text invalid-feedback">' . $data['error']['image'] . '</small>'
                ?>
            </div>

            <div class="form-group mt-3 mb-3">
                <input type="submit" class="form-control btn btn-outline-primary" id="submit" name="submit" value="Submit">
            </div>

        </form>

    </div>

</div>