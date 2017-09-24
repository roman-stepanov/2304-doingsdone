<?php
$email = $_POST['email'] ?? '';
$name = $_POST['name'] ?? '';
?>

<h2 class="content__main-heading">Регистрация аккаунта</h2>
<form class="form" action="index.php" method="post">
    <div class="form__row">
        <label class="form__label" for="email">E-mail <sup>*</sup></label>
        <input class="form__input <?= isset($data['errors']['email']) ? 'form__input--error' : ''; ?>" type="text" name="email" id="email" value="<?= $email; ?>" placeholder="Введите e-mail">
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
        <label class="form__label" for="name">Имя <sup>*</sup></label>
        <input class="form__input <?= isset($data['errors']['name']) ? 'form__input--error' : ''; ?>" type="text" name="name" id="name" value="<?= $name; ?>" placeholder="Введите имя">
        <?php if (isset($data['errors']['name'])): ?>
            <p class="form__message"><?= $data['errors']['name']; ?></p>
        <?php endif; ?>
    </div>
    <div class="form__row form__row--controls">
        <?php if (count($data['errors'])): ?>
            <p class="error-massage">Пожалуйста, исправьте ошибки в форме</p>
        <?php endif; ?>
        <input class="button" type="submit" name="register" value="Зарегистрироваться">
    </div>
</form>
