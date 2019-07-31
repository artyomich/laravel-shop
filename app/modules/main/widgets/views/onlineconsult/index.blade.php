<script type="text/javascript">var CHelp;
    (function () {
        var d = document, s = d.createElement("script"), c = d.getElementsByTagName("script"), a = c[c.length - 1], h = d.location.protocol;
        s.src = "https://cdn.chathelp.ru/js.min/ch-base.js";
        s.type = "text/javascript";
        s.async = 1;
        a.parentNode.insertBefore(s, a);
        s.onload = function () {
            var siteId = "{{ $model->city_key }}";
            CHelp = new ChatHelpJS(siteId);
        }
    })()</script>