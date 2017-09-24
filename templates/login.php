<?php
$email = $_POST['email'] ?? '';
?>

<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>
    <h2 class="modal__heading">Вход на сайт</h2>
    <form class="form" action="index.php" method="post">
        <div class="form__row">
            <label class="form__label" for="email">E-mail <sup>*</sup></label>
            <input class="form__input <?= isset($data['errors']['email']) ? 'form__input--error' : ''; ?>" type="text" name="email" id="email" value="<?= htmlspecialchars($email); ?>" placeholder="Введите e-mail">
            <?php if (isset($data['errors']['email'])): ?>
                <p class="form__message"><?= $data['errors']['email']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>
            <input class="form__input <?= isset($data['errors']['password']) ? 'form__input--error' : ''; ?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">
            <?php if (isset($data['errors']['password'])): ?>
                <p class="form__message"><?= $data['errors']['password']; ?></p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="checkbox">
              <input class="checkbox__input visually-hidden" type="checkbox" checked>
              <span class="checkbox__text">Запомнить меня</span>
            </label>
        </div>
        <div class="form__row form__row--controls">
            <?php if (count($data['errors'])): ?>
                <p class="error-massage"><?= $data['errors']['message']; ?></p>
            <?php endif; ?>
            <input class="button" type="submit" name="login" value="Войти">
        </div>
    </form>
</div>
