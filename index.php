<?php
session_start();

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

$days = rand(-3, 3);
$task_deadline_ts = strtotime("+" . $days . " day midnight"); // метка времени даты выполнения задачи
$current_ts = strtotime('now midnight'); // текущая метка времени

// запишите сюда дату выполнения задачи в формате дд.мм.гггг
$date_deadline = date("d.m.Y", $task_deadline_ts);

$seconds_per_day = 86400;
// в эту переменную запишите кол-во дней до даты задачи
$days_until_deadline = ($task_deadline_ts - $current_ts) / $seconds_per_day;

$projects = ['Все', 'Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasks = [
    [
        'title' => 'Собеседование в IT компании',
        'date' => '01.06.2018',
        'category' => 'Работа',
        'completed' => false
    ],
    [
        'title' => 'Выполнить тестовое задание',
        'date' => '25.05.2018',
        'category' => 'Работа',
        'completed' => false
    ],
    [
        'title' => 'Сделать задание первого раздела',
        'date' => '21.04.2018',
        'category' => 'Учеба',
        'completed' => true
    ],
    [
        'title' => 'Встреча с дрyгом',
        'date' => '22.04.2018',
        'category' => 'Входящие',
        'completed' => false
    ],
    [
        'title' => 'Купить корм для кота',
        'date' => false,
        'category' => 'Домашние дела',
        'completed' => false
    ],
    [
        'title' => 'Заказать пиццу',
        'date' => false,
        'category' => 'Домашние дела',
        'completed' => false
    ]
];

require_once('functions.php');
require_once('init.php');
require_once('userdata.php');

$content_data = [
    'tasks' => [],
    'show_complete_tasks' => false
];

$new_task_data = [
    'errors' => [],
    'projects' => $projects
];

$login_data = [
    'errors' => []
];

$layout_data = [
    'title' => 'Дела в Порядке!',
    'user' => false,
    'projects' => [],
    'active_project' => 0,
    'tasks' => [],
    'content' => '',
    'modal' => false
];

if (($_SERVER['REQUEST_METHOD'] == 'POST') && !empty($_POST)) {

    if (isset($_POST['task'])) {
        $required  = ['name', 'project', 'date'];
        $rules = [
            'project' => 'validate_project',
            'date' => 'validate_date'
        ];

        foreach ($_POST as $key => $value) {
            if (in_array($key, $required) && $value == '') {
                $new_task_data['errors'][] = $key;
            }

            if (array_key_exists($key, $rules) && !call_user_func($rules[$key], $value)) {
                $new_task_data['errors'][] = $key;
            }
        }

        if (!count($new_task_data['errors'])) {
            $new_task = [
                'title' => $_POST['name'],
                'date' => $_POST['date'],
                'category' => $projects[$_POST['project']],
                'completed' => false
            ];
            array_unshift($tasks, $new_task);

            if (isset($_FILES['preview'])) {
                $file_path = __DIR__ . '\\';
                $file_name = $_FILES['preview']['name'];
                $file_tmp_name = $_FILES['preview']['tmp_name'];
                move_uploaded_file($file_tmp_name, $file_path . $file_name);
            }
        }
    }

    if (isset($_POST['login'])) {
        $required  = ['email', 'password'];
        $rules = [
            'email' => 'validate_email'
        ];

        foreach($_POST as $key => $value) {
            if (in_array($key, $required) && $value == '') {
                $login_data['errors'][] = $key;
            }

            if (array_key_exists($key, $rules) && !call_user_func($rules[$key], $value)) {
                $login_data['errors'][] = $key;
            }
        }

        if (!count($login_data['errors'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = search_user_by_email($email, $users);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                header("Location: /index.php");
            } else {
                $login_data['errors'][] = 'password';
            }
        }
    }
}

if (isset($_SESSION['user'])) {
    $project = (int)$_GET['project'] ?? 0;
    if (array_key_exists($project, $projects)) {
        $content_data['tasks'] = filter_tasks($tasks, $projects[$project]);
    } else {
        http_response_code(404);
    }

    if (isset($_GET['show_completed'])) {
        setcookie('show_completed', $_GET['show_completed'], strtotime('+3 days'), '/');
        header("Location: /index.php");
    }
    if (isset($_COOKIE['show_completed']) && ($_COOKIE['show_completed'] == 1)) {
        $content_data['show_complete_tasks'] = true;
    }

    $layout_data['user'] = $_SESSION['user'];
    $layout_data['projects'] = $projects;
    $layout_data['active_project'] = $project;
    $layout_data['tasks'] = $tasks;
    $layout_data['content'] = render_template('templates/index.php', $content_data);

    if (isset($_GET['add']) || count($new_task_data['errors'])) {
        $new_task_data['projects'] = $projects;
        $layout_data['modal'] = render_template('templates/new-task.php', $new_task_data);
    }
} else {
    $layout_data['content'] = render_template('templates/guest.php', []);

    if (isset($_GET['login']) || count($login_data['errors'])) {
        $layout_data['modal'] = render_template('templates/login.php', $login_data);
    }
}

$layout = render_template('templates/layout.php', $layout_data);

print($layout);
?>
