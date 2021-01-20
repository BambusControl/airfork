class VoteHandler
{
    uid;
    userVotes;
    allVotes;

    constructor(userId)
    {
        this.uid = userId;
        this.userVotes = [];
        this.allVotes = [];
        this.load = this.load.bind(this);
        this.userVote = this.userVote.bind(this);
        this.vote = this.vote.bind(this);
        this.voteCount = this.voteCount.bind(this);
        // alert("new");
    }

    async load()
    {
        alert("load");
        try {
            this.allVotes = await (await fetch("?c=home&a=get_votes")).json();
        } catch (e) {
            console.error("Error VoteHandler::load() " + e.message);
        }

        for (let vote of this.allVotes) {

            if (vote.user == this.uid) {
                this.userVotes[vote.post] = vote;
            }
        }
    }

    async userVote(pid)
    {
        if (this.userVotes[pid] == null) {
            console.log("Vote not cached: " + pid + " " + this.uid);
            let vote = await (await fetch("?c=home&a=get_vote&pid=" + pid + "&uid=" + this.uid)).json()
            this.userVotes[pid] = vote;
            return vote;
        } else {
            console.log("Vote cached: + " + pid);
            return this.userVotes[pid]
        }
    }

    async vote(voteBtn) // TODO tidy
    {
        // Get id of the post
        let pid = voteBtn.parent()[0].id;
        let request, voteCount, otherVote;

        let isUpvote = voteBtn.hasClass("btn-upvote");
        let className = {true : "upvoted", false : "downvoted"};

        // Check for other vote button
        otherVote = isUpvote ? voteBtn.next().next() : voteBtn.prev().prev();
        if (otherVote.hasClass(className[!isUpvote])) {
            otherVote.removeClass(className[!isUpvote]);
            request = "?c=home&a=vote&pid=" + pid + "&uid=" + this.uid + "&t=" + "0";
            await fetch(request); // TODO
        }

        voteBtn.toggleClass(className[isUpvote])

        // Update database
        request = "?c=home&a=vote&pid=" + pid + "&uid=" + this.uid + "&t=";
        request += voteBtn.hasClass(className[isUpvote]) ? (isUpvote ? "1" : "-1") : "0";

        voteCount = isUpvote ? voteBtn.next() : voteBtn.prev();

        // update count from server
        fetch(request).then(() => {
            this.voteCount(pid, true).then(count => {
                voteCount.text(count);
            });
        });
    }

    async voteCount(pid, forceUpdate = false) {
        if (this.allVotes[pid] == null || forceUpdate) {
            let votes =  await ( await fetch("?c=home&a=get_votes&pid=" + pid)).json()
            this.allVotes[pid] = votes == null ? [] : votes;
        }

        let count = 0;
        for (let vote of this.allVotes[pid]) {
            if (vote.post === pid) {
                count += parseInt(vote.type, 10);
            }
        }
        return count;
    }
}