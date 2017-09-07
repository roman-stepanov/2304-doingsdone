<?php
$email = $_POST['email'] ?? '';
?>

<div class="modal">
    <button class="modal__close" type="button" name="button">Закрыть</button>
    <h2 class="modal__heading">Вход на сайт</h2>
    <form class="form" action="index.php" method="post">
        <div class="form__row">
            <label class="form__label" for="email">E-mail <sup>*</sup></label>
            <input class="form__input <?= in_array('email', $data['errors']) ? 'form__input--error' : ''; ?>" type="text" name="email" id="email" value="<?= htmlspecialchars($email); ?>" placeholder="Введите e-mail">
            <?php if (in_array('email', $data['errors'])): ?>
                <p class="form__message">E-mail введён некорректно</p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>
            <input class="form__input <?= in_array('password', $data['errors']) ? 'form__input--error' : ''; ?>" type="password" name="password" id="password" value="" placeholder="Введите пароль">
            <?php if (in_array('password', $data['errors'])): ?>
                <p class="form__message">Неверный пароль</p>
            <?php endif; ?>
        </div>
        <div class="form__row">
            <label class="checkbox">
              <input class="checkbox__input visually-hidden" type="checkbox" checked>
              <span class="checkbox__text">Запомнить меня</span>
            </label>
        </div>
        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="login" value="Войти">
        </div>
    </form>
</div>
