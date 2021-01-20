class VoteHandler
{
    uid;
    userVoteTypes;
    allVotes;

    constructor(userId)
    {
        this.uid = userId;
        this.userVoteTypes = [];
        this.allVotes = [];
    }

    loadAll()
    {
        fetch("?c=home&a=get_votes").then(
            j => j.json().then( votes => {
                for (let vote of votes) {
                    if (this.allVotes[vote.post] == null) {
                        this.allVotes[vote.post] = [];
                    }
                    this.allVotes[vote.post][vote.user] = vote;

                    if (vote.user == this.uid) {
                        this.userVoteTypes[vote.post] = parseInt(vote.type, 10);
                    }
                }
            })
        );
    }

    async userVoteType(pid)
    {
        if (this.userVoteTypes[pid] == null) {
            // Get vote from server
            let vote = await (await fetch("?c=home&a=get_vote&pid=" + pid + "&uid=" + this.uid)).json()
            this.userVoteTypes[pid] = vote == null ? 0 : parseInt(vote.type, 10);
            return this.userVoteTypes[pid];
        } else {
            // Return vote from 'cache'
            // console.log("Cached vote: pid = " + pid + ", vote = " + this.userVoteTypes[pid]);
            return this.userVoteTypes[pid]
        }
    }

    async vote(voteBtn)
    {
        // Get id of the post
        let pid = voteBtn.parent()[0].id;
        let request = '';

        let isUpvote = voteBtn.hasClass("btn-upvote");
        let className = {true : "upvoted", false : "downvoted"};

        // Check for other vote button
        let otherVote = isUpvote ? voteBtn.next().next() : voteBtn.prev().prev();
        if (otherVote.hasClass(className[!isUpvote])) {
            // Other vote button was clicked, remove the vote
            otherVote.removeClass(className[!isUpvote]);
            request = "?c=home&a=vote&pid=" + pid + "&uid=" + this.uid + "&t=" + "0";
            await fetch(request);
        }

        // Update button appearance
        voteBtn.toggleClass(className[isUpvote])

        // Update database
        request = "?c=home&a=vote&pid=" + pid + "&uid=" + this.uid + "&t=";
        request += voteBtn.hasClass(className[isUpvote]) ? (isUpvote ? "1" : "-1") : "0";

        let voteCount = isUpvote ? voteBtn.next() : voteBtn.prev();

        // Update count from server
        fetch(request).then(() => {
            this.voteCount(pid, true).then(count => voteCount.text(count));
        });
    }

    async voteCount(pid, forceUpdate = false)
    {
        if (this.allVotes[pid] == null || forceUpdate) {
            // Get vote from server
            let votes =  await ( await fetch("?c=home&a=get_votes&pid=" + pid)).json()
            this.allVotes[pid] = votes == null ? [] : votes;
        }

        // Calculate post 'rating'

        let count = 0;
        for (let vote of this.allVotes[pid]) {
            if (vote != null) {
                if (vote.post === pid) {
                    count += parseInt(vote.type, 10);
                }
            }
        }
        return count;
    }
}