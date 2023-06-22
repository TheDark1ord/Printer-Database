<?php

# I do not really know how to do it more correctly
function getFirstMatch($select_querry_result) {
    foreach ($select_querry_result as $match) {
        return $match;
    }
}

# Test if all of the required attributes provided by the $fields variable,
# which is an array of strings, are present in the json $data variable and not null
function testRequired($data, $fields): bool {
    foreach($fields as $field) {
        if (!array_key_exists($field, $data) or $data[$field] == null) {
            return false;
        }
    }
    return true;
}

?>