<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="post">
    <input class="search-form__input" type="text" name="" value="" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <div class="radio-button-group">
        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio" checked="">
            <span class="radio-button__text">Все задачи</span>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio">
            <span class="radio-button__text">Повестка дня</span>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio">
            <span class="radio-button__text">Завтра</span>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio">
            <span class="radio-button__text">Просроченные</span>
        </label>
    </div>

    <label class="checkbox">
        <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
        <input id="show-complete-tasks" class="checkbox__input visually-hidden" type="checkbox" <?= ($data['show_complete_tasks'] == 1) ? 'checked' : ''; ?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <?php foreach ($data['tasks'] as $key => $value): ?>
        <?php if (!$value['completed'] || $data['show_complete_tasks'] == 1): ?>
            <tr class="tasks__item task <?= ($value['completed']) ? 'task--completed' : '';?>">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden" type="checkbox" <?= ($value['completed']) ? 'checked' : '';?> disabled>
                        <span class="checkbox__text"><?= htmlspecialchars($value['name']); ?></span>
                    </label>
                </td>

                <td class="task__date">
                    <?= ($value['deadline']) ? htmlspecialchars(date('d.m.Y', strtotime($value['deadline']))) : 'Нет'; ?>

                </td>

                <td class="task__controls">
                    <?php if (!$value['completed']): ?>
                        <button class="expand-control" type="button" name="button"><?= htmlspecialchars($value['name']); ?></button>

                        <ul class="expand-list hidden">
                            <li class="expand-list__item">
                                <a href="/index.php?complete_task=<?= $value['id']; ?>">Выполнить</a>
                            </li>

                            <li class="expand-list__item">
                                <a href="/index.php?delete_task=<?= $value['id']; ?>">Удалить</a>
                            </li>
                        </ul>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>
