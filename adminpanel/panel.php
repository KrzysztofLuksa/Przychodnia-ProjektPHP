<?php
include '../funkcje.php';
session_start();
if(!$_SESSION['admin']) {
    header("location: index.php");
}
otworz_polaczenie();
global $polaczenie;
$wynik_pacjent = null;
$wynik_lekarz = null;
$wizyty = "";
$wynik_wizyty = mysqli_query($polaczenie, "select * from wizyty ORDER BY data DESC LIMIT 10");
if($wynik_wizyty) $wizyty .= "<tr><th>Data</th><th>Godzina</th><th>Pacjent</th><th>Lekarz</th></tr>";
while ($wiersz = mysqli_fetch_row($wynik_wizyty)) { // wrzucanie danych do tabeli + laczenie ich z kluczami lekarza i pacjenta
    $kluczLekarza = $wiersz[4];
    $kluczPacjenta = $wiersz[3];

    $wynik_pacjent = mysqli_query($polaczenie, "select imie, nazwisko from pacjenci where numer = $kluczPacjenta");
    if ($wynik_pacjent && mysqli_num_rows($wynik_pacjent) > 0) { // jesli nie znajdule pacjenta z danym numerem to wrzuca puste dane
        $dane_pacjent = mysqli_fetch_row($wynik_pacjent);
    } else {
        $dane_pacjent = ["", "", ""];
    }

    $wynik_lekarz = mysqli_query($polaczenie, "select imie, nazwisko from personel where numer = $kluczLekarza");
    if ($wynik_lekarz && mysqli_num_rows($wynik_lekarz) > 0) {
        $dane_lekarz = mysqli_fetch_row($wynik_lekarz);
    } else {
        $dane_lekarz = ["", "", ""];
    }

    $wizyty .= "<tr><td>".$wiersz[1]."</td><td>".$wiersz[2]."</td><td>".$dane_pacjent[0]." ".$dane_pacjent[1]."</td><td>".$dane_lekarz[0]." ".$dane_lekarz[1]."</td></tr>";
}

$pacjenci = "";
$wynik_pacjenci = mysqli_query($polaczenie, "select * from pacjenci ORDER BY numer DESC LIMIT 10");
if($wynik_pacjenci) $pacjenci .= "<tr><th>Imie</th><th>Nazwisko</th><th>Adres</th><th>Telefon</th></tr>";
while ($wiersz2 = mysqli_fetch_row($wynik_pacjenci)) {
    $pacjenci .= "<tr><td>".$wiersz2[1]."</td><td>".$wiersz2[2]."</td><td>".$wiersz2[4]."</td><td>".$wiersz2[5]."</td></tr>";
}

//czyszczenie pamieci
if ($wynik_wizyty) mysqli_free_result($wynik_wizyty);
if ($wynik_pacjent) mysqli_free_result($wynik_pacjent);
if ($wynik_lekarz) mysqli_free_result($wynik_lekarz);
if ($wynik_pacjenci) mysqli_free_result($wynik_pacjenci);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel</title>
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
        #elementContainer {
            display: flex;
            justify-content: center;
            width: 100%;
            height: 100%;
            margin: 20px 0;
        }
        .element {
            width: 40%;
            height: 100%;
            margin: 0 20px;
            padding: 20px;
            background-color: whitesmoke;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
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

        <div id="elementContainer">
            <div class="element">
                <h2>Ostatnio dodane wizyty</h2>
                <table>
                    <?=$wizyty?>
                </table>
            </div>
            <div class="element">
                <h2>Ostatnio dodani pacjenci</h2>
                <table>
                    <?=$pacjenci?>
                </table>
            </div>
        </div>
    </div>
    <?php
    zamknij_polaczenie();
     ?>
</body>
</html>