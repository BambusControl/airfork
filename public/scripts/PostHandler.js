class PostHandler
{
    postContainer;
    user;
    voteHandler;
    imageHandler;
    type;
    allPosts;

    constructor(container, user)
    {
        this.createPost = this.createPost.bind(this);
        this.load = this.load.bind(this);

        this.allPosts = [];
        this.postContainer = container;
        this.user = user;
        this.type = container.title;

        if (user != null) {
            this.voteHandler = new VoteHandler(user.uid);
            this.voteHandler.loadAll();
        } else {
            this.voteHandler = null;
        }

        this.imageHandler = new ImageHandler();
        this.imageHandler.loadAll();

        if (this.type === "article") {
            let btn = document.getElementById("article-switch");
            if (btn != null) {
                btn.onchange = () => this.toggleType($(btn));
            }
        } else {

        }
    }

    load(dataGet)
    {
        let request = "?c=home&a=get_posts";
        if (dataGet.a === "profile") {
            request += "&uid=" + (dataGet.uid != null ? dataGet.uid : this.user.uid);
        } else {
            request += "&type=" + this.type;
        }

        let spinner = new Spinner($(this.postContainer), true);
        fetch(request).then(
            j => j.json().then(
                posts => {
                    spinner.remove();
                    posts.forEach(this.createPost);
                }
            )
        );
    }

    toggleType(checkbox)
    {
        // if (this.type != null) {
            if (this.type === "article") {
                this.type = "userpost";
            } else {
                this.type = "article";
            }

            // checkbox.prop("disabled", true);

            $("#first").toggle();
            $("#second").toggle();

            this.postContainer.innerHTML = "";
            this.load(_GET());
        // }
    }

    createPost(post)
    {
        if (this.allPosts[post.id] == null) {
            let header = this.generateHeader(post);
            let image = this.imageHandler.getImgAsync(post.image);
            let body = this.generateBody(post);
            let footer = this.generateFooter();

            let card = this.generateCard(header, image, body, footer);

            // Add the news post card-div into the list
            this.postContainer.appendChild(card);

            this.allPosts[post.id] = card;
        } else {
            this.postContainer.appendChild(this.allPosts[post.id]);
            // console.log("Cached post: pid = " + post.id);
        }
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

            cardHeader.appendChild(details);

            // Edit part part
            if (this.user.is_admin || post.author == this.user.uid) {

                let error = document.createElement("small");
                error.className = "text-danger";
                $(error).toggle();
                error.innerText = "Nastala chyba!";

                let hcontainer = document.createElement("div");
                hcontainer.setAttribute("data-id", post.id);

                // Edit button
                if (post.author == this.user.uid) {
                    let btnEdit = document.createElement("button");
                    btnEdit.className = "btn btn-sm btn-light text-primary";
                    btnEdit.innerText = "Upraviť";
                    btnEdit.setAttribute("data-state", "0");
                    btnEdit.type = "submit";
                    btnEdit.onclick = () => this.onEdit(btnEdit, cardHeader, title, post, error);
                    hcontainer.appendChild(btnEdit);
                }

                // Delete button
                let btnDelete = document.createElement("button");
                btnDelete.className = "btn btn-sm btn-light text-danger";
                btnDelete.setAttribute("data-state", "0");
                btnDelete.innerText = "Vymazať";
                btnDelete.onclick = () => this.onDelete(btnDelete, cardHeader, title, post, error);

                hcontainer.append(btnDelete, error);
                cardHeader.appendChild(hcontainer);
            }
        }

        return cardHeader;
    }

    onDelete(btnDelete, cardHeader, title, post, error) // TODO comment
    {
        let state = parseInt(btnDelete.getAttribute("data-state"));
        let b = $(btnDelete);

        if (state === -1) {
            // Reset button
            state++;
            b.toggleClass("btn-light text-danger btn-danger");
            b.text("Vymazať")
        } else if (state === 0) {
            // Do you really want to delete this post?
            state++;
            b.toggleClass("btn-light text-danger btn-danger");
            let w = b.css("width");
            b.text("Naozaj?").css("width", w);

            // Reset button after 5 seconds
            setTimeout(() => {
                b.attr("data-state", -1);
                this.onDelete(btnDelete, cardHeader, title, post, error);
            }, 5000);
        } else {
            // Delete the post
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
        b.attr("data-state", state);
    }

    onEdit(btnEdit, cardHeader, title, post, error) // TODO comment
    {
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
        b.toggleClass("text-primary btn-light btn-success");

        state = state + 1;
        state = state % 2;
        if (state === 0) {
            b.text("Edit");
        } else {
            b.text("Save");
        }
        b.attr("data-state", state);
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
        this.voteHandler.userVoteType(voteBlock.id).then(
            vote => {
                // Create voting system and append to voteBlock

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
