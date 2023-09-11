<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div id="redoc_container"></div>
<script src="https://cdn.jsdelivr.net/npm/@redoc/redoc-pro@1.0.0-beta.38/dist/redocpro-standalone.min.js"></script>
<script>

    RedocPro.init(
        "{{config('apidoc.output') . "/apidoc.json"}}", {
            "hideDownloadButton" : {{config('apidoc.hide_download_button') ?: 0}},
            "showConsole": {{config('apidoc.hide_try_it') ?: 0}},
            "redocExport": "RedocPro",
            "layout": { "scope": "section" },
        }, document.getElementById("redoc_container")
    );

    // var constantMock = window.fetch;
    // window.fetch = function () {
    //
    //     if (/\/api/.test(arguments[0]) && !arguments[1].headers.Accept) {
    //         arguments[1].headers.Accept = 'application/json';
    //     }
    //
    //     return constantMock.apply(this, arguments)
    // }
</script>
</body>
</html>
