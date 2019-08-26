import $ from 'jquery';
import { Collapse } from 'bootstrap';
import 'popper.js';

$('.collapse').collapse({
    toggle: false,
});

$('.jump-links a').each(function () {
    $(this).click(function (ev) {
        ev.preventDefault();
        $([ document.documentElement, document.body ]).animate({
            scrollTop: $($(this).attr('href')).offset().top,
        }, 200);
    });
});
