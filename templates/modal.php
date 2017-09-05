<?php
$name = $_POST['name'] ?? '';
$project = $_POST['project'] ?? 0;
$date = $_POST['date'] ?? '';
?>

<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>
    <h2 class="modal__heading">Добавление задачи</h2>
    <form class="form" action="index.php" method="post" enctype="multipart/form-data">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>
            <input class="form__input  <?= in_array('name', $data['errors']) ? 'form__input--error' : ''; ?>" type="text" name="name" id="name" value="<?= $name; ?>" placeholder="Введите название">
            <?php if (in_array('name', $data['errors'])): ?>
                <span class="form__error">Заполните это поле</span>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>
            <select class="form__input form__input--select  <?= in_array('project', $data['errors']) ? 'form__input--error' : ''; ?>" name="project" id="project">
                <?php foreach ($data['projects'] as $key => $value): ?>
                    <option value="<?= $key; ?>" <?= ($key == $project) ? 'selected' : ''; ?>><?= $value; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (in_array('project', $data['errors'])): ?>
                <span class="form__error">Выберите проект</span>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>
            <input class="form__input form__input--date  <?= in_array('date', $data['errors']) ? 'form__input--error' : ''; ?>" type="text" name="date" id="date" value="<?= $date; ?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
            <?php if (in_array('date', $data['errors'])): ?>
                <span class="form__error">Заполните дату в правильном формате (ДД.ММ.ГГГГ)</span>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label">Файл</label>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="preview" id="preview" value="">
                <label class="button button--transparent" for="preview">
                    <span>Выберите файл</span>
                </label>
            </div>
        </div>
        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</div>
