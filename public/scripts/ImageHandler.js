class ImageHandler
{
    imagePath;

    constructor()
    {
        this.imagePath = [];
    }

    async loadAll() {
        fetch("?c=content&a=get_images").then(
            j => j.json().then(
                imgs => imgs.forEach(
                    img => {
                        if (this.imagePath[img.id] == null) {
                            this.imagePath[img.id] = img.path;
                        }
                    }
                )
            )
        );
    }

    getImg(id) {
        if (id == null) {
            return null;
        }

        let img = document.createElement("img");
        img.className = "card-img-top";
        img.style.boxShadow = "inset 0 0 32px RGBA(0, 0, 0, 0.2)";

        if (this.imagePath[id] != null) {
            // console.log("Cached image : " + this.imagePath[id]);
            img.src = this.imagePath[id];
        } else {
            fetch("?c=content&a=get_image&id=" + id).then(
                doc => {
                    doc.json().then(
                        image => {
                            img.src = image.path;
                            this.imagePath[id] = image.path;
                        }
                    )
                }
            );
        }

        return img;
    }
}
