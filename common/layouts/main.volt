<!DOCTYPE html>
<html lang="de">
    <head>
        {{ get_title() }}
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--CSS-->
        {{ stylesheet_link("//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css", false) }}
        {{ stylesheet_link("http://cdn.wysibb.com/css/default/wbbtheme.css", false) }}
        {{ stylesheet_link("dist/css/app.min.css") }}
        <!--JS-->
        {{ javascript_include("//code.jquery.com/jquery-1.10.2.js", false) }}
        {{ javascript_include("//code.jquery.com/ui/1.11.2/jquery-ui.js", false) }}
        {{ javascript_include("https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js", false) }}
        {{ javascript_include("http://cdn.wysibb.com/js/jquery.wysibb.min.js", false) }}
        {{ javascript_include("dist/js/helper.min.js") }}
        {{ javascript_include("dist/js/forum.min.js") }}
    </head>
    <body>
        <!--INCLUDE NAVI-->
        {{ partial("partials/nav") }}
        <!--INCLUDE CONTENT-->
        <div class="container">
            {{ content() }}
        </div>
        <div id="stormform"></div>
    </body>
</html>