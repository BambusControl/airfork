$(document).ready(async function(){
    let container = document.getElementById("post-container");

    if (container != null) {
        let user = await (await fetch("?c=account&a=logged_in")).json();
        let posts = new PostHandler(container, user);
        await posts.load(true);
    }
});

class PostHandler
{
    postContainer;
    path;
    user;
    voteHandler;
    type;
    dataGet;

    constructor(container, user)
    {
        this.toggleType = this.toggleType.bind(this);
        this.createNewsPost = this.createNewsPost.bind(this);
        this.load = this.load.bind(this);
        this.getVoteBlock = this.getVoteBlock.bind(this);

        let parts = window.location.search.substr(1).split("&");
        this.dataGet = {};
        for (let i = 0; i < parts.length; i++) {
            let temp = parts[i].split("=");
            this.dataGet[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
        }

        this.postContainer = container;
        this.type = container.title;
        this.path = new ImagePath();
        this.user = user;

        if (user != null) {
            this.voteHandler = new VoteHandler(user.uid);
        } else {
            this.voteHandler = null;
        }

        if (this.type === "article") {
            let sw = document.getElementById("article-switch");
            sw.onchange = () => this.toggleType($(sw));
        }
    }

    async load(all)
    {
        try {
            console.log("?c=home&a=get_all_posts&type=" + this.type + (this.dataGet.a === "profile" ? ("&uid=" + this.user.uid) : ""));
            let data = await ( await fetch("?c=home&a=get_all_posts&type=" + this.type + (this.dataGet.a === "profile" ? ("&uid=" + this.user.uid) : ""))).json();
            await this.voteHandler.load();
            await this.path.loadImages();

            data.forEach(this.createNewsPost);
        } catch (e) {
            console.error("Error PostHandler::load() " + e.message);
        }
    }

    async toggleType(sw)
    {
        if (this.type === "article") {
            this.type = "userpost";
        } else {
            this.type = "article";
        }

        sw.prop("disabled", true);
        // sw.addProp("disabled", )

        $("#first").toggle();
        $("#second").toggle();

        this.postContainer.innerHTML = "";
        try {
            await this.load(true);
        } catch (e) {
            console.error("Error PostHandler::toggleType() " + e.message);
        }
        sw.prop("disabled", false);
    }

    createNewsPost(post)
    {
        let header = this.generateHeader(post);
        let image = this.getImage(post);
        let body = this.generateBody(post);
        let footer = this.generateFooter();

        let card = this.generateCard(header, image, body, footer);

        // Add the news post card-div into the list
        this.postContainer.appendChild(card);
    }

    generateCard(cardHeader, cardImage, cardBody, cardFooter)
    {
        // Add everything to the card div
        let card = document.createElement("div");
        card.className = "card post max-height";

        // Header
        card.appendChild(cardHeader);

        // Image
        if (cardImage != null) {
            card.appendChild(cardImage);
        }

        // Body and Footer
        card.append(cardBody, cardFooter);
        return card;
    }

    generateHeader(post)
    {
        // Create header
        let cardHeader = document.createElement("div");
        cardHeader.setAttribute("class", "card-header post");

        // Voting if a user is logged in
        if (this.user.logged_in) {
            let voteBlock = this.getVoteBlock(post);
            cardHeader.appendChild(voteBlock);
        }

        // Post title
        let title = document.createElement("span");
        title.className = "h4"
        title.innerText = post.title

        cardHeader.appendChild(title);
        return cardHeader;
    }

    generateFooter()
    {
        // Create footer TODO footer
        let cardFooter = document.createElement("div");
        cardFooter.className = "card-footer p-0";

        // Create expand button
        let expand = document.createElement("i");
        expand.className = "fas fa-arrows-alt-v mr-1";
        let showmore = document.createElement("button");
        showmore.type = "button";
        showmore.className = "block outl-n";
        $(cardFooter).click(function () {
            $(cardFooter).parent().toggleClass("max-height");
            // TODO flip arrow
        });
        showmore.append(expand);

        cardFooter.appendChild(showmore);
        return cardFooter;
    }

    generateBody(post)
    {
        // Create text
        let paragraph = document.createElement("p");
        paragraph.className = "card-text";
        paragraph.innerText = post.content;
        // TODO trim text - then button view more...

        // Create card body
        let cardBody = document.createElement("div");
        cardBody.className = "card-body overflow-h"
        cardBody.appendChild(paragraph);

        return cardBody;
    }

    getImage(post)
    {
        if (post.image != null) {   // TODO expand image
            let image = this.path.getById(post.image);
            let img = document.createElement("img");
            img.className = "card-img-top";
            img.src = image.path;
            img.alt = image.alt;
            return img;
            // card.appendChild(img);
        }
        return null;
    }

    getVoteBlock(post)
    {
        // Voting block
        let voteBlock = document.createElement("span");
        voteBlock.className = "vote-block"
        voteBlock.id = post.id;

        // Check if user voted on this post
        let userVote = 0;
        if (this.voteHandler != null) {
            userVote = this.voteHandler.userVote(voteBlock.id);
            // alert(voteBlock.id + '   ' + userVote.type);
            if (userVote != null) {
                userVote = parseInt(userVote.type, 10);
                // alert(voteBlock.id + '   ' + userVote + '    '  + (userVote===1) + '    ' + (userVote === 1 ? " upvoted" : "") + '    ' + (userVote === -1 ? " downvoted" : ""));
            }

        }

        // Upvote button
        let upvoteArrow = document.createElement("i");
        upvoteArrow.className = "fas fa-chevron-up";
        let upvote = document.createElement("button");
        upvote.type = "button";
        upvote.className = "btn-upvote outl-n" + (userVote === 1 ? " upvoted" : "");
        upvote.onclick = () => this.voteHandler.vote($(upvote));
        upvote.appendChild(upvoteArrow);

        // Downvote button
        let downvoteArrow = document.createElement("i");
        downvoteArrow.className = "fas fa-chevron-down";
        let downvote = document.createElement("button");
        downvote.type = "button";
        downvote.className = "btn-downvote outl-n" + (userVote === -1 ? " downvoted" : "");
        downvote.onclick = () => this.voteHandler.vote($(downvote));
        downvote.appendChild(downvoteArrow);

        // Vote counter
        let count = document.createElement("span");
        count.className = "vote-counter mlr-1";
        count.innerText = this.voteHandler.voteCount(voteBlock.id);    // TODO string format

        voteBlock.append(upvote, count, downvote);
        return voteBlock;
    }
}

class VoteHandler
{
    uid;
    userVotes;
    allVotes;

