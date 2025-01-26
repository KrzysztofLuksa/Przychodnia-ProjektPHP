<?php
include '../funkcje.php';
session_start();
if (!$_SESSION['admin']) {
    header("location: index.php");
} else {
    otworz_polaczenie();
    global $polaczenie;
    $pacjenci = "";
    $nr = "";
    $imie_filter = isset($_POST['imie_filter']) ? $_POST['imie_filter'] : '';
    $nazwisko_filter = isset($_POST['nazwisko_filter']) ? $_POST['nazwisko_filter'] : '';
    $pesel_filter = isset($_POST['pesel_filter']) ? $_POST['pesel_filter'] : '';

    if (isset($_POST['przycisk'])) {
        $nr = key($_POST['przycisk']);
    }

    if (isset($_POST['strona'])) {
        if (empty($nr)) {
            $_SESSION['strona'] = $_POST['strona'];
        } else {
            $_SESSION['strona'] = "wizyty.php";
        }
    }

    $zapytanie = "select * from pacjenci";
    $warunki = [];
    
    if (!empty($imie_filter)) {
        $warunki[] = "imie = '$imie_filter'";
    }
    if (!empty($nazwisko_filter)) {
        $warunki[] = "nazwisko = '$nazwisko_filter'";
    }
    if (!empty($pesel_filter)) {
        $warunki[] = "pesel = '$pesel_filter'";
    }
    
    if (!empty($warunki)) {
        $zapytanie .= " where " . implode(" AND ", $warunki);
    }

    $wynik = mysqli_query($polaczenie, $zapytanie) or exit("Błąd w zapytaniu: " . $zapytanie);
    if ($wynik) {
        $pacjenci .= "<tr><th>Imię</th><th>Nazwisko</th><th>Pesel</th><th>Adres</th><th>Telefon</th><th><input type='submit' name='przycisk[-1]' value='Anuluj'></th></tr>";
        while ($wiersz = mysqli_fetch_row($wynik)) {
            $pacjenci .= "<tr><td>" . $wiersz[1] . "</td><td>" . $wiersz[2] . "</td><td>" . $wiersz[3] . "</td><td>" . $wiersz[4] . "</td><td>" . $wiersz[5] . "</td>
            <td align='center'><input type='submit' name='wybrany[$wiersz[0],$nr]' value='Wybierz'></td></tr>";
        }
        mysqli_free_result($wynik);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wybierz pacjenta</title>
    <style>
        body, html {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            background-color: white;
        }
        #glownyKontener {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        #header {
            width: 100%;
            height: 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: black;
            color: white;
            padding: 0 20px;
        }
        #header-link-container {
            display: flex;
            align-items: center;
        }
        .header-link {
            margin-right: 20px;
            color: white;
            text-decoration: none;
            font-size: 18px;
        }
        .header-link:hover {
            text-decoration: underline;
        }
        #headrer-side-container, #headrer-side-logout-container {
            display: flex;
            align-items: center;
        }
        #element {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            height: 100%;
            overflow-y: scroll;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        td, th {
            padding: 12px;
            text-align: center;
            border: 1px solid black;
        }
        th {
            background-color: green;
            color: white;
        }
        td {
            background-color: lightgray;
        }
    </style>
</head>
<body>

<div id="glownyKontener">
    <div id="header">
        <div id="headrer-side-container">
            <a href="panel.php" class="header-link"><h2>Home</h2></a>
        </div>
        <div id="header-link-container">
            <a href="personel.php" class="header-link"><h2>Personel</h2></a>
            <a href="pacjenci.php" class="header-link"><h2>Pacjenci</h2></a>
            <a href="wizyty.php" class="header-link"><h2>Wizyty</h2></a>
            <a href="recepta.php" class="header-link"><h2>Recepta</h2></a>
            <a href="skierowanie.php" class="header-link"><h2>Skierowanie</h2></a>
            <a href="raport.php" class="header-link"><h2>Raport</h2></a>
        </div>
        <div id="headrer-side-logout-container">
            <a href="index.php?wylogowanie" class="header-link"><h2>Wyloguj się</h2></a>
        </div>
    </div>
    
    <div id="element">
        <form method="POST" action="">
            <input type="hidden" name="przycisk[<?=$nr?>]">
            <table>
                <tr>
                    <td><input type="text" id="imie_filter" name="imie_filter" placeholder="Imię" value="<?=$imie_filter?>"></td>
                    <td><input type="text" id="nazwisko_filter" name="nazwisko_filter" placeholder="Nazwisko" value="<?=$nazwisko_filter?>"></td>
                    <td><input type="text" id="pesel_filter" name="pesel_filter" placeholder="PESEL" value="<?=$pesel_filter?>"></td>
                    <td><input type="submit" value="Szukaj"></td>
                    <td><input type="button" value="Wyczyść" onclick="window.location.href='wybierzpacjenta.php'"></td>
                </tr>
            </table>
        </form>
        <form action="<?=$_SESSION['strona'] ?? ""?>" method="post">
            <input type="hidden" name="nr" value="<?=$nr?>">
            <table border="1">
                <?=$pacjenci?>
            </table>
        </form>
    </div>
    <?php zamknij_polaczenie(); ?>
</div>
</body>
</html>