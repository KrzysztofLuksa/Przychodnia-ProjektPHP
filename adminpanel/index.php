<?php
include '../funkcje.php';
//sprawdzenie czy tablica get zawiera ustawioną zmienna wylogowanie jesli tak to usuwa dane z sesji
if (isset($_GET['wylogowanie'])) {
    session_start();
    session_unset();
    session_destroy();
    header("Location: index.php");
}
$validateString = "";
if(!empty($_POST['login']) && !empty($_POST['haslo'])) { //zapytanie do bazy danych z loginem i potem sprawdzenie hasla czy jest poprawne
    $login = $_POST['login'];
    $haslo = $_POST['haslo'];
    otworz_polaczenie();
    global $polaczenie;
    $odpowiedz = mysqli_query($polaczenie, "select * from Admin where login='$login'");
    $rows = mysqli_fetch_row($odpowiedz);
    if(is_array($rows)) {
        if(password_verify($haslo, $rows[2])) {
            session_start();
            $_SESSION['admin'] = true;
            header("location: panel.php");
        } else $validateString .= "Błędny login lub hasło";
    } else $validateString .= "Błędny login lub hasło";
mysqli_free_result($odpowiedz);
zamknij_polaczenie();
} else {
    $validateString .= "Wypełnij wszystkie pola";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Przychodnia - Logowanie</title>
    <style>
        body, html {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: gray;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        #kontenerLogowania {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 10px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid black;
            border-radius: 4px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: darkgreen;
        }
        .wiadomoscBlendu {
            font-weight: bold;
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div id="kontenerLogowania"> 
        <!-- Formularz logowania -->
        <form method="post" action="">
            <table>
                <tr>
                    <td>Login</td>
                    <td><input type="text" name="login"></td>
                </tr>
                <tr>
                    <td>Hasło</td>
                    <td><input type="password" name="haslo"></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="Zaloguj się"></td>
                </tr>
            </table>
        </form>
        <p class="wiadomoscBlendu"><?=$validateString?></p>
    </div>
</body>
</html>