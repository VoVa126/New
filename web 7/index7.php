<?php
header('Content-Type: text/html; charset=UTF-8');
session_start();

// Генерация CSRF-токена
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = array();
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        setcookie('login', '', 100000);
        setcookie('pass', '', 100000);
        $messages[] = 'Спасибо, результаты сохранены.';
        if (!empty($_COOKIE['login']) && !empty($_COOKIE['pass'])) {
            $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong> и паролем <strong>%s</strong> для изменения данных.',
                htmlspecialchars($_COOKIE['login'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($_COOKIE['pass'], ENT_QUOTES, 'UTF-8'));
        }
    }
    $errors = array();
    $errors['names'] = !empty($_COOKIE['name_error']);
    $errors['phone'] = !empty($_COOKIE['phone_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['data'] = !empty($_COOKIE['data_error']);
    $errors['gender'] = !empty($_COOKIE['gender_error']);
    $errors['agree'] = !empty($_COOKIE['agree_error']);

    if ($errors['names']) {
        setcookie('names_error', '', 100000);
        $messages[] = '<div>Заполните имя.</div>';
    }
    if ($errors['phone']) {
        setcookie('phone_error', '', 100000);
        $messages[] = '<div>Некорректный телефон.</div>';
    }
    if ($errors['email']) {
        setcookie('email_error', '', 100000);
        $messages[] = '<div>Некорректный email.</div>';
    }
    if ($errors['data']) {
        setcookie('data_error', '', 100000);
        $messages[] = '<div>Выберите год рождения.</div>';
    }
    if ($errors['gender']) {
        setcookie('gender_error', '', 100000);
        $messages[] = '<div>Выберите пол.</div>';
    }
    if ($errors['agree']) {
        setcookie('agree_error', '', 100000);
        $messages[] = '<div>Поставьте галочку.</div>';
    }
    
    $values = array();
    $values['names'] = isset($_COOKIE['names_value']) ? htmlspecialchars($_COOKIE['names_value'], ENT_QUOTES, 'UTF-8') : '';
    $values['phone'] = isset($_COOKIE['phone_value']) ? htmlspecialchars($_COOKIE['phone_value'], ENT_QUOTES, 'UTF-8') : '';
    $values['email'] = isset($_COOKIE['email_value']) ? htmlspecialchars($_COOKIE['email_value'], ENT_QUOTES, 'UTF-8') : '';
    $values['data'] = isset($_COOKIE['data_value']) ? htmlspecialchars($_COOKIE['data_value'], ENT_QUOTES, 'UTF-8') : '';
    $values['gender'] = isset($_COOKIE['gender_value']) ? htmlspecialchars($_COOKIE['gender_value'], ENT_QUOTES, 'UTF-8') : '';
    $values['biography'] = isset($_COOKIE['biography_value']) ? htmlspecialchars($_COOKIE['biography_value'], ENT_QUOTES, 'UTF-8') : '';
    $values['agree'] = isset($_COOKIE['agree_value']) ? htmlspecialchars($_COOKIE['agree_value'], ENT_QUOTES, 'UTF-8') : ''; 
    $values['language'] = isset($_COOKIE['language_value']) ? json_decode($_COOKIE['language_value'], true) : array();

    if (!empty($_SESSION['login'])) {
        printf('Вход с логином %s, uid %d', htmlspecialchars($_SESSION['login'], ENT_QUOTES, 'UTF-8'), intval($_SESSION['uid']));
    }

    if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])) {
        try {
            $db = new PDO('mysql:host=localhost;dbname=u67448', 'u67448', '2263728', array(PDO::ATTR_PERSISTENT => true));
            $stmt = $db->prepare("SELECT * FROM application WHERE id = ?");
            $stmt->execute([$_SESSION['uid']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $values['names'] = htmlspecialchars($row['names'], ENT_QUOTES, 'UTF-8');
                $values['phone'] = isset($_COOKIE['phone']) ? htmlspecialchars($_COOKIE['phone'], ENT_QUOTES, 'UTF-8') : '';
                $values['email'] = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8');
                $values['data'] = isset($_COOKIE['data']) ? htmlspecialchars($_COOKIE['data'], ENT_QUOTES, 'UTF-8') : '';
                $values['gender'] = htmlspecialchars($row['gender'], ENT_QUOTES, 'UTF-8');
                $values['biography'] = htmlspecialchars($row['biography'], ENT_QUOTES, 'UTF-8');
                $values['agree'] = true;
                
                $stmt = $db->prepare("SELECT * FROM languages WHERE id = ?");
                $stmt->execute([$_SESSION['uid']]);
                $language = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $language[] = htmlspecialchars($row['name_of_language'], ENT_QUOTES, 'UTF-8');
                }
                $values['language'] = $language;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die('Произошла ошибка при подключении к базе данных.');
        }
    }
    
    include('form.php');
} else {
    $errors = FALSE;

    if (empty(htmlspecialchars($_POST['names'], ENT_QUOTES, 'UTF-8'))) {
        setcookie('names_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('names_value', htmlspecialchars($_POST['names'], ENT_QUOTES, 'UTF-8'), time() + 12 * 30 * 24 * 60 * 60);
    }
    if (!preg_match('/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/', $_POST['phone'])) {
        setcookie('phone_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('phone_value', htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8'), time() + 30 * 24 * 60 * 60);
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('email_value', htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'), time() + 12 * 30 * 24 * 60 * 60);
    }
    if (empty($_POST['data'])) {
        setcookie('data_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('data_value', htmlspecialchars($_POST['data'], ENT_QUOTES, 'UTF-8'), time() + 12 * 30 * 24 * 60 * 60);
    }
    if (empty($_POST['gender'])) {
        setcookie('gender_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('gender_value', htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8'), time() + 12 * 30 * 24 * 60 * 60);
    }
    if (empty($_POST['agree'])) {
        setcookie('agree_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        setcookie('agree_value', htmlspecialchars($_POST['agree'], ENT_QUOTES, 'UTF-8'), time() + 12 * 30 * 24 * 60 * 60);
    }

    $language = isset($_POST['language']) ? $_POST['language'] : array();
    if (!empty($_POST['biography'])) {
        setcookie('biography_value', htmlspecialchars($_POST['biography'], ENT_QUOTES, 'UTF-8'), time() + 12 * 30 * 24 * 60 * 60);
    }
    if (!empty($language)) {
        $json = json_encode(array_map('htmlspecialchars', $language), ENT_QUOTES, 'UTF-8');
        setcookie('language_value', $json, time() + 12 * 30 * 24 * 60 * 60);
    }

    // Проверка CSRF-токена
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Неверный CSRF-токен.');
    }

    if ($errors) {
        header('Location: index.php');
        exit();
    } else {
        setcookie('names_error', '', 100000);
        setcookie('phone_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('data_error', '', 100000);
        setcookie('gender_error', '', 100000);
        setcookie('agree_error', '', 100000);
    }

    try {
        $db = new PDO('mysql:host=localhost;dbname=u67448', 'u67448', '2263728', array(PDO::ATTR_PERSISTENT => true));
        if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login']) && !$errors) {
            $stmt = $db->prepare("UPDATE application SET names = ?, phone = ?, email = ?, data = ?, gender = ?, biography = ? WHERE id = ?");
            $stmt->execute([
                htmlspecialchars($_POST['names'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($_POST['data'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($_POST['biography'], ENT_QUOTES, 'UTF-8'),
                intval($_SESSION['uid'])
            ]);

            $stmt = $db->prepare("DELETE FROM languages WHERE id = ?");
            $stmt->execute([intval($_SESSION['uid'])]);

            foreach ($language as $lang) {
                $stmt = $db->prepare("INSERT INTO languages SET id = ?, name_of_language = ?");
                $stmt->execute([intval($_SESSION['uid']), htmlspecialchars($lang, ENT_QUOTES, 'UTF-8')]);
            }
        } else {
            $login = 'user' . substr(uniqid(), -5);
            $pass = substr(md5(uniqid()), 0, 10);
            $stmt = $db->prepare("INSERT INTO application SET names = ?, phone = ?, email = ?, data = ?, gender = ?, biography = ?");
            $stmt->execute([
                htmlspecialchars($_POST['names'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($_POST['data'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($_POST['gender'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($_POST['biography'], ENT_QUOTES, 'UTF-8')
            ]);
            $id = $db->lastInsertId();

            foreach ($language as $lang) {
                $stmt = $db->prepare("INSERT INTO languages SET id = ?, name_of_language = ?");
                $stmt->execute([$id, htmlspecialchars($lang, ENT_QUOTES, 'UTF-8')]);
            }

            $stmt = $db->prepare("INSERT INTO login SET id = ?, login = ?, pass = ?");
            $stmt->execute([$id, htmlspecialchars($login, ENT_QUOTES, 'UTF-8'), htmlspecialchars(md5($pass), ENT_QUOTES, 'UTF-8')]);

            setcookie('login', $login);
            setcookie('pass', $pass);
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        die('Произошла ошибка при подключении к базе данных.');
    }

    setcookie('save', '1');
    header('Location: ./');
}
?>
