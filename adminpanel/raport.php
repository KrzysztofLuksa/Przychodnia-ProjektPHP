<?php
include '../funkcje.php';
session_start();
if (!$_SESSION['admin']) {
    header("location: index.php");
}
otworz_polaczenie();
global $polaczenie;

//liczba pacjentow
$zapytanie = "select COUNT(*) from pacjenci";
$wynik = mysqli_query($polaczenie, $zapytanie) or exit("Błąd w zapytaniu: " . $zapytanie);
$liczba_pacjentow = mysqli_fetch_row($wynik)[0];
mysqli_free_result($wynik);

// liczba wizyt
$zapytanie = "select COUNT(*) from wizyty";
$wynik = mysqli_query($polaczenie, $zapytanie) or exit("Błąd w zapytaniu: " . $zapytanie);
$liczba_wizyt = mysqli_fetch_row($wynik)[0];
mysqli_free_result($wynik);

// liczba lekarzy
$zapytanie = "select COUNT(*) from personel where stanowisko='lekarz'";
$wynik = mysqli_query($polaczenie, $zapytanie) or exit("Błąd w zapytaniu: " . $zapytanie);
$liczba_lekarzy = mysqli_fetch_row($wynik)[0];
mysqli_free_result($wynik);

// liczba wizyt na lekarza
if ($liczba_lekarzy > 0) {
    $wizyty_na_lekarza = $liczba_wizyt / $liczba_lekarzy;
} else {
    $wizyty_na_lekarza = 0; // lub inna wartość domyślna
}

//lista lekarzy i ich statystyki
$lekarze_wizyty = [];
$zapytanie = "select numer, imie, nazwisko from personel where stanowisko='lekarz'";
$wynik = mysqli_query($polaczenie, $zapytanie) or exit("Błąd w zapytaniu: " . $zapytanie);
while ($wiersz = mysqli_fetch_row($wynik)) {
    $lekarz_numer = $wiersz[0];
    
    //pobieranie z bazy danych liczby wizyt i obliczenia sredniej oceny wszystkich wizyt danego lekarza
    $zapytanie_wizyty = "select COUNT(*), AVG(ocena) from wizyty where lekarz = $lekarz_numer";
    $wynik_wizyty = mysqli_query($polaczenie, $zapytanie_wizyty) or exit("Błąd w zapytaniu: " . $zapytanie_wizyty);
    $wizyty = mysqli_fetch_row($wynik_wizyty);
    mysqli_free_result($wynik_wizyty);
    
    $lekarze_wizyty[] = [
        $wiersz[0], // numer
        $wiersz[1], // imie
        $wiersz[2], // nazwisko
        $wizyty[0], // liczba wizyt
        $wizyty[1]  // średnia ocena
    ];
}
mysqli_free_result($wynik);

