$(document).ready(async function(){
    let container = document.getElementById("post-container");

    if (container != null) {
        let user = await (await fetch("?c=account&a=logged_in")).json();

        let posts = new PostHandler(container, user);
        let dataGet = _GET();
        await posts.load(dataGet);
        setInterval(posts.load, 60000, dataGet);
    }
});

function _GET()
{
    let parts = window.location.search.substr(1).split("&");
    let dataGet = [];
    for (let i = 0; i < parts.length; i++) {
        let temp = parts[i].split("=");
        dataGet[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
    }
    return dataGet;
}

class Spinner
{
    spinner;

    constructor(container, grow = false, small = false, span = false)
    {
        this.remove = this.remove.bind(this);

        this.spinner = $(span ? "<span></span>" : "<div></div>");
        this.spinner.addClass(grow ? "spinner-grow" : "spinner-border")
        if (small) { this.spinner.addClass(grow ? "spinner-grow-sm" : "spinner-border-sm") };
        if (!span) {
            this.spinner.css("display", "block");
            this.spinner.css("margin-left", "auto");
            this.spinner.css("margin-right", "auto");
        }

        $(container).append(this.spinner);
    }

    remove()
    {
        this.spinner.remove();
    }
}