
<div class="container-fluid">

    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title">
        </div>

        <div class="form-group">
            <label for="text">Text:</label>
            <textarea class="form-control" rows="5" id="text" name="text"></textarea>
        </div>

        <div class="form-group">
            <label for="image">Select image:</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <div class="form-group">
            <label for="image-alt">Alernative text for image:</label>
            <input type="text" class="form-control" id="image-alt" name="image-alt">
        </div>

        <div class="form-group mt-3 mb-3">
            <input type="submit" class="form-control btn btn-outline-primary" id="submit" name="submit" value="Submit">
        </div>
    </form>

</div>