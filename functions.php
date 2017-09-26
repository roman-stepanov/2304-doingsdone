<?php
require_once 'mysql_helper.php';

/**
 * Подключает шаблон и передает в него данные
 *
 * @param string $template Путь к файлу шаблона
 * @param array $data Данные для шаблона
 *
 * @return string HTML-код шаблона с подставленными данными
 */
function render_template($template, $data = []) {
    $result = '';

    if (file_exists($template)) {
        ob_start();
        require_once $template;
        $result = ob_get_clean();
    }

    return $result;
}

/**
 * Определяет просрочена дата задачи или нет
 *
 * @param string $date Дата задачи
 *
 * @return boolean Результат выполнения
 */
function is_date_expired($date) {
    $seconds_per_day = 86400;
    $current_ts = strtotime('now midnight');
    $days_until_deadline = 0;

    if ($date) {
        $deadline_ts = strtotime($date);
        $days_until_deadline = ($deadline_ts - $current_ts) / $seconds_per_day;
    }

    return ($days_until_deadline < 0);
}

/**
 * Переводит дату задчи из "человеческого формата" в относительный формат
 *
 * @param string $date Дата задачи
 *
 * @return string Дата в относительном формате
 */
function convert_human_date($date) {
    $patterns = [
        '/сегодня/',
        '/после/',
        '/завтра/',
        '/ в /',
        '/понедельник/',
        '/вторник/',
        '/среда/',
        '/четверг/',
        '/пятница/',
        '/суббота/',
        '/воскресенье/'
    ];
    $replacements = [
        'today',
        '+1 day',
        '+1 day',
        ' ',
        'next monday',
        'next tuesday',
        'next wednesday',
        'next thursday',
        'next friday',
        'next saturday',
        'next sunday'
    ];

    return preg_replace($patterns, $replacements, mb_strtolower($date));
}

/**
 * Проверяет обязательные поля формы отправленные через POST
 *
 * @param array $post массив $_POST
 * @param array $required Массив с именами полей для проверки
 *
 * @return array Массив с именами полей, которые не прошли проверку
 */
function check_required_fields($post, $required) {
    $result = [];

    foreach($post as $key => $value) {
        if (in_array($key, $required) && $value === '') {
            $result[$key] = 'Заполните это поле';
        }
    }

    return $result;
}

/**
 * Проверяет, что значение является корректным e-mail
 *
 * @param string $email Email пользователя
 *
 * @return string Email
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Получение данных из БД
 *
 * @param mysqli $connect Ресурс соединения
 * @param string $sql SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return array Массив с данными
 */
function select_data($connect, $sql, $data = []) {
    $result = [];

    $stmt = db_get_prepare_stmt($connect, $sql, $data);
    if ($stmt && mysqli_stmt_execute($stmt)) {
        $result = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }

    return $result;
}

/**
 * Вставка данных в таблицу БД
 *
 * @param mysqli $connect Ресурс соединения
 * @param string $sql Имя таблицы, в которую добавляются данные
 * @param array $data Ассоциативный массив, где ключи - имена полей, а значения - значения полей таблицы
 *
 * @return integer Идентификатор последней добавленной записи
 */
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

/**
 * Выполняет произвольный запрос БД
 *
 * @param mysqli $connect Ресурс соединения
 * @param string $sql SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return boolean Результат выполнения запроса
 */
function exec_query($connect, $sql, $data = []) {
    $stmt = db_get_prepare_stmt($connect, $sql, $data);

    return ($stmt && mysqli_stmt_execute($stmt));
}

/**
 * Получение списка проектов пользователя со счетчиками задач
 *
 * @param mysqli $connect Ресурс соединения
 * @param integer $user_id  Идентификатор пользователя
 *
 * @return array Массив с названиеми проектов и колиеством задач
 */
function get_list_projects($connect, $user_id) {
    $result = select_data(
        $connect,
        'SELECT projects.id, projects.name, COUNT(tasks.id) AS count_tasks '.
            'FROM projects LEFT JOIN tasks ON projects.id = tasks.project_id '.
            'WHERE projects.user_id = ? GROUP BY projects.id',
        [$user_id]
    );

    if ($result) {
        $count_all_tasks = select_data(
            $connect,
            'SELECT COUNT(tasks.id) AS count_tasks FROM tasks WHERE user_id = ? GROUP BY user_id',
            [$user_id]
        );

        array_unshift($result, [
            'id' => 0,
            'name' => 'Все',
            'count_tasks' => $count_all_tasks ? $count_all_tasks[0]['count_tasks'] : 0
        ]);
    }

    return $result;
}

/**
 * Находит проект пользователя по названию
 *
 * @param mysqli $connect Ресурс соединения
 * @param integer $user_id Идентификатор пользователя
 * @param string $project_name Название проекта
 *
 * @return array Данные о проекте
 */
function get_project($connect, $user_id, $project_name) {
    $sql = 'SELECT * FROM projects WHERE user_id = ? AND name = ?';
    $result = current(select_data($connect, $sql, [$user_id, $project_name]));

    return $result;
}

/**
 * Находит проект по идентификатору
 *
 * @param mysqli $connect Ресурс соединения
 * @param integer $user_id Идентификатор проекта
 *
 * @return array Данные о проекте
 */
function get_project_by_id($connect, $project_id) {
    $sql = 'SELECT * FROM projects WHERE id = ?';
    $result = current(select_data($connect, $sql, [$project_id]));

    return $result;
};
