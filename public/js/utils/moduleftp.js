function getFile() {
    document.getElementById('hiddenfile').click();
}

function getFileValue() {
    jQuery('#selectedfile').val(jQuery('#hiddenfile').val());
}