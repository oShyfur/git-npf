// Function to update child select options based on selected parent
function updateChildSelect() {
    var selectedMinistry = $("#Form-field-Site-ministry").val();
    // Replace 'your-plugin' with the correct plugin or module name
    $.ajax({
        url: "/getDirectorates", // Replace 'your-plugin' with the correct plugin or module name
        method: "GET",
        data: { ministry_id: selectedMinistry },
        success: function (response) {
            var directorateSelect = $("#Form-field-Site-directorate");
            var emptyOption = "Select Line Directorate";

            // Clear the current options and add the empty option
            directorateSelect.empty();
            directorateSelect.append(
                '<option value="">' + emptyOption + "</option>"
            );

            // Add the new options
            $.each(response, function (index, directorate) {
                directorateSelect.append(
                    '<option value="' +
                        directorate.id +
                        '">' +
                        directorate.name +
                        "</option>"
                );
            });
        },
        error: function (xhr) {
            // Handle error if needed
            console.error(xhr);
        },
    });
}

// Call the function whenever the parent select changes
$("#Form-field-Site-ministry").on("change", function () {
    updateChildSelect();
});

// Call the function on initial page load
// updateChildSelect();
// '['+directorate.subdomain +']'+
