class ImagePath
{
    static imagePath = [];

    /*constructor()
    {
        this.imagePath = [];
        // this.imageAlt = [];
        // this.loadImages = this.loadImages.bind(this);
    }*/

    /*async loadImages()
    {
        try {
            let data = await ( await fetch("?c=home&a=get_images") ).json();
            data.forEach(
                (value) => {
                    this.imagePath[value.id] = value.path;
                    this.imageAlt[value.id] = value.alt;
                }
            )
        } catch (e) {
            console.error("Error ImagePath::loadImages() " + e.message);
        }
    }

    getSrcById(id)
    {
        return this.imagePath[id];
    }

    getImg(id) {
        let img = document.createElement("img");
        img.className = "card-img-top";
        img.src = this.getSrcById(id);
        return img;
    }*/

    static getImgAsync(id) {
        console.log(Object.keys(ImagePath.imagePath).length);
        if (id == null) {
            return null;
        }

        let img = document.createElement("img");
        img.className = "card-img-top";
        img.style.boxShadow = "inset 0 0 32px RGBA(0, 0, 0, 0.2)";

        if (this.imagePath[id] != null) {
            console.log("Cached: " + this.imagePath[id]);
            img.src = ImagePath.imagePath[id];
        } else {
            fetch("?c=home&a=get_image&id=" + id).then(
                doc => {
                    doc.json().then(
                        image => {
                            img.src = image.path;
                            ImagePath.imagePath[id] = image.path;
                        }
                    )
                }
            );
        }

        return img;
    }
}
