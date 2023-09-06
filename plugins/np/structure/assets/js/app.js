$(document).ready(function () {


    // for sso at user add/update page
    $('body').on('change', '#Form-field-User-_employee', function () {

        $id = $(this).val();

        $.request('onGetOisfEmployee', {
            data: { employee_id: $id },
            success: function (data) {

                $.each(data.employee, function (field, value) {

                    console.log(field + '-' + value);

                    $el = '#Form-field-User-' + field;
                    if ($($el).length)
                        $($el).val(value);

                });

            }
        });
    });

    // for sso at user add/update page
    $('body').on('change', '#Form-field-Menu-link_type', function () {
        //alert($("#Form-field-Menu-link_type").val());
        if($("#Form-field-Menu-link_type").val()=='external'){
            $(".input-group-btn .find-record").hide();
            $('.link-picker .input-group').css("width", "100%");
        }
        if($("#Form-field-Menu-link_type").val()=='contentref'){
            $(".input-group-btn .find-record").show();
            $('.link-picker .input-group').css("width", "auto");
        }
        //contentref
    });



    //setJsonEditor("Form-field-ContentType-config-group");

});

// $('#LinkPicker-formLinkPath-link_path').('click', function() {
//     alert('here');
//     //editorEl.codeEditor('setTheme', $(this).val())
// })

function setJsonEditor($id) {


    // create the editor
    container = document.getElementById($id)
    options = {}
    editor = new JSONEditor(container, options)

    // get json
    updatedJson = editor.get()
}