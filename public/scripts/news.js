class News
{
    newsContainer;
    path;

    constructor()
    {
        this.createNewsPost = this.createNewsPost.bind(this);
        this.getNews = this.getNews.bind(this);
        this.newsContainer = document.getElementById("news-container");
        this.path = new ImagePath();
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

    createNewsPost(post)    // TODO refactor to more methods
    {
        // Card-header content - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Upvote
        let upvoteArrow = document.createElement("i");
        upvoteArrow.className = "fas fa-chevron-up";
        let upvote = document.createElement("button");
        upvote.type = "button";
        upvote.className = "btn-upvote"
        // upvote.onclick = () => upvoteArrow.style.color = "green";
        upvote.appendChild(upvoteArrow);

        // Downvote
        let downvoteArrow = document.createElement("i");
        downvoteArrow.className = "fas fa-chevron-down";
        let downvote = document.createElement("button");
        downvote.type = "button";
        downvote.className = "btn-downvote";
        // downvote.onclick = () => downvoteArrow.style.color = "red";
        downvote.appendChild(downvoteArrow);

        // Vote counter
        let count = document.createElement("span");
        count.className = "vote-counter mlr-1";
        count.innerText = post.upvotes - post.downvotes;    // TODO string format

        // Voting block
        let voteBlock = document.createElement("span"); // TODO widen a bit
        voteBlock.className = "vote-block"
        voteBlock.id = post.id;
        voteBlock.append(upvote, count, downvote);

        // Title
        let title = document.createElement("span");
        title.className = "h4"
        title.innerText = post.title

        let cardHeader = document.createElement("div");
        cardHeader.setAttribute("class", "card-header post");
        cardHeader.append(voteBlock, title);

        // Card-body content - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        let paragraph = document.createElement("p");
        paragraph.className = "card-text";
        paragraph.innerText = post.content;
        // TODO trim text - then button view more...
        let expand = document.createElement("i");
        expand.className = "fas fa-arrows-alt-v mr-1";
        let expandText = document.createElement("span")
        expandText.innerText = "zobraz viac"
        let showmore = document.createElement("button");
        showmore.type = "button";
        showmore.append(expand, expandText);

        let cardBody = document.createElement("div");
        cardBody.className = "card-body"
        cardBody.append(paragraph, showmore);

        // Card-footer content - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // TODO edit delete ... post

        let cardFooter = document.createElement("div");
        cardFooter.className = "card-footer";
        cardFooter.innerText = "this is the footer"
        // cardFooter.append();

        // Add everything to the card div  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        let card = document.createElement("div");
        card.setAttribute("class", "card news");
        card.appendChild(cardHeader);

        // Image
        if (post.image != null) {   // TODO expand image
            let image = this.path.getById(post.image);
            let img = document.createElement("img");
            img.className = "card-img-top";
            img.src = image.path;
            img.alt = image.alt;
            card.appendChild(img);
        }

        card.append(cardBody, cardFooter);

        // Add the news post card-div into the list
        this.newsContainer.append(card);
    }

}

/*
document.addEventListener("DOMContentLoaded", () => {
    let news = new News();
});*/
