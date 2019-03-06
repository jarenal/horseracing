<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{title}</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="css/app.css">
    </head>
    <body>
        <div id="app" v-cloak>
            <div class="container-fluid">
                <div class="row text-center">
                    <div class="col-6">
                        <button id="btn-create" type="button" class="btn btn-success m-3" :disabled="isCreateDisabled">Create Race</button>
                    </div>
                    <div class="col-6">
                        <button id="btn-progress" type="button" class="btn btn-primary m-3" :disabled="isProgressDisabled">Progress</button>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="card mb-3" style="width: 100%">
                            <div class="card-header"><h3>Active races</h3></div>
                            <div class="card-body">
                                <active-races
                                        v-for="race in activeRaces"
                                        v-bind:race="race"
                                        v-bind:key="race.id"
                                ></active-races>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="card mb-3" style="width: 100%">
                            <div class="card-header"><h3>Last 5 races</h3></div>
                            <div class="card-body">
                                <last-races
                                        v-for="race in lastRaces"
                                        v-bind:race="race"
                                        v-bind:key="race.id"
                                ></last-races>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <div class="card mb-3" style="width: 100%">
                            <div class="card-header"><h3>Hall of fame</h3></div>
                            <div class="card-body">
                                <div class="card mb-3" style="width: 100%">
                                    <div class="card-header">The best time was <b>{{ bestHorse.timeElapsed }}</b> sec. performed by the horse with number <b>#{{ bestHorse.id }}</b> in the race number <b>#{{ bestHorse.race_id }}</b></div>
                                    <div class="card-body">
                                        <table class="table table-striped mt-1">
                                            <tbody>
                                            <tr>
                                                <td>Speed</td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" :style="{width: (bestHorse.speed * 10) + '%'}" :aria-valuenow="bestHorse.speed * 10" aria-valuemin="0" aria-valuemax="100">{{ bestHorse.speed }}</div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Strength</td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" :style="{width: (bestHorse.strength * 10) + '%'}" :aria-valuenow="bestHorse.strength * 10" aria-valuemin="0" aria-valuemax="100">{{ bestHorse.strength }}</div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Endurance</td>
                                                <td>
                                                    <div class="progress">
                                                        <div class="progress-bar" role="progressbar" :style="{width: (bestHorse.endurance * 10) + '%'}" :aria-valuenow="bestHorse.endurance * 10" aria-valuemin="0" aria-valuemax="100">{{ bestHorse.endurance }}</div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Javascript -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-waitingfor/1.2.7/bootstrap-waitingfor.min.js" integrity="sha256-RXOSAzmT17HHrnqwX3Hko5x/Tg9CchHMX6wR5dGJ6UY=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/vue"></script>
        <script src="js/app.js"></script>
        <script type="text/x-template" id="active-races-template">
            <div class="row">
                <div class="col">
                    <div class="card mb-3" style="width: 100%">
                        <div class="card-header">Race #{{ race.id }} (Active)</div>
                        <div class="card-body">
                            <div class="card-text">
                                <b>Distance covered:</b> {{ race.distanceCovered }} m. <b>Time elapsed</b> {{ race.timeElapsed }} s.
                            </div>
                            <horses-table
                                v-bind:horses="race.horses"
                            ></horses-table>
                        </div>
                    </div>
                </div>
            </div>
        </script>
        <script type="text/x-template" id="last-races-template">
            <div class="row">
                <div class="col">
                    <div class="card mb-3" style="width: 100%">
                        <div class="card-header">Race #{{ race.id }} (Finished)</div>
                        <div class="card-body">
                            <div class="card-text">
                                <b>Distance covered:</b> {{ race.distanceCovered }} m. <b>Time elapsed</b> {{ race.timeElapsed }} s.
                            </div>
                            <horses-top-table
                                v-bind:horses="race.horsesTop3"
                            ></horses-top-table>
                        </div>
                    </div>
                </div>
            </div>
        </script>
        <script type="text/x-template" id="horses-table-template">
            <table class="table table-striped mt-1">
                <thead>
                <tr>
                    <th scope="col">Rank</th>
                    <th scope="col">Horse Id.</th>
                    <th scope="col">Speed</th>
                    <th scope="col">Strength</th>
                    <th scope="col">Endurance</th>
                    <th scope="col">Time (sec)</th>
                    <th scope="col">Distance (m)</th>
                    <th scope="col" class="col-4">Distance (%)</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="horse in horses" v-bind:class="{ 'table-success': horse.distancePercentage === 100 }">
                    <td scope="row">#{{ horse.rank }}</td>
                    <td scope="row">{{ horse.id }}</td>
                    <td scope="row">{{ horse.speed }}</td>
                    <td scope="row">{{ horse.strength }}</td>
                    <td scope="row">{{ horse.endurance }}</td>
                    <td scope="row">{{ horse.timeElapsed }}</td>
                    <td>{{ horse.distance }}</td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" :style="{width: horse.distancePercentage + '%'}" :aria-valuenow="horse.distancePercentage" aria-valuemin="0" aria-valuemax="100">{{horse.distancePercentage}}%</div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </script>
        <script type="text/x-template" id="horses-top-table-template">
            <table class="table table-striped mt-1">
                <thead>
                <tr>
                    <th scope="col">Rank</th>
                    <th scope="col">Horse Id.</th>
                    <th scope="col">Time (sec)</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="horse in horses">
                    <th scope="row">#{{ horse.rank }}</th>
                    <th scope="row">{{ horse.id }}</th>
                    <th scope="row">{{ horse.timeElapsed}}</th>
                </tr>
                </tbody>
            </table>
        </script>
    </body>
</html>