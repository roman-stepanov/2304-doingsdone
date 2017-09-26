<?php
$name = $_POST['name'] ?? '';
$project = (int)$_POST['project'] ?? 0;
$date = $_POST['date'] ?? '';
?>

<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>
    <h2 class="modal__heading">Добавление задачи</h2>
    <form class="form" action="index.php" method="post" enctype="multipart/form-data">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>
            <input class="form__input  <?= isset($data['errors']['name']) ? 'form__input--error' : ''; ?>" type="text" name="name" id="name" value="<?= $name; ?>" placeholder="Введите название">
            <?php if (isset($data['errors']['name'])): ?>
                <p class="form__message"><?= $data['errors']['name']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>
            <select class="form__input form__input--select  <?= isset($data['errors']['project']) ? 'form__input--error' : ''; ?>" name="project" id="project">
                <?php foreach ($data['projects'] as $key => $value): ?>
                    <option value="<?= $value['id']; ?>" <?= ($value['id'] === $project) ? 'selected' : ''; ?>><?= $value['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($data['errors']['project'])): ?>
                <p class="form__message"><?= $data['errors']['project']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения <sup>*</sup></label>
            <input class="form__input form__input--date  <?= isset($data['errors']['date']) ? 'form__input--error' : ''; ?>" type="text" name="date" id="date" value="<?= $date; ?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
            <?php if (isset($data['errors']['date'])): ?>
                <p class="form__message"><?= $data['errors']['date']; ?></p>
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
            <?php if (count($data['errors'])): ?>
                <p class="error-massage">Пожалуйста, исправьте ошибки в форме</p>
            <?php endif; ?>
            <input class="button" type="submit" name="new-task" value="Добавить">
        </div>
    </form>
</div>
