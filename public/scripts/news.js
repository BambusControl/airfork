class News
{
    newsContainer;
    path;

    constructor()
    {
        this.createNewsPost = this.createNewsPost.bind(this);
        this.newsContainer = document.getElementById("news-container");
        this.path = new ImagePath();
        this.getNews();
        // setInterval(this.getNews, 5000); // kazdych 5 s reloadne novinky
    }

    async getNews()
    {
        try {

            let data = await ( await fetch("?c=home&a=news") ).json();
            await this.path.loadImages();

            data.forEach(this.createNewsPost);

        } catch (e) {
            console.error("Error News::getNews() " + e.message);
        }
    }

    createNewsPost(post)
    {
        let card = document.createElement("div");
        card.setAttribute("class", "card news");

        if (post.image != null) {
            let image = this.path.getById(post.image);
            let img = document.createElement("img");
            // img.setAttribute("class", "card-img-top");
            img.setAttribute("src", image.path);
            img.setAttribute("alt",image.alt);
            card.append(img);
        }

        let cardBody = document.createElement("div");
        cardBody.setAttribute("class", "card-body");

        let header = document.createElement("h3");
        header.setAttribute("class", "card-title");
        header.innerText = post.title

        let paragraph = document.createElement("p");
        paragraph.setAttribute("class", "card-text");
        paragraph.innerText = post.content

        cardBody.append(header, paragraph);
        card.append(cardBody);
        this.newsContainer.append(card);
    }

}

document.addEventListener("DOMContentLoaded", () => {
    let news = new News();
});