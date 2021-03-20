// validate form values

// return true if valid, false if invalid
function isValidUsername(username) {
    // 3 - 24 chars
    // any alphanum
    // no special chars excepts "-" and "_"
    var regex = new RegExp(/^[\w-]{3,24}$/);

    return regex.test(username);
}

// return true if valid, false if invalid
// regex from: https://digitalfortress.tech/tricks/top-15-commonly-used-regex/
function isValidPassword(password) {
    // at least 1 lowercase
    // at least 1 uppercase
    // at least 1 number
    // at least 8 chars long
    // special chars allowed
    var regex = new RegExp(/(?=(.*[0-9]))((?=.*[A-Za-z0-9])(?=.*[A-Z])(?=.*[a-z]))^.{8,}$/);

    return regex.test(password);
}