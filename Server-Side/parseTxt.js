function txtParse(txt, level) {
    txt = txt.replace(/"/g, '%double_quote%');
    if (level > 1) {
        txt = txt.replace(/'/g, '%single_quote%');
        txt = txt.replace(/:/g, '%colon%');
        txt = txt.replace(/;/g, '%pcolon%');
        txt = txt.replace(/-/g, '%hyphon%');
        txt = txt.replace(/,/g, '%comma%');
        txt = txt.replace(/\[/g, '%sqbrak_left%');
        txt = txt.replace(/]/g, '%sqbrak_right%');

    }
    return txt;
}

function txtUnparse(txt) {
    txt = txt.replace(/%double_quote%/g, '"');
    txt = txt.replace(/%single_quote%/g, "'");
    txt = txt.replace(/%colon%/g, ':');
    txt = txt.replace(/%pcolon%/g, ';');
    txt = txt.replace(/%hyphon%/g, '-');
    txt = txt.replace(/%comma%/g, ',');
    txt = txt.replace(/%sqbrak_left%/g, '[');
    txt = txt.replace(/%sqbrak_right%/g, ']');
    txt = txt.replace(/%qbrak_left%/g, '{');
    txt = txt.replace(/%qbrak_right%/g, '}');
    return txt;
}
