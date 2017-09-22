<?php
$name = $_POST['name'] ?? '';
?>

<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>
    <h2 class="modal__heading">Новый проект</h2>
    <form class="form" action="index.php" method="post">
        <div class="form__row">
            <label class="form__label" for="email">Название проекта <sup>*</sup></label>
            <input class="form__input <?= isset($data['errors']['name']) ? 'form__input--error' : ''; ?>" type="text" name="name" id="email" value="<?= htmlspecialchars($name); ?>" placeholder="Введите название проекта">
            <?php if (isset($data['errors']['name'])): ?>
                <p class="form__message"><?= $data['errors']['name']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row form__row--controls">
            <?php if (count($data['errors'])): ?>
                <p class="error-massage">Пожалуйста, исправьте ошибки в форме</p>
            <?php endif; ?>
            <input class="button" type="submit" name="new-project" value="Добавить">
        </div>
    </form>
</div>