    constructor(userId)
    {
        this.uid = userId;
        this.userVotes = [];
        this.load = this.load.bind(this);
        this.userVote = this.userVote.bind(this);
        this.vote = this.vote.bind(this);
        this.voteCount = this.voteCount.bind(this);
    }

    async load()
    {
        try {
            this.allVotes = await (await fetch("?c=home&a=get_votes")).json();
        } catch (e) {
            console.error("Error VoteHandler::load() " + e.message);
        }

        for (let vote of this.allVotes) {

            if (vote.user === this.uid) {
                this.userVotes.push(vote);
            }
        }
    }

    userVote(postId)
    {
        for (let vote of this.userVotes) {
            if (vote.post === postId) {
                return vote;
            }
        }
        return null;
    }

    async vote(voteBtn)
    {
        // Get id of the post
        let pid = voteBtn.parent()[0].id;
        let request, voteCount, otherVote;

        if (voteBtn.hasClass("btn-upvote")) {
            // Upvote

            // Check for other vote button
            otherVote = voteBtn.next().next();
            if (otherVote.hasClass("downvoted")) {
                otherVote.removeClass("downvoted");
                request = "?c=home&a=vote&pid=" + pid + "&uid=" + this.uid + "&t=" + "0";
                try {
                    await fetch(request);
                } catch (e) {
                    console.error("Error PostHandler::vote(), check upvote button, " + e.message);
                }
            }

            // Update appearance
            voteBtn.toggleClass("upvoted");

            // Update database
            request = "?c=home&a=vote&pid=" + pid + "&uid=" + this.uid + "&t=" + (voteBtn.hasClass("upvoted") ? "1" : "0");

            voteCount = voteBtn.next();
        } else {
            // Downvote

            // Check for other vote button
            otherVote = voteBtn.prev().prev(); // TODO
            if (otherVote.hasClass("upvoted")) {
                otherVote.removeClass("upvoted");
                request = "?c=home&a=vote&pid=" + pid + "&uid=" + this.uid + "&t=" + "0";
                try {
                    await fetch(request);
                } catch (e) {
                    console.error("Error PostHandler::vote(), check upvote button, " + e.message);
                }
            }

            // Update appearance
            voteBtn.toggleClass("downvoted");

            // Update database
            request = "?c=home&a=vote&pid=" + pid + "&uid=" + this.uid + "&t=" + (voteBtn.hasClass("downvoted") ? "-1" : "0");
            voteCount = voteBtn.prev();
        }

        // get data from server
        try {
            await fetch(request);
            await this.load();
        } catch (e) {
            console.error("Error PostHandler::vote(), couldn't get data from server: " + e.message);
        }
        let count = this.voteCount(pid);

        // update counter
        voteCount.text(count);
    }

    voteCount(postId) {
        let c = 0;
        for (let vote of this.allVotes) {
            if (vote.post === postId) {
                c += parseInt(vote.type, 10);
            }
        }
        // alert(c);
        return c;
    }
}