//Wizyty w wybranym okresie
$lekarze_okres = [];
$daneZOkresu = false;
if (!empty($_POST['data_od']) && !empty($_POST['data_do'])) {
    $data_od = $_POST['data_od'];
    $data_do = $_POST['data_do'];

    // Pobierz listę lekarzy
    $zapytanie = "select numer, imie, nazwisko from personel where stanowisko='lekarz'";
    $wynik = mysqli_query($polaczenie, $zapytanie) or exit("Błąd w zapytaniu: " . $zapytanie);
    while ($wiersz = mysqli_fetch_row($wynik)) {
        $lekarz_numer = $wiersz[0];

        //liczba wizyt i srednia ocena dla danego lekarza w wybranym okresie
        $zapytanie_wizyty = "select COUNT(*), AVG(ocena) from wizyty where lekarz = $lekarz_numer AND data >= '$data_od' AND data <= '$data_do'";
        $wynik_wizyty = mysqli_query($polaczenie, $zapytanie_wizyty) or exit("Błąd w zapytaniu: " . $zapytanie_wizyty);
        $wizyty = mysqli_fetch_row($wynik_wizyty);
        mysqli_free_result($wynik_wizyty);

        // Dodaj dane do listy, jeśli są wizyty w wybranym okresie
        if ($wizyty[0] > 0) {
            $lekarze_okres[] = [
                $wiersz[0], // numer
                $wiersz[1], // imie
                $wiersz[2], // nazwisko
                $wizyty[0], // liczba wizyt
                $wizyty[1]  // średnia ocena
            ];
        }
    }
    mysqli_free_result($wynik);

    //sprawdzenie czy sa dane w tablicy
    if (count($lekarze_okres) == 0) {
        $daneZOkresu = true;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporty</title>
    <style>
        body, html {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            background-color: white;
            overflow: hidden;
        }
        #glownyKontener {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: bolder;
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
        .raport {
            width: 80%;
            margin: 20px;
            padding: 20px;
            background-color: whitesmoke;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .raport h2 {
            margin-bottom: 20px;
        }
        .raport p {
            margin: 10px 0;
        }
        .raport table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .raport table, .raport th, .raport td {
            border: 1px solid black;
        }
        .raport th, .raport td {
            padding: 10px;
            text-align: center;
        }
        .raport th {
            background-color: green;
            color: white;
        }
        .form-container {
            margin-bottom: 20px;
        }
        .form-container input[type="submit"] {
            padding: 5px 10px;
            background-color: green;
            color: white;
            border: none;
            cursor: pointer;
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
    <div class="raport">
        <h2>Raport</h2>
        <table>
            <tr>
                <th>Liczba pacjentów</th>
                <th>Liczba wizyt</th>
                <th>Liczba lekarzy</th>
                <th>liczba wizyt na lekarza</th>
            </tr>
            <tr>
                <td><?= $liczba_pacjentow ?></td>
                <td><?= $liczba_wizyt ?></td>
                <td><?= $liczba_lekarzy ?></td>
                <td><?= number_format($wizyty_na_lekarza, 2) ?></td>
            </tr>
        </table>
        <h3>Wizyty przeprowadzone przez lekarzy</h3>
        <table>
            <tr>
                <th>Lekarz</th>
                <th>Liczba wizyt</th>
                <th>Średnia ocena wizyt</th>
            </tr>
            <?php
            foreach ($lekarze_wizyty as $lekarz) {
                echo "<tr><td>" . $lekarz[1] . " " . $lekarz[2] . "</td><td>" . $lekarz[3] . "</td><td>" . number_format($lekarz[4], 2) . "</td></tr>";
            }
            ?>
        </table>
        <h3>Podsumowanie wizyt w wybranym okresie</h3>
        <div class="form-container">
            <form method="POST" action="">
                <table>
                    <tr>
                        <td><label for="data_od">Od:</label></td>
                        <td><input type="date" value="<?= $_POST['data_od'] ?? "" ?>" name="data_od" required></td>
                    </tr>
                    <tr>
                        <td><label for="data_do">Do:</label></td>
                        <td><input type="date" value="<?= $_POST['data_do'] ?? "" ?>" name="data_do" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" value="Pokaż wizyty"></td>
                    </tr>
                </table>
            </form>
        </div>
        <?php if (!empty($lekarze_okres)) { ?>
        <table>
            <tr>
                <th>Lekarz</th>
                <th>Liczba wizyt</th>
                <th>Średnia ocena wizyt</th>
            </tr>
            <?php
            foreach ($lekarze_okres as $lekarz) {
                echo "<tr><td>" . $lekarz[1] . " " . $lekarz[2] . "</td><td>" . $lekarz[3] . "</td><td>" . number_format($lekarz[4], 2) . "</td></tr>";
            }
            ?>
        </table>
        <?php } if($daneZOkresu) { ?>
            <p>Brak danych w wybranym okresie.</p>
        <?php } ?>
    </div>
</div>
</div>
</body>
</html>