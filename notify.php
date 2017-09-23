<?php
require_once 'vendor/autoload.php';
require_once 'functions.php';
require_once 'init.php';

$sql_users = 'SELECT u.id, u.email, u.name '.
    'FROM users u JOIN tasks t ON u.id = t.user_id '.
    'WHERE t.completed IS NULL AND t.deadline BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 HOUR) '.
    'GROUP BY u.id';
$sql_tasks = 'SELECT name, TIME_FORMAT(deadline, "%H:%i") AS task_time '.
    'FROM tasks '.
    'WHERE completed IS NULL AND deadline BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 1 HOUR) AND user_id = ?';

$users = select_data($connect, $sql_users);
if ($users) {
    $transport = new Swift_SmtpTransport('smtp.mail.ru', 465, 'ssl');
    $transport->setUsername('doingsdone@mail.ru');
    $transport->setPassword('rds7BgcL');

    $mailer = new Swift_Mailer($transport);

    $message = new Swift_Message();
    $message->setFrom(['doingsdone@mail.ru' => 'doingsdone']);
    $message->setSubject('Уведомление от сервиса «Дела в порядке»');

    foreach ($users as $user) {
        $message_text = "Уважаемый, " . $user['name'] . ". У вас запланирована задача:\n";

        $tasks = select_data($connect, $sql_tasks, [$user['id']]);
        foreach ($tasks as $task) {
            $message_text .= "\t" . $task['name'] . " на " . $task['task_time'] . "\n";
        }

        $message->setTo([$user['email'] => $user['name']]);
        $message->setBody($message_text, 'text/plain');

        $mailer->send($message);
    }
}
