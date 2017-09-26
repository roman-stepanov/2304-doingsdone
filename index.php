<?php
error_reporting(E_ALL);
session_start();

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

require_once 'vendor/autoload.php';
require_once 'functions.php';
require_once 'mysql_helper.php';
require_once 'init.php';

$content_data = [
    'search' => '',
    'active_project' => 0,
    'deadline' => false,
    'show_complete_tasks' => false,
    'tasks' => []
];

$register_data = [
    'required' => ['email', 'password', 'name'],
    'errors' => []
];

$new_project_data = [
    'required' => ['name'],
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
    'content' => '',
    'modal' => false
];

if (($_SERVER['REQUEST_METHOD'] === 'POST') && !empty($_POST)) {

    if (isset($_POST['new-project']) && isset($_SESSION['user']['id'])) {
        $new_project_data['errors'] = check_required_fields($_POST, $new_project_data['required']);

        if (get_project($connect, $_SESSION['user']['id'], $_POST['name'])) {
            $new_project_data['errors']['name'] = 'Такой проект уже существует';
        }

        if (!count($new_project_data['errors'])) {
            $new_project = [
                'name' => $_POST['name'],
                'user_id' => $_SESSION['user']['id']
            ];

            insert_data($connect, 'projects', $new_project);
            header('Location: /index.php');
        }
    }

    if (isset($_POST['new-task']) && isset($_SESSION['user']['id'])) {
        $new_task_data['errors'] = check_required_fields($_POST, $new_task_data['required']);

        if ($_POST['project'] && !get_project_by_id($connect, $_POST['project'])) {
            $new_task_data['errors']['project'] = 'Проект не существует';
        }

        $new_task_deadline = strtotime(convert_human_date($_POST['date']));
        if (!$new_task_deadline) {
            $new_task_data['errors']['date'] = 'Неверный формат даты';
        } elseif ($new_task_deadline < strtotime(date('d.m.Y'))) {
            $new_task_data['errors']['date'] = 'Дата должна быть больше либо равна текущей датe';
        }

        if (!count($new_task_data['errors'])) {
            $new_task = [
                'created' => date('Y-m-d H:i'),
                'name' => $_POST['name'],
                'file_name' => null,
                'deadline' => date('Y-m-d H:i', $new_task_deadline),
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
            header('Location: /index.php');
        }
    }

    if (isset($_POST['register'])) {
        $register_data['errors'] = check_required_fields($_POST, $register_data['required']);

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
            header('Location: /index.php?login');
        }
    }

    if (isset($_POST['login'])) {
        $login_data['errors'] = check_required_fields($_POST, $login_data['required']);

        if (!validate_email($_POST['email'])) {
            $login_data['errors']['email'] = 'E-mail введён некорректно';
        }

        if (!count($login_data['errors'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = current(select_data($connect, 'SELECT * FROM users WHERE email = ?', [$email]));

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user;
                header('Location: /index.php');
            } else {
                $login_data['errors']['message'] = 'Вы ввели неверный email/пароль';
            }
        } else {
            $login_data['errors']['message'] = 'Пожалуйста, исправьте ошибки в форме';
        }
    }
}

if (isset($_SESSION['user']['id'])) {
    $projects = get_list_projects($connect, $_SESSION['user']['id']);

    $search = $_GET['search'] ?? '';
    $project = $_GET['project'] ?? 0;
    $deadline = $_GET['deadline'] ?? 0;

    if (in_array($project, array_column($projects, 'id'))) {
        $sql = 'SELECT *, DATE_FORMAT(deadline, "%d.%m.%Y") AS deadline_format FROM tasks WHERE user_id = ?';
        $sql_params[] = $_SESSION['user']['id'];

        if ($project) {
            $sql .= ' AND project_id = ?';
            $sql_params[] = $project;
        }

        $search = trim($search);
        if ($search) {
            $sql .= ' AND name LIKE ?';
            $sql_params[] = '%' . $search . '%';
        }

        switch ($deadline) {
            case 1:
                $sql .= ' AND DATE(deadline) = CURDATE()';
                break;
            case 2:
                $sql .= ' AND DATE(deadline) = CURDATE() + INTERVAL 1 DAY';
                break;
            case 3:
                $sql .= ' AND completed IS NULL AND DATE(deadline) < CURDATE()';
                break;
        }

        $content_data['search'] = $search;
        $content_data['active_project'] = (int)$project;
        $content_data['deadline'] = (int)$deadline;
        $content_data['tasks'] = select_data($connect, $sql, $sql_params);
    } else {
        http_response_code(404);
    }

    if (isset($_GET['show_completed'])) {
        setcookie('show_completed', $_GET['show_completed'], strtotime('+3 days'), '/');
        header('Location: /index.php');
    }
    if (isset($_COOKIE['show_completed']) && ($_COOKIE['show_completed'] === '1')) {
        $content_data['show_complete_tasks'] = true;
    }

    $layout_data['sidebar'] = true;
    $layout_data['user'] = $_SESSION['user'];
    $layout_data['projects'] = $projects;
    $layout_data['active_project'] = (int)$project;
    $layout_data['content'] = render_template('templates/index.php', $content_data);

    if (isset($_GET['new_project']) || count($new_project_data['errors'])) {
        $layout_data['modal'] = render_template('templates/new-project.php', $new_project_data);
    }

    if (isset($_GET['add']) || count($new_task_data['errors'])) {
        array_shift($projects);
        array_unshift($projects, [
            'id' => '',
            'name' => '<Выберите проект>'
        ]);
        $new_task_data['projects'] = $projects;
        $layout_data['modal'] = render_template('templates/new-task.php', $new_task_data);
    }

    if (isset($_GET['task'])) {
        $task_id = (int)$_GET['task'];
        $sql = '';

        if (isset($_GET['complete'])) {
            $sql = 'UPDATE tasks SET completed = ';
            $sql .= ($_GET['complete']) ?  'CURRENT_TIMESTAMP()' : 'NULL';
            $sql .= ' WHERE id = ?';
        }

        if (isset($_GET['delete'])) {
            $sql = 'DELETE FROM tasks WHERE id = ?';
        }

        if ($sql && exec_query($connect, $sql, [$task_id])) {
            header('Location: /index.php');
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
