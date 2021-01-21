class UserHandler
{

    users;

    constructor()
    {
        this.users = [];
    }

    loadAll() {
        fetch("?c=account&a=get_users").then(
            j => j.json().then(
                users => users.forEach(
                    user => {
                        if (this.users[user.id] == null) {
                            this.users[user.id] = user;
                        }
                    }
                )
            )
        );
    }

    async getUser(uid)
    {
        if (this.users[uid] == null) {
            this.users[uid] = await (await fetch("?c=account&a=get_users")).json();
        }

        return this.users[uid];
    }

    getProfileLink(uid)
    {
        let link = document.createElement("a");
        link.href = "#";

        this.getUser(uid).then(
            user => {
                link.href = "?c=account&a=profile&uid=" + user.id;
                link.innerText = user.firstname + " " + user.lastname;
            }
        );

        return link;
    }

}