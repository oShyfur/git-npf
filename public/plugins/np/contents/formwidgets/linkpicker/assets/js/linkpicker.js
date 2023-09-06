/*
 * This is a sample JavaScript file used by linkPicker
 *
 * You can delete this file if you want
 */


function makePopup($el, $model, $containerid, $id, $name) {
    console.log($name);
    $slug = $($el).find('input').val();
    $sessionKey = $($el).closest('form').find("input[name=_session_key]").val();
    $token = $($el).closest('form').find("input[name=_token]").val();
    $.request('onSelectContent', {
        success: function (data) {
            $('#' + data.element).empty().html(data.value);
            //console.log(data);
        },
        data: { slug: $slug, model: $model, containerid: $containerid, id: $id, name: $name, _session_key: $sessionKey, _token: $token }
    })
    $('.modal').trigger('close.oc.popup');
    return false;


}