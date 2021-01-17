$(document).ready(async function(){

    let other = new Other();
    await other.loadNews();

    $(".btn-upvote").click( function () {
        other.handleVote($(this));
    });
    // TODO
    $(".btn-downvote").click( function () {
        other.handleVote($(this));
    });

});

class Other
{
    news;

    constructor()
    {
        this.news = new News();
        this.loadNews = this.loadNews.bind(this);
        this.updateCounter = this.updateCounter.bind(this);
    }

    async loadNews()
    {
        await this.news.getNews();
    }

    async handleVote(vote)
    {
        // Get id of the post
        let id = vote.parent()[0].id;

        if (vote.hasClass("btn-upvote")) {
            // Upvote

            // Update appearance
            vote.toggleClass("upvoted");

            // Update database
            // $.get("?c=home&a=vote&id=" + id + "&t=" + vote.hasClass("upvoted") ? "u" : "ru");
            let request = "?c=home&a=vote&id=" + id + "&t=" + (vote.hasClass("upvoted") ? "u" : "ru");
            let post = await( await fetch(request) ).json();

            // Update counter
            this.updateCounter(vote.next()[0], post.upvotes, post.downvotes)
        } else {
            // Downvote

        }
    }

    updateCounter(counter, upvotes, downvotes)
    {
        counter.innerText = upvotes - downvotes;
    }
}