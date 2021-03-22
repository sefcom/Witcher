var lang = B.getLang();
if (lang != B.options.defaultLang) {
    $.ajax({
        "type": "get",
        "url": "/lang/" + lang + "/translate.json" + "?" + Math.random(),
        "async": true,
        "cache": false,
        "dataType": "text",
        "success": function (data) {
            B.setMsg($.parseJSON(data));
            B.translatePage();
        }
    })
} else {
    document.documentElement.style.display = '';
}

document.documentElement.className += " lang-" + lang;