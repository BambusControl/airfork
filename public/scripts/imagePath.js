class ImagePath
{
    imagePath;
    imageAlt

    constructor()
    {
        this.imagePath = [];
        this.imageAlt = [];
        this.loadImages = this.loadImages.bind(this);
    }

    async loadImages()
    {
        try {
            let data = await ( await fetch("?c=home&a=images") ).json();
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

    getById(id)
    {
        return {"path" : this.imagePath[id], "alt" : this.imageAlt[id]} ;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    let imagePath = new ImagePath();
});