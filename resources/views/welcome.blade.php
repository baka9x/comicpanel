<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Comic Blog</title>
        <!-- Styles -->
        <link rel="stylesheet" href="/css/all.css"/>
       
        <script>
            (function () {
                window.Laravel = {
                    csrfToken: '{{ csrf_token() }}'
                };
            })();
        </script>

    </head>
    <body>
      <div id="app">
          <mainapp/>
      </div>
    </body>
    <script src="{{mix('/js/app.js')}}"></script>
</html>
