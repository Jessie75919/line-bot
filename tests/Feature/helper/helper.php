<?php



function create($class, $count = 1, $attr = [])
{
    if ($count !== 1) {
        return factory($class, $count)->create($attr);
    }
    return factory($class)->create($attr);
}

function make($class, $count = 1, $attr = [])
{
    if ($count !== 1) {
        return factory($class, $count)->make($attr);
    }
    return factory($class)->make($attr);
}