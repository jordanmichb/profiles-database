/* Validate login with JS as well as PHP */
function validateLogin() {
    try {
        email = document.querySelector('#email').value;
        pw = document.querySelector('#pass').value;
        if (pw == null || pw == "" || email == null || email == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if (!email.includes('@')) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
}



/* Event for adding new positions when creating or editing a profile */
let posCount = 0; // Keep track of positions
$('.add_pos').click(function() {
    // Max nine positions
    const count = $('.positions').children().length;
    if (count >= 9) {
        alert('Maximum 9 positions');
        return;
    }
    // For edit page. If adding positions when some already exist,
    // count needs to be adjusted to correct number
    if (posCount == 0 && count != 0) {
        posCount = count;
    }

    posCount++;
    $('.positions').append(
        '<div class="position" id="position' + posCount + '"> \
            <label for="pos_year' + posCount + '">Year: </label>\
            <input type="text" id="pos_year' + posCount + '" name="pos_year' + posCount + '">\
            <label for="pos_desc' + posCount + '">Description: </label>\
            <textarea id="pos_desc' + posCount + '" name="pos_desc' + posCount + '" rows="6"></textarea>\
            <button id="del_pos' + posCount + '" class="del_pos" type="button">Delete</button>\
        </div>'
    )
    // Event for deleting position
    $('#del_pos' + posCount).click((e) => deletePosition(e));
});

$('.del_pos').click((e) => deletePosition(e));

function deletePosition(e) {
    const id = e.target.id.substring(7);
    $('#position' + id).remove();
};



/* Event for adding new education when creating or editing a profile */
let eduCount = 0; // Keep track of education
$('.add_edu').click(function() {
    // Max nine education
    const count = $('.educations').children().length;
    if (count >= 9) {
        alert('Maximum 9 education');
        return;
    }
    // For edit page. If adding education when some already exist,
    // count needs to be adjusted to correct number
    if (eduCount == 0 && count != 0) {
        eduCount = count;
    }

    eduCount++;
    $('.educations').append(
        '<div class="education" id="education' + eduCount + '"> \
            <label for="edu_year' + eduCount + '">Year: </label>\
            <input type="text" id="edu_year' + eduCount + '" name="edu_year' + eduCount + '">\
            <label for="edu_school' + eduCount + '">School: </label>\
            <input type="text" id="edu_school' + eduCount + '" name="edu_school' + eduCount + '" class="school">\
            <button id="del_edu' + eduCount + '" class="del_edu" type="button">Delete</button>\
        </div>'
    )
    // Event for deleting position, add for new entries
    $('#del_edu' + eduCount).click((e) => deleteEducation(e));

    // Add autocomplete to new entries
    $('.school').autocomplete({
        source: "school.php"
    });
});

// Event for deleting position, for existing entries
$('.del_edu').click((e) => deleteEducation(e));

function deleteEducation(e) {
    const id = e.target.id.substring(7);
    $('#education' + id).remove();
};

// Add autocomplete to existing entries
$('.school').autocomplete({
    source: "school.php"
});