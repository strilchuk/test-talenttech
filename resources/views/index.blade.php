<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/my.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/my.css') }}" rel="stylesheet">

    <title>Talenttech-test</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="card w-50">
        <div class="card-body">
            <form id="inn-form">
                <div class="form-group">
                    <label for="InputINN">ИНН</label>
                    <div class="row">
                        <div class="col">
                            <input class="form-control" id="InputINN" aria-describedby="emailHelp"
                                   placeholder="Введите ИНН">
                        </div>
                        <div class="col my-send-col">
                            <button type="submit" class="btn btn-success ">Отправить</button>
                        </div>
                    </div>
                    <small id="emailHelp" class="form-text text-muted">Проверка принадлежности введённого ИНН
                        плательщику налога на профессиональный доход (самозанятый)</small>
                    <div class="alert alert-danger my-error" role="alert" style="display: none">

                    </div>
                    <div class="alert alert-success my-success" role="alert" style="display: none">

                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</body>
</html>
