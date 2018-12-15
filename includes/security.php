<?php

// Generate random string
function gen($length)
{
    $length = $length / 2;
    return bin2hex(random_bytes($length));
}


// Get expire time in unix format
function expires($added)
{
    return time() + $added;
}

#################
# Validate data #
#################

// Validate data is set
function is_empty($var, $type ='Unknown', $frontEnd = false, $redirectTo = null)
{
    if (empty($var)) {
        if ($frontEnd) {
            redirect($redirectTo, "{$type} is empty.");
        } else {
            $type = strtolower(trim($type));
            response(false, "{$type}_empty");
        }
    }
}


// Clean data
function clean_data($data)
{
    $conn = sql_connect();
    $data = $conn->escape_string($data);
    sql_disconnect($conn);

    $data = trim($data);
    $data = htmlspecialchars($data);
    $data = stripslashes($data);

    return $data;
}


// Check data
function check_data($data, $isEmpty = true, $isEmptyType = 'Unknown', $clean = true, $frontEnd = false, $redirectTo = null)
{
    if ($isEmpty) {
        is_empty($data, $isEmptyType, $frontEnd, $redirectTo);
    }

    if ($clean) {
        return clean_data($data);
    } else {
        return $data;
    }
}
