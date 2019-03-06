(function () {

    waitingDialog.show('Loading', {onShow: function () {
        // Websocket
        var ws = new WebSocket("ws://localhost:8080");

        ws.onopen = function (event) {
            // No implemented
        };

        ws.onmessage = function (event) {
            var message = JSON.parse(event.data);
            switch (message.type) {
                case 'statistics':
                    app.activeRaces = message.data.activeRaces;
                    app.lastRaces = message.data.lastRaces;
                    app.bestHorse = message.data.bestHorse;
                    break;
            }
            waitingDialog.hide();
        };

        // Events
        $(document).off('click', '#btn-create').on('click', '#btn-create', function (e) {
            waitingDialog.show();
            ws.send("create");
        });

        $(document).off('click', '#btn-progress').on('click', '#btn-progress', function (e) {
            waitingDialog.show();
            ws.send("progress");
        });

        // VueJS
        Vue.component('active-races', {
            props: ['race'],
            template: '#active-races-template'
        });

        Vue.component('last-races', {
            props: ['race'],
            template: '#last-races-template'
        });

        Vue.component('horses-table', {
            props: ['horses'],
            template: '#horses-table-template'
        });

        Vue.component('horses-top-table', {
            props: ['horses'],
            template: '#horses-top-table-template'
        });

        var app = new Vue({
            el: '#app',
            data: {
                activeRaces: [],
                lastRaces: [],
                bestHorse: {}
            },
            computed: {
                isCreateDisabled: function () {
                    return this.activeRaces.length === 3;
                },
                isProgressDisabled: function () {
                    return this.activeRaces.length === 0;
                }
            }
        });
    }});

})();