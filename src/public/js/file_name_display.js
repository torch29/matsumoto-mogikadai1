document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('img_path');
    const fileNameDisplay = document.getElementById('selectedFileName');

    fileInput.addEventListener('change', function () {
        if (fileInput.files.length > 0) {
            fileNameDisplay.textContent = fileInput.files[0].name;
        } else {
            fileNameDisplay.textContent = '';
        }
    });
});