<?php
include '../funkcje.php';
session_start();
if (!$_SESSION['admin']) {
    header("location: index.php");
}
otworz_polaczenie();
global $polaczenie;

$lekarze = [];
$zapytanie = "select numer, imie, nazwisko, specjalizacja from personel where stanowisko='lekarz'";
$wynik = mysqli_query($polaczenie, $zapytanie) or exit("Błąd w zapytaniu: " . $zapytanie);
while ($wiersz = mysqli_fetch_row($wynik)) {
    $lekarze[] = $wiersz;
}
mysqli_free_result($wynik);

$imiePacjenta = '';
$nazwiskoPacjenta = '';
$peselPacjenta = '';
$rodzaj = '';
$opis = '';
$data_skierowania = '';
$zapisano = false;
$sciezka = '';
$blendy = '';

function pobierz_dane_pacjenta($pacjent) {
    global $polaczenie;
    $zapytanie = "select imie, nazwisko, pesel from pacjenci where numer=$pacjent";
    $wynik = mysqli_query($polaczenie, $zapytanie) or exit("Błąd w zapytaniu: " . $zapytanie);
    $pacjent_dane = mysqli_fetch_row($wynik);
    mysqli_free_result($wynik);
    zamknij_polaczenie();
    return $pacjent_dane;
}

function zapisz_skierowanie($rodzaj, $opis, $lekarz, $data_skierowania, $imiePacjenta, $nazwiskoPacjenta, $peselPacjenta, $lekarze) {
    foreach ($lekarze as $l) { //szukanie danych lekarza kluczem wybranego lekarza
        if ($l[0] == $lekarz) {
            $lekarz_dane = $l;
            break;
        }
    }

    $dane = "Imię pacjenta: " . $imiePacjenta . "\n" .
            "Nazwisko pacjenta: " . $nazwiskoPacjenta . "\n" .
            "PESEL pacjenta: " . $peselPacjenta . "\n" .
            "-------------------------\n" .
            "Rodzaj skierowania: " . $rodzaj . "\n" .
            "Opis skierowania: " . $opis . "\n" .
            "Lekarz: " . $lekarz_dane[1] . " " . $lekarz_dane[2] . " (" . $lekarz_dane[3] . ")\n" .
            "Data skierowania: " . $data_skierowania . "\n";

    $sciezka = 'skierowania/skierowanie_' . $peselPacjenta . '_' . date('Y-m-d_H-i-s') . '.txt';//sciezka pliku do ktorego zostana zapisane dane

    //otwieranie pliku
    $plik = fopen($sciezka, 'a');
    if ($plik) {
        fputs($plik, $dane);
        fclose($plik);
    } else {
        echo "Nie można otworzyć pliku do zapisu.";
    }
    return $sciezka;
}
//funkcja walidujaca dane z formularza
function validacjaFormularza($rodzaj, $opis, $lekarz, $data_skierowania, $imiePacjenta, $nazwiskoPacjenta, $peselPacjenta) {
    if (empty($rodzaj) || empty($opis) || empty($lekarz) || empty($data_skierowania) || empty($imiePacjenta) || empty($nazwiskoPacjenta) || empty($peselPacjenta)) {
        return 'Wszystkie pola muszą być wypełnione.';
    } elseif (strtotime($data_skierowania) < strtotime(date('Y-m-d'))) {
        return 'Data skierowania nie może być wcześniejsza niż dzisiejsza.';
    }
    return '';
}
//funckja czyszczaca formularz po jego prawidlowym zapisaniu
function wyczyscDaneFormularza() {
    global $imiePacjenta, $nazwiskoPacjenta, $peselPacjenta, $rodzaj, $opis, $data_skierowania;
    $imiePacjenta = '';
    $nazwiskoPacjenta = '';
    $peselPacjenta = '';
    $rodzaj = '';
    $opis = '';
    $data_skierowania = '';
}

