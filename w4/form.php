<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Form</title>
  <style>
    .error {
      border: 1px solid red;
      background-color: #fdd;
    }
    .form-container {
      margin: 10px;
    }
    .form-group {
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <?php if (!empty($messages)): ?>
    <div class="form-container">
      <?php foreach ($messages as $message): ?>
        <p><?= $message ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form action="index.php" method="POST">
    <div class="form-group">
      <label for="names">Имя:</label>
      <input type="text" name="names" id="names" value="<?= htmlspecialchars($values['names']) ?>" class="<?= $errors['names'] ? 'error' : '' ?>">
    </div>

    <div class="form-group">
      <label for="phone">Телефон:</label>
      <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($values['phone']) ?>" class="<?= $errors['phone'] ? 'error' : '' ?>">
    </div>

    <div class="form-group">
      <label for="email">Почта:</label>
      <input type="email" name="email" id="email" value="<?= htmlspecialchars($values['email']) ?>" class="<?= $errors['email'] ? 'error' : '' ?>">
    </div>

    <div class="form-group">
      <label for="date">Дата:</label>
      <input type="date" name="date" id="date" value="<?= htmlspecialchars($values['date']) ?>" class="<?= $errors['date'] ? 'error' : '' ?>">
    </div>

    <div class="form-group">
      <label for="gender">Пол:</label>
      <input type="radio" name="gender" value="male" <?= $values['gender'] === 'male' ? 'checked' : '' ?>>Мужской
      <input type="radio" name="gender" value="female" <?= $values['gender'] === 'female' ? 'checked' : '' ?>>Женский
      <div class="<?= $errors['gender'] ? 'error' : '' ?>"></div>
    </div>

    <div class="form-group">
      <label for="languages">Языки программирования:</label>
      <select name="languages[]" id="languages" multiple class="<?= $errors['languages'] ? 'error' : '' ?>">
        <option value="1" <?= in_array('1', $values['languages']) ? 'selected' : '' ?>>PHP</option>
        <option value="2" <?= in_array('2', $values['languages']) ? 'selected' : '' ?>>JavaScript</option>
        <option value="3" <?= in_array('3', $values['languages']) ? 'selected' : '' ?>>Python</option>
        <option value="4" <?= in_array('4', $values['languages']) ? 'selected' : '' ?>>Ruby</option>
      </select>
    </div>

    <div class="form-group">
      <label for="biography">Биография:</label>
      <textarea name="biography" id="biography" class="<?= $errors['biography'] ? 'error' : '' ?>"><?= htmlspecialchars($values['biography']) ?></textarea>
    </div>

    <div class="form-group">
      <label for="agree">Согласие с условиями:</label>
      <input type="checkbox" name="agree" id="agree" <?= $values['agree'] ? 'checked' : '' ?> class="<?= $errors['agree'] ? 'error' : '' ?>">
    </div>

    <div class="form-group">
      <input type="submit" value="Отправить">
    </div>
  </form>
</body>
</html>
