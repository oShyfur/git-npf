$(function () {

    function reArrangeElementsinRegion(section, regionName) {
        $(section).children().each(function (index, item) {

            if ($(this).attr('data-index') != index) {
                $(this).attr('data-index', index)
            }
            $(this).attr('data-region', regionName);
            $(this).find('input').remove();
            $(this).append('<input type="hidden" name="blocks[' + regionName + '][]" value="' + $(this).attr('data-id') + '">');
        });
    }

    // top region
    $('#top').sortable({
        connectWith: ".region",
        handle: ".drag-button",
        receive: function (event, ui) {
            reArrangeElementsinRegion(this, 'top');
        }
    });

    // left region
    $('#left').sortable({
        connectWith: ".region",
        handle: ".drag-button",
        receive: function (event, ui) {
            reArrangeElementsinRegion(this, 'left');
        }
    });

    // right region
    $('#right').sortable({
        connectWith: ".region",
        handle: ".drag-button",
        receive: function (event, ui) {
            reArrangeElementsinRegion(this, 'right');
        }
    });

    // footer region
    $('#footer').sortable({
        connectWith: ".region",
        handle: ".drag-button",
        receive: function (event, ui) {
            reArrangeElementsinRegion(this, 'footer');
        }
    });

    // no region
    $('#no_region').sortable({
        connectWith: ".region",
        handle: ".drag-button",
        receive: function (event, ui) {

            reArrangeElementsinRegion(this, 'no_region');
        }
    });

});