'use strict';
/*jslint browser: true*/
/*global $, jQuery, alert*/
$(document).ready(function () {
    var a = document.location.href.match(/\#(\.*)$/);
    return a && $(".pagination  a").each(function () {
        var b = $(this);
        b.attr("href", b.attr("href") + "#" + a[1]);
    });
});
