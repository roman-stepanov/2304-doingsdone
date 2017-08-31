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

?>
