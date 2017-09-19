<?php
session_start();

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

require_once('functions.php');
require_once('mysql_helper.php');
require_once('init.php');

$content_data = [
    'tasks' => [],
    'show_complete_tasks' => false
];

$register_data = [
    'required' => ['email', 'password', 'name'],
    'errors' => []
];

$new_task_data = [
    'required' => ['name', 'project', 'date'],
    'errors' => [],
    'projects' => []
];

$login_data = [
    'required' => ['email', 'password'],
    'errors' => []
];

$layout_data = [
    'title' => 'Дела в Порядке!',
    'body-background' => false,
    'page-header' => true,
    'sidebar' => false,
    'user' => false,
    'projects' => [],
    'active_project' => 0,
    'tasks' => [],
    'content' => '',
    'modal' => false
];

if (($_SERVER['REQUEST_METHOD'] == 'POST') && !empty($_POST)) {

    if (isset($_POST['new-task']) && isset($_SESSION['user'])) {
        foreach($_POST as $key => $value) {
            if (in_array($key, $new_task_data['required']) && $value == '') {
                $new_task_data['errors'][$key] = 'Заполните это поле';
            }
        }

        if (!validate_project($_POST['project'])) {
            $new_task_data['errors']['project'] = 'Веберите проект';
        }

        if (!validate_date($_POST['date'])) {
            $new_task_data['errors']['date'] = 'Дата должна быть в формате ДД.ММ.ГГГГ и больше либо равна текущей датe';
        }

        if (!count($new_task_data['errors'])) {
            $new_task = [
                'created' => date('Y-m-d'),
                'name' => $_POST['name'],
                'file_name' => null,
                'deadline' => date('Y-m-d', strtotime($_POST['date'])),
                'project_id' => (int)$_POST['project'],
                'user_id' => $_SESSION['user']['id']
            ];

            if (isset($_FILES['preview'])) {
                $file_path = __DIR__ . '\\';
                $file_name = $_FILES['preview']['name'];
                $file_tmp_name = $_FILES['preview']['tmp_name'];
                move_uploaded_file($file_tmp_name, $file_path . $file_name);
                $new_task['file_name'] = $file_name;
            }

            insert_data($connect, 'tasks', $new_task);
            header("Location: /index.php");
        }
    }

    if (isset($_POST['register'])) {
        foreach($_POST as $key => $value) {
            if (in_array($key, $register_data['required']) && $value == '') {
                $register_data['errors'][$key] = 'Заполните это поле';
            }
        }

        if (!validate_email($_POST['email'])) {
            $register_data['errors']['email'] = 'E-mail введён некорректно';
        } else {
            if (select_data($connect, 'SELECT * FROM users WHERE email = ?', [$_POST['email']])) {
                $register_data['errors']['email'] = 'E-mail уже зарегистрирован';
            }
        }

        if (!count($register_data['errors'])) {
            $new_user = [
                'registration' => date('Y-m-d'),
                'email' => $_POST['email'],
                'name' => $_POST['name'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
            ];
            insert_data($connect, 'users', $new_user);
            $_SESSION['register_complete'] = true;
            header("Location: /index.php?login");
        }
    }

    if (isset($_POST['login'])) {
        foreach($_POST as $key => $value) {
            if (in_array($key, $login_data['required']) && $value == '') {
                $login_data['errors'][$key] = 'Заполните это поле';
            }
        }

        if (!validate_email($_POST['email'])) {
            $login_data['errors']['email'] = 'E-mail введён некорректно';
        }

        if (!count($login_data['errors'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = current(select_data($connect, 'SELECT * FROM users WHERE email = ?', [$email]));

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                header("Location: /index.php");
            } else {
                $login_data['errors']['message'] = 'Вы ввели неверный email/пароль';
            }
        }
    }
}

if (isset($_SESSION['user'])) {
    $projects = select_data($connect, 'SELECT id, name FROM projects WHERE user_id = ? ORDER BY id', [$_SESSION['user']['id']]);
    array_unshift($projects, ['id' => 0, 'name' => 'Все' ]);
    $tasks = select_data($connect, 'SELECT * FROM tasks WHERE user_id = ?', [$_SESSION['user']['id']]);

    $project = (int)$_GET['project'] ?? 0;
    if (in_array($project, array_column($projects, 'id'))) {
        $content_data['tasks'] = filter_tasks($tasks, $project);
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

    $layout_data['sidebar'] = true;
    $layout_data['user'] = $_SESSION['user'];
    $layout_data['projects'] = $projects;
    $layout_data['active_project'] = $project;
    $layout_data['tasks'] = $tasks;
    $layout_data['content'] = render_template('templates/index.php', $content_data);

    if (isset($_GET['add']) || count($new_task_data['errors'])) {
        $new_task_data['projects'] = $projects;
        $layout_data['modal'] = render_template('templates/new-task.php', $new_task_data);
    }

    if (isset($_GET['complete_task'])) {
        $task_id = $_GET['complete_task'];
        $sql = 'UPDATE tasks SET completed = CURDATE() WHERE id = ?';
        if (exec_query($connect, $sql, [$task_id])) {
            header("Location: /index.php");
        }
    }

    if (isset($_GET['delete_task'])) {
        $task_id = $_GET['delete_task'];
        $sql = 'DELETE FROM tasks WHERE id = ?';
        if (exec_query($connect, $sql, [$task_id])) {
            header("Location: /index.php");
        }
    }
} else {
    $layout_data['body-background'] = true;
    $layout_data['content'] = render_template('templates/guest.php');

    if (isset($_GET['register']) || count($register_data['errors'])) {
        $layout_data['body-background'] = false;
        $layout_data['page-header'] = false;
        $layout_data['sidebar'] = true;
        $layout_data['content'] = render_template('templates/register.php', $register_data);
    }

    if (isset($_GET['login']) || count($login_data['errors'])) {
        if (isset($_SESSION['register_complete'])) {
            $login_data['errors']['message'] = 'Теперь вы можете войти, используя свой email и пароль';
            unset($_SESSION['register_complete']);
        }
        $layout_data['modal'] = render_template('templates/login.php', $login_data);
    }
}

$layout = render_template('templates/layout.php', $layout_data);

print($layout);
?>
