<?php

function render_template($template, $data) {
    $result = '';

    if (file_exists($template)) {
        ob_start();
        require_once $template;
        $result = ob_get_clean();
    }

    return $result;
}

function get_count_tasks($task_list, $project_name) {
    $all_projects = 'Все';
    $result = 0;

    if (strtolower($project_name) == strtolower($all_projects)) {
        $result = count($task_list);
    } else {
        foreach ($task_list as $key => $value) {
            if (strtolower($value['category']) == strtolower($project_name)) {
                $result++;
            }
        }
    }

    return $result;
}

function filter_tasks($task_list, $project_name) {
    $all_projects = 'Все';
    $result = [];

    if (strtolower($project_name) == strtolower($all_projects)) {
        $result = $task_list;
    } else {
        foreach ($task_list as $key => $value) {
            if (strtolower($value['category']) == strtolower($project_name)) {
                array_push($result, $value);
            }
        }
    }

    return $result;
}

function validate_project($value) {
    return ($value != 0);
}

function validate_date($value) {
    return (strtotime($value) && ($value == date("d.m.Y", strtotime($value))));
}

function validate_email($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL);
}

function search_user_by_email ($email, $users) {
    $result = null;

    foreach ($users as $user) {
        if ($user['email'] == $email) {
            $result = $user;
            break;
        }
    }

    return $result;
}

function select_data($connect, $sql, $data = []) {
    $result = [];

    $stmt = db_get_prepare_stmt($connect, $sql, $data);
    if ($stmt && mysqli_stmt_execute($stmt)) {
        $result = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }

    return $result;
}

function insert_data($connect, $table, $data) {
    $result = false;

    $columns = array_keys($data);
    $values = array_values($data);
    $placeholders = array_fill(0, count($values), '?');

    $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $columns) .') VALUES (' . implode(', ', $placeholders) .')';

    $stmt = db_get_prepare_stmt($connect, $sql, $values);
    if ($stmt && mysqli_stmt_execute($stmt)) {
        $result = mysqli_insert_id($connect);
    }

    return $result;
}

function exec_query($connect, $sql, $data = []) {
    $stmt = db_get_prepare_stmt($connect, $sql, $data);

    return ($stmt && mysqli_stmt_execute($stmt));
}
?>
