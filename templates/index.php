<h2 class="content__main-heading">Список задач</h2>

<form class="search-form" action="index.php" method="get">
    <input class="search-form__input" type="text" name="search" value="<?= $data['search']; ?>" placeholder="Поиск по задачам">

    <input class="search-form__submit" type="submit" name="" value="Искать">
</form>

<div class="tasks-controls">
    <div class="radio-button-group">
        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio" <?= ($data['deadline'] === 0) ? 'checked' : '';?>>
            <a class="radio-button__text" href="/index.php?project=<?= $data['active_project']; ?>">Все задачи</a>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio" <?= ($data['deadline'] === 1) ? 'checked' : '';?>>
            <a class="radio-button__text" href="/index.php?project=<?= $data['active_project']; ?>&amp;deadline=1">Повестка дня</a>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio" <?= ($data['deadline'] === 2) ? 'checked' : '';?>>
            <a class="radio-button__text" href="/index.php?project=<?= $data['active_project']; ?>&amp;deadline=2">Завтра</a>
        </label>

        <label class="radio-button">
            <input class="radio-button__input visually-hidden" type="radio" name="radio" <?= ($data['deadline'] === 3) ? 'checked' : '';?>>
            <a class="radio-button__text" href="/index.php?project=<?= $data['active_project']; ?>&amp;deadline=3">Просроченные</a>
        </label>
    </div>

    <label class="checkbox">
        <!--добавить сюда аттрибут "checked", если переменная $show_complete_tasks равна единице-->
        <input id="show-complete-tasks" class="checkbox__input visually-hidden" type="checkbox" <?= ($data['show_complete_tasks']) ? 'checked' : ''; ?>>
        <span class="checkbox__text">Показывать выполненные</span>
    </label>
</div>

<table class="tasks">
    <?php foreach ($data['tasks'] as $key => $value): ?>
        <?php if (!$value['completed'] || $data['show_complete_tasks']): ?>
            <tr class="tasks__item task <?= ($value['completed']) ? 'task--completed' : (is_date_expired($value['deadline']) ? 'task--important' : ''); ?>">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden" type="checkbox" <?= ($value['completed']) ? 'checked' : '';?> data-task="<?= $value['id']; ?>">
                        <span class="checkbox__text"><?= htmlspecialchars($value['name']); ?></span>
                    </label>
                </td>

                <td class="task__date">
                    <?= ($value['deadline']) ? htmlspecialchars($value['deadline_format']) : 'Нет'; ?>

                </td>

                <td class="task__controls">
                    <?php if (!$value['completed']): ?>
                        <button class="expand-control" type="button" name="button"><?= htmlspecialchars($value['name']); ?></button>

                        <ul class="expand-list hidden">
                            <li class="expand-list__item">
                                <a href="/index.php?task=<?= $value['id']; ?>&amp;complete=1">Выполнить</a>
                            </li>

                            <li class="expand-list__item">
                                <a href="/index.php?task=<?= $value['id']; ?>&amp;delete">Удалить</a>
                            </li>
                        </ul>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>
