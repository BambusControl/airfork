class PostHandler
{
    postContainer;
    user;
    voteHandler;
    type;

    constructor(container, user)
    {
        this.toggleType = this.toggleType.bind(this);
        this.createNewsPost = this.createNewsPost.bind(this);
        this.load = this.load.bind(this);
        this.getVoteBlock = this.getVoteBlock.bind(this);

        this.postContainer = container;
        this.type = container.title;
        this.user = user;

        if (user != null) {
            this.voteHandler = new VoteHandler(user.uid);
        } else {
            this.voteHandler = null;
        }

        if (this.type === "article") {
            let btn = document.getElementById("article-switch");
            if (btn != null) {
                btn.onchange = () => this.toggleType($(btn));
            }
        } else {

        }
    }

    async load(dataGet)
    {
        try {
            let input = "?c=home&a=get_all_posts&type=" + this.type + (dataGet.a === "profile" ? ("&uid=" + (dataGet.uid != null ? dataGet.uid : this.user.uid)) : "");
            let data = await ( await fetch(input) ).json();
            // await this.voteHandler.load();

            data.forEach(this.createNewsPost);
        } catch (e) {
            console.error("Error PostHandler::load() " + e.message);
        }
    }

    async toggleType(checkbox)
    {
        if (this.type === "article") {
            this.type = "userpost";
        } else {
            this.type = "article";
        }

        checkbox.prop("disabled", true);

        $("#first").toggle();
        $("#second").toggle();

        this.postContainer.innerHTML = "";
        let spinner = new Spinner(this.postContainer);
        try {
            this.load(_GET()).then(() => {
                spinner.remove();
                checkbox.prop("disabled", false);
            });
        } catch (e) {
            console.error("Error PostHandler::toggleType() " + e.message);
        }
    }

    createNewsPost(post)
    {
        let header = this.generateHeader(post);
        let image = ImagePath.getImgAsync(post.image);
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
        cardHeader.className = "card-header post";

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

        // Details table
        let details = document.createElement("table");
        details.className = "post-details";

        // Date
        let detR1 = document.createElement("tr");
        detR1.innerText = post.date;
        details.appendChild(detR1);

        // Author
        if (this.user.logged_in) {
            let detR2 = document.createElement("tr");
            detR2.innerHTML = "<a href=\"?c=account&a=profile&uid=" + post.author + "\">Profil autora</a>";
            details.appendChild(detR2);
        }


        cardHeader.appendChild(details);

        // Admin part
        if (this.user.logged_in && (this.user.is_admin || post.author == this.user.uid)) {

            let error = document.createElement("small");
            error.className = "text-danger";
            $(error).toggle();
            error.innerText = "Nastala chyba!";

            let hcontainer = document.createElement("div");
            hcontainer.setAttribute("data-id", post.id);

            if (post.author == this.user.uid) {
                let btnEdit = document.createElement("button");
                btnEdit.className = "btn btn-sm btn-light text-primary";
                btnEdit.innerText = "Edit";
                btnEdit.setAttribute("data-state", "0");
                btnEdit.type = "submit";
                btnEdit.onclick = function () {
                    let b = $(btnEdit);
                    let state = parseInt(b.attr("data-state"));

                    // Paragraph
                    let p = $(cardHeader).parent().find("p").toggle();
                    let isTextP = p.siblings().length === 0;
                    let pt;
                    if (isTextP) {
                        pt = $("<textarea></textarea>").text(p.text()).addClass("form-control");
                        pt.prop("required", true);
                        p.after(pt);
                    } else {
                        pt = p.next().toggle();
                        if (state === 0) {
                            pt.val(p.text());
                        }
                    }

                    // Title
                    let h = $(title).toggle();
                    let ht;
                    if (isTextP) {
                        ht = $("<input type=\"text\">").val(h.text()).addClass("form-control");
                        ht.prop("required", true);
                        h.after(ht);
                    } else {
                        ht = h.next().toggle();
                        if (state === 0) {
                            ht.val(h.text());
                        }
                    }

                    // Post data
                    if (state === 1) {
                        let spinner = $("<div class=\"spinner-grow spinner-grow-sm\"></div>");
                        h.before(spinner);

                        $.post(
                            "?c=home&a=modify_post",
                            {
                                id: post.id,
                                title: ht.val(),
                                text: pt.val()
                            },
                            function (data, status) {
                                h.prev().remove();
                                if (status !== "success" || data.error != null) {
                                    error.innerText += "  " + data.error;
                                    $(error).toggle();
                                } else {
                                    h.text(data['title']);
                                    p.text(data['content']);
                                }
                            }
                        )
                    }

                    // Button
                    b.toggleClass("text-primary");
                    b.toggleClass("btn-light");
                    b.toggleClass("btn-success");

                    state = state + 1;
                    state = state % 2;
                    if (state === 0) {
                        b.text("Edit");
                    } else {
                        b.text("Save");
                    }
                    b.attr("data-state", state);
                };
                hcontainer.appendChild(btnEdit);
            }

            let btnDelete = document.createElement("button");
            btnDelete.className = "btn btn-sm btn-light text-danger";
            btnDelete.setAttribute("data-state", "0");
            btnDelete.innerText = "Delete";
            btnDelete.onclick = () => {
                let state = parseInt(btnDelete.getAttribute("data-state"));
                let b = $(btnDelete);

                if (state === 0) {
                    state++;
                    b.toggleClass("btn-light text-danger btn-danger");
                    b.text("Vymazať");
                } else {
                    let spinner = $("<div class=\"spinner-grow spinner-grow-sm\"></div>");
                    $(title).before(spinner);
                    $.get(
                        "?c=home&a=remove_post&pid=" + post.id,
                        (data, status) => {
                            spinner.remove();
                            if (status === "success") {
                                $(cardHeader).parent().remove();
                            } else {
                                $(error).toggle();
                            }
                        }
                    );
                }
                alert(state);
                b.attr("data-state", state);
            }

            hcontainer.append(btnDelete, error);
            // console.log(hcontainer.getAttribute("data-id"));
            // hcontainer.style = "position : relative; float : right; left : 8vh"
            cardHeader.appendChild(hcontainer);
        }

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

    getVoteBlock(post)
    {
        // Voting block
        let voteBlock = document.createElement("span");
        voteBlock.className = "vote-block"
        voteBlock.id = post.id;

        // Check if user voted on this post
        this.voteHandler.userVote(voteBlock.id).then(
            vote => {
                // Create voting system and append to voteBlock
                if (vote != null) {
                    console.log("vote " + vote);
                    vote = parseInt(vote.type, 10);
                }

                // Upvote button
                let upvoteArrow = document.createElement("i");
                upvoteArrow.className = "fas fa-chevron-up";
                let upvote = document.createElement("button");
                upvote.type = "button";
                upvote.className = "btn-upvote outl-n mr-1" + (vote === 1 ? " upvoted" : "");
                upvote.onclick = () => this.voteHandler.vote($(upvote));
                upvote.appendChild(upvoteArrow);

                // Downvote button
                let downvoteArrow = document.createElement("i");
                downvoteArrow.className = "fas fa-chevron-down";
                let downvote = document.createElement("button");
                downvote.type = "button";
                downvote.className = "btn-downvote outl-n ml-1" + (vote === -1 ? " downvoted" : "");
                downvote.onclick = () => this.voteHandler.vote($(downvote));
                downvote.appendChild(downvoteArrow);

                // Vote counter
                let count = document.createElement("span");
                count.className = "vote-counter";
                let s = new Spinner(count, true, true, true);
                this.voteHandler.voteCount(voteBlock.id).then(
                    c => {
                        count.innerText = c;
                        s.remove();
                    }
                );    // TODO string format

                voteBlock.append(upvote, count, downvote);
            }
        );

        return voteBlock;
    }
}