if (!empty($_POST['wybrany'])) {
    $key = array_keys($_POST['wybrany'])[0];
    list($pacjent, $nr) = explode(',', $key);
    $pacjent = trim($pacjent);

    $pacjent_dane = pobierz_dane_pacjenta($pacjent);
    $imiePacjenta = $pacjent_dane[0];
    $nazwiskoPacjenta = $pacjent_dane[1];
    $peselPacjenta = $pacjent_dane[2];
} elseif (!empty($_POST['rodzaj']) && !empty($_POST['opis']) && !empty($_POST['lekarz']) && !empty($_POST['data_skierowania'])) {
    $rodzaj = $_POST['rodzaj'];
    $opis = $_POST['opis'];
    $lekarz = $_POST['lekarz'];
    $data_skierowania = $_POST['data_skierowania'];
    $imiePacjenta = $_POST['imie_pacjenta'];
    $nazwiskoPacjenta = $_POST['nazwisko_pacjenta'];
    $peselPacjenta = $_POST['pesel_pacjenta'];

    // Walidacja danych
    $blendy = validacjaFormularza($rodzaj, $opis, $lekarz, $data_skierowania, $imiePacjenta, $nazwiskoPacjenta, $peselPacjenta);

    if (empty($blendy)) {
        $sciezka = zapisz_skierowanie($rodzaj, $opis, $lekarz, $data_skierowania, $imiePacjenta, $nazwiskoPacjenta, $peselPacjenta, $lekarze);
        $zapisano = true;
        wyczyscDaneFormularza();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skierowanie</title>
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
        form {
            width: 50%;
            margin: 20px;
            padding: 20px;
            background-color: whitesmoke;
            border-radius: 8px;
            display: flex;
            justify-content: center;
        }
        table {
            width: 100%;
        }
        th {
            font-weight: bolder;
            padding-bottom: 20px;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid black;
            border-radius: 4px;
        }
        input[type="submit"] {
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
        <h1>Wystawianie skierowania</h1>
        <form method="POST" action="">
            <input type="hidden" name="wybrany_pacjent" value="<?=$pacjent?>">
            <input type="hidden" name="strona" value="skierowanie.php">
            <table>
                <tr>
                    <th>Imię pacjenta</th>
                    <td colspan=2>
                        <input type="text" name="imie_pacjenta" readonly value="<?=htmlspecialchars($imiePacjenta)?>" size=15 style='text-align: left'>
                    </td>
                </tr>
                <tr>
                    <th>Nazwisko pacjenta</th>
                    <td colspan=2>
                        <input type="text" name="nazwisko_pacjenta" readonly value="<?=htmlspecialchars($nazwiskoPacjenta)?>" size=15 style='text-align: left'>
                    </td>
                </tr>
                <tr>
                    <th>Pesel pacjenta</th>
                    <td colspan=2>
                        <input type="text" name="pesel_pacjenta" readonly value="<?=htmlspecialchars($peselPacjenta)?>" size=15 style='text-align: left'>
                    </td>
                </tr>
                <tr>
                    <th>Wybierz pacjenta</th>
                    <td>
                        <input type="submit" value="Wybierz pacjenta" onclick="this.form.action='wybierzpacjenta.php'" style="width: 100%;">
                    </td>
                </tr>
                <tr>
                    <th>Rodzaj skierowania</th>
                    <td><input type="text" id="rodzaj" name="rodzaj" value="<?=htmlspecialchars($rodzaj)?>"></td>
                </tr>
                <tr>
                    <th>Opis skierowania</th>
                    <td><textarea id="opis" name="opis" rows="4"><?=htmlspecialchars($opis)?></textarea></td>
                </tr>
                <tr>
                    <th>Lekarz</th>
                    <td>
                        <select name="lekarz">
                        <?php
                            foreach ($lekarze as $lekarz) {
                                if($lekarz[0] == $_POST['lekarz'])
                                echo "<option selected value=".$lekarz[0].">".$lekarz[1]." ".$lekarz[2]." ".$lekarz[3]."</option>";
                                else
                                echo "<option value=".$lekarz[0].">".$lekarz[1]." ".$lekarz[2]." ".$lekarz[3]."</option>";
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Data skierowania</th>
                    <td><input type="date" id="data_skierowania" name="data_skierowania" value="<?=htmlspecialchars($data_skierowania)?>"></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <input type="submit" value="Wystaw skierowanie">
                    </td>
                </tr>
            </table>
        </form>
        <?php if ($blendy): ?>
            <p style="color: red;"><?=$blendy?></p>
        <?php endif; ?>
        <?php if ($zapisano): ?>
            <h2>Zapisano skierowanie:</h2>
            <?php
            //odczytywanie z pliku
            $plik = fopen($sciezka, 'r');
            if ($plik) {
                echo '<pre>';
                while (($linia = fgets($plik)) !== false) {
                    echo htmlspecialchars($linia);
                }
                echo '</pre>';
                fclose($plik);
            } else {
                echo "Nie można otworzyć pliku do odczytu.";
            }
            ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>