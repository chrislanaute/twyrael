$(document).ready(function () {
    $('.diablo').popover({
        container: 'body'
    });

    $('.diablo').click(function() {
        var request = new XMLHttpRequest();

        request.open('GET', 'https://EU.api.blizzard.com/d3/profile/' + $(this).html().replace("#", "-") + '/?locale=fr_FR&access_token=USCCc4IwaUmT7IbonZ08AUY8H31Tho2E0P', true);
        request.onload = function() {
            var data = JSON.parse(this.response);
            if (request.status >= 200 && request.status < 400)
                $(this).attr('data-content', data.battleTag);
            else
                console.log('error');
        }

        request.send();
    });
});