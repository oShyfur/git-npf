$(function () {
    // rendering list
    renderList();
    // showing create form
    $('#createForm').hide();
    // hiding edit form
    $('#editForm').hide();
    // handle action buttons click event
    $('body').on('click', '.new-menu', function () {
        // showing create form
        $('#createForm').show();
    }).on('click', '.btn-add', function () {
        // get id from data attributes
        let id = $(this).data('id');
        // showing create form
        $('#createForm').show();
        // hiding edit form
        $('#editForm').hide();
        // fill form data
        $('#parent_id').val(id).trigger('change');
    }).on('click', '.btn-edit', function () {
        // get id from data attributes
        let id = $(this).data('id');
        // showing create form
        $('#createForm').hide();
        // hiding edit form
        $('#editForm').show();
        // get menu data by id
        let menu = getMenuItem(id);
        // fill form data
        $('#edit_title_en').val(menu.title.en);
        $('#edit_title_bn').val(menu.title.bn);
        $('#edit_parent_id').val(menu.parent_id == 0 ? '' : menu.parent_id).trigger('change');
        $('#edit_link_type').val(menu.link_type).trigger('change');
        $('#edit_link_target').val(menu.link_target).trigger('change');
        $('#edit_weight').val(menu.weight);
        $('#edit_link_path').val(menu.link_path);
        $('#edit_id').val(menu.id);
    }).on('click', '.btn-del', function () {
        // get id from data attribute
        var id = $(this).data('id');
        // sweetalert
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                // deleting menu item
                deleteMenuItem(id);
                // re rendering list
                renderList();
            }
        });
    }).on('click', '.btn-cancel', function () {
        // showing create form
        $('#createForm').show();
        // hiding edit form
        $('#editForm').hide();
    }).on('click', '.expand-all', function () {
        $('.dd').nestable('expandAll');
    }).on('click', '.collapse-all', function () {
        $('.dd').nestable('collapseAll');
    });
    // link path show/hide
    $('#link_type').on("change", function (e) {
        let text_link = '<label>Link Path</label>\n' +
            '<input type="text" id="content_ref_link_path" name="link_path" value="" class="form-control" />';
        let content_ref = '<label>Link Path</label>\n' +
            '<div>\n' +
            '    <input id="link_path" name="link_path" style="width: 70%; float: left;" type="text" value="" class="form-control" />\n' +
            '    <a style="width: 28%; float: right; text-align: center !important;" class="btn btn-default" role="button">Browse</a>\n' +
            '</div>';
        let link_type = $('#link_type').val();
        if (link_type == 1 || link_type == 2) {
            $('#link_path_holder').addClass('form-group dropdown-field span-full').html(text_link);
        } else if (link_type == 4) {
            $('#link_path_holder').addClass('form-group dropdown-field span-full').html(content_ref);
        }
    });
    // link path show/hide
    $('#edit_link_type').on("change", function (e) {
        let text_link = '<label>Link Path</label>\n' +
            '<input type="text" id="edit_link_path" name="link_path" value="" class="form-control" />';
        let content_ref = '<label>Link Path</label>\n' +
            '<div>\n' +
            '    <input id="edit_link_path" name="link_path" style="width: 70%; float: left;" type="text" value="" class="form-control" />\n' +
            '    <a style="width: 28%; float: right; text-align: center !important;" class="btn btn-default" role="button">Browse</a>\n' +
            '</div>';
        let link_type = $('#edit_link_type').val();
        if (link_type == 1 || link_type == 2) {
            $('#edit_link_path_holder').addClass('form-group dropdown-field span-full').html(text_link);
        } else if (link_type == 4) {
            $('#edit_link_path_holder').addClass('form-group dropdown-field span-full').html(content_ref);
        } else {
            $('#edit_link_path_holder').addClass('form-group dropdown-field span-full').html('');
        }
    });
    // drag n drop change
    $('.dd').on('change', function (e) {
        $.request('onUpdateParentChild', {
            data: { arr: JSON.stringify($('.dd').nestable('serialize')) },
            success: function () {
                renderList();
            }
        })
    });
});

// render parent menu
function renderParentMenu() {
    let arr = getMenuList();
    let root = this;
    let options = '<option value="">Select Parent Menu</option>';
    $.each(arr, function (key, value) {
        options += '<option value="' + value.id + '">' + value.title.bn + '</option>'
    });
    $('#parent_id').html(options).select2();
    $('#edit_parent_id').html(options).select2();
}

// rendering menu list
function renderList() {
    // get menu list
    var menuArr = getHierarchicalMenuList();
    // generating list
    var list = generateList(menuArr);
    // append list on page
    $("#menuList").html(list);
    // activating nestable list
    activateNestableList();
    // get render parent menu
    renderParentMenu();
}

// get menu list from api
function getMenuList() {
    // ajax setup for csrf token validation
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        async: false
    });
    // list array
    var list = [];
    // get menu
    $.request('onGetMenu', {
        success: function (data) {
            list = data.menus;
        }
    });
    return list;
}

// get menu list from api
function getHierarchicalMenuList() {
    // ajax setup for csrf token validation
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        async: false
    });
    // list array
    var list = [];
    // get menu
    $.request('onGetHierarchicalMenu', {
        success: function (data) {
            list = data.menus;
        }
    });
    return list;
}

// generating html list for displaying menu
function generateList(arr) {
    var list = $("<ol>").addClass("dd-list");
    $.each(arr, function (key, value) {
        var child = $("<li data-id=" + value.id + ">").addClass("dd-item dd3-item");
        var handle = $("<div>").addClass("dd-handle dd3-handle");
        var content = $("<div>").addClass("dd-content dd3-content").text(value.title.bn);
        list.append(child.append(handle).append(content).append("<a data-id=" + value.id + " class='btn-del btn-danger btn-sm pull-right' style='margin-top: -38px; z-index: 999999999; margin-right: 10px;'><i class='icon-trash-o'></i></a><a data-id=" + value.id + " class='btn-edit btn btn-warning btn-sm pull-right' style='margin-top: -38px; z-index: 999999999; margin-right: 60px;'><i class='icon-pencil-square-o'></i></a><a data-id=" + value.id + " class='btn-add btn btn-success btn-sm pull-right' style='margin-top: -38px; z-index: 999999999; margin-right: 115px;'><i class='icon-plus-square-o'></i></a>"));
        if (value["children"].length > 0) {
            child.append(generateList(value["children"]));
        }
    });
    return list;
}

// activate nestable list
function activateNestableList() {
    $('.dd').nestable('destroy').nestable({
        group: 'menu',
        maxDepth: 3,
    }).nestable('collapseAll');
}

// get update response
function updateMenuForm(data) {
    // re-render menulist
    renderList();
    // reset menu form
    resetMenuForm();
}

// get update edit
function updateMenuEditForm(data) {
    // re-render menulist
    renderList();
}

// reset menu form
function resetMenuForm() {
    $('.menuForm').trigger('reset');
}

// get menu item
function getMenuItem(id) {
    // ajax setup for csrf token validation
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        async: false
    });
    // item
    var item = '';
    // get menu
    $.request('onGetSingleMenu', {
        data: { id: id },
        success: function (data) {
            item = data.menu;
        }
    });
    return item;
}

// delete menu item
function deleteMenuItem(id) {
    $(this).request('onDeleteMenu', {
        data: {
            id: id
        }
    });
}