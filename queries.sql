# добавление пользователей
INSERT INTO
  users (registration, email, name, password)
VALUES
  (CURDATE(), 'ignat.v@gmail.com', 'Игнат', '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka'),
  (CURDATE(), 'kitty_93@li.ru', 'Леночка', '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa'),
  (CURDATE(), 'warrior07@mail.ru', 'Руслан', '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW');

# добавление проектов для пользователя id = 1
INSERT INTO
  projects (name, user_id)
VALUES
  ('Входящие', 1),
  ('Учеба', 1),
  ('Работа', 1),
  ('Домашние дела', 1),
  ('Авто', 1);

# добавление списка задач для пользователя id = 1
INSERT INTO
  tasks (created, completed, name, deadline, project_id, user_id)
VALUES
  (CURDATE(), NULL, 'Собеседование в IT компании', '2017-10-01', 3, 1),
  (CURDATE(), NULL, 'Выполнить тестовое задание', '2017-09-25', 3, 1),
  (CURDATE(), CURDATE(), 'Сделать задание первого раздела', '2017-09-21', 2, 1),
  (CURDATE(), NULL, 'Встреча с другом', '2017-09-21', 1, 1),
  (CURDATE(), NULL, 'Купить корм для кота', NULL, 4, 1),
  (CURDATE(), NULL, 'Заказать пиццу', NULL, 4, 1);

# получить список из всех проектов для одного пользователя
SELECT id, name FROM projects WHERE user_id = 1 ORDER BY id;

# получить список из всех задач для одного проекта
SELECT id, name, file_name, deadline, completed FROM tasks WHERE project_id = 3 ORDER BY deadline;

# пометить задачу как выполненную
UPDATE tasks SET completed = CURDATE() WHERE id = 1;

# получить все задачи для завтрашнего дня
SELECT id, name, file_name, deadline, completed FROM tasks WHERE deadline = CURDATE() + INTERVAL 1 DAY;

# обновить название задачи по её идентификатору
UPDATE tasks SET name = 'Новое название задачи' WHERE id = 6;
