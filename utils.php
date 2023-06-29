<?php
use Laminas\Diactoros\Response\JsonResponse;
use Rakit\Validation\Validation;

# I do not really know how to do it more correctly
function getFirstMatch($select_querry_result) {
    foreach ($select_querry_result as $match) {
        return $match;
    }
}
?>