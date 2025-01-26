<?php
include '../funkcje.php';
session_start();
if(!$_SESSION['admin']) {
    header("location: index.php");
} else {
    otworz_polaczenie();
    global $polaczenie;
    $wizyty = "";
    $pesel_filter = isset($_POST['pesel_filter']) ? $_POST['pesel_filter'] : '';
    $data_od = isset($_POST['data_od']) ? $_POST['data_od'] : '';
    $data_do = isset($_POST['data_do']) ? $_POST['data_do'] : '';

    $zapytanie = "select * from wizyty";
    $warunki = [];

    //filtry wyszukiwania
    if (!empty($pesel_filter)) {
        $zapytanie_pacjent = "select numer from pacjenci where pesel = '$pesel_filter'";
        $wynik_pacjent = mysqli_query($polaczenie, $zapytanie_pacjent) or exit("Błąd w zapytaniu: " . $zapytanie_pacjent);
        $pacjent = mysqli_fetch_row($wynik_pacjent);
        mysqli_free_result($wynik_pacjent);
        if ($pacjent) {
            $warunki[] = "pacjent = " . $pacjent[0];
        } else {
            $warunki[] = "0";//jesli nie ma zadnych wynikow
        }
    }

    if (!empty($data_od)) {
        $warunki[] = "data >= '$data_od'";
    }
    if (!empty($data_do)) {
        $warunki[] = "data <= '$data_do'";
    }

    if (!empty($warunki)) {
        $zapytanie .= " where " . implode(" AND ", $warunki);
    }

    //pobieranie danych z bazy
    $wynik = mysqli_query($polaczenie, $zapytanie) or exit("Błąd w zapytaniu: " . $zapytanie);
    if($wynik) {
        $wizyty .= "<tr><th>Data</th><th>Godzina</th><th>Pacjent</th><th>Pesel pacjenta</th><th>Lekarz</th><th>Specjalizacja lekarza</th><th>Ocena</th><th>Opis</th><th>Diagnoza</th><th>Zalecenia</th><th>Przepisane leki</th>
        <th><input type='submit' name='przycisk[-1]' value='Dodaj nową'></th></tr>";
        while ($wiersz = mysqli_fetch_row($wynik)) {
            $kluczLekarza = $wiersz[4];
            $kluczPacjenta = $wiersz[3];
        
            $wynik_pacjent = mysqli_query($polaczenie, "select imie, nazwisko, pesel from pacjenci where numer = $kluczPacjenta");
            if ($wynik_pacjent && mysqli_num_rows($wynik_pacjent) > 0) {
                $dane_pacjent = mysqli_fetch_row($wynik_pacjent);
            } else {
                $dane_pacjent = ["", "", ""];
            }
        
            $wynik_lekarz = mysqli_query($polaczenie, "select imie, nazwisko, specjalizacja from personel where numer = $kluczLekarza");
            if ($wynik_lekarz && mysqli_num_rows($wynik_lekarz) > 0) {
                $dane_lekarz = mysqli_fetch_row($wynik_lekarz);
            } else {
                $dane_lekarz = ["", "", ""];
            }
        
            $wizyty .= "<tr><td>".$wiersz[1]."</td><td>".$wiersz[2]."</td><td>".$dane_pacjent[0]." ".$dane_pacjent[1]."</td><td>".$dane_pacjent[2]."</td><td>".$dane_lekarz[0]." ".$dane_lekarz[1]."</td><td>".
            $dane_lekarz[2]."<td>".$wiersz[5]."</td>"."<td>".$wiersz[6]."</td>"."<td>".$wiersz[7]."</td>"."<td>".$wiersz[8]."</td>"."<td>".$wiersz[9]."</td>".
            "</td><td align='center'><input type='submit' name='przycisk[$wiersz[0]]' value='Edytuj'>
                                          <input type='submit' name='przycisk[$wiersz[0]]' value='Usuń'></td></tr>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wizyty</title>
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
<?php
//edycja lub tworzenie nowej wizyty
function edytuj_wizyte($nr = -1, $pacjent = null) {
    global $polaczenie;
    $data = '';
    $godzina = '';
    $imiePacjenta = '';
    $nazwiskoPacjenta = '';
    $peselPacjenta = '';
    $opis = '';
    $diagnoza = '';
    $zalecenia = '';
    $leki_przepisane = '';

    if ($nr != -1) {
        $rozkaz = "select data, godzina, pacjent, lekarz, ocena, opis, diagnoza, zalecenia, leki_przepisane from wizyty where numer=$nr";
        $rekord = mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: " . $rozkaz);
        $wizyta = mysqli_fetch_row($rekord);
        $data = $wizyta[0];
        $godzina = $wizyta[1];
        $lekarzWizyta = $wizyta[3];
        $opis = $wizyta[5];
        $diagnoza = $wizyta[6];
        $zalecenia = $wizyta[7];
        $leki_przepisane = $wizyta[8];
        if ($pacjent === null) {
            $pacjent = $wizyta[2];//jesli pacjent == null przypisz mu numer pacjenta z wizyty
        }
        mysqli_free_result($rekord);
    }

    if ($pacjent !== null) {
        $rozkaz2 = "select imie, nazwisko, pesel from pacjenci where numer=$pacjent";
        $rekord2 = mysqli_query($polaczenie, $rozkaz2) or exit("Błąd w zapytaniu: " . $rozkaz2);
        $pacjent_dane = mysqli_fetch_row($rekord2);
        $imiePacjenta = $pacjent_dane[0];
        $nazwiskoPacjenta = $pacjent_dane[1];
        $peselPacjenta = $pacjent_dane[2];
        mysqli_free_result($rekord2);
    }

    $rozkaz3 = "select numer, imie, nazwisko, specjalizacja from personel where stanowisko='lekarz'";
    $rekord3 = mysqli_query($polaczenie, $rozkaz3) or exit("Błąd w zapytaniu: " . $rozkaz3);
    $lekarze = [];
    while ($wiersz = mysqli_fetch_row($rekord3)) {
        $lekarze[] = $wiersz;
    }
    mysqli_free_result($rekord3);

    ?>
    <form method="POST" action="">
    <table border=0>
    <input type="hidden" name="strona" value="wizyty.php">
    <input type="hidden" name="wybrany_pacjent" value="<?=$pacjent?>">
    <tr>
    <td>Imię pacjenta</td><td colspan=2>
    <input type="text" name="imie_pacjenta" readonly value="<?=$imiePacjenta?>" size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td>Nazwisko pacjenta</td><td colspan=2>
    <input type="text" name="nazwisko_pacjenta" readonly value="<?=$nazwiskoPacjenta?>" size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td>Pesel pacjenta</td><td colspan=2>
    <input type="text" name="pesel_pacjenta" readonly value="<?=$peselPacjenta?>" size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td>Wybierz pacjenta</td>
    <td  colspan="2">
    <input type="submit" name="przycisk[<?=$nr?>]" value="Wybierz pacjenta" onclick="this.form.action='wybierzpacjenta.php?nr=<?=$nr?>'" style='100%'>
    </td>
    </tr>
    <tr>
    <td>Data</td><td colspan=2>
    <input type="date" name="data" value="<?=$data?>" size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td>Godzina</td><td colspan=2>
    <input type="time" name="godzina" value="<?=$godzina?>" size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td>Lekarz</td>
    <td colspan=2>
        <select name="lekarz" required>
            <?php
            foreach($lekarze as $lekarz) {
                if($lekarz[0] == $lekarzWizyta) 
                echo "<option selected value=".$lekarz[0].">".$lekarz[1]." ".$lekarz[2]." ".$lekarz[3]."</option>";
                else
                echo "<option value=".$lekarz[0].">".$lekarz[1]." ".$lekarz[2]." ".$lekarz[3]."</option>";
            }
             ?>
        </select>
    </td>
    </tr>
    <?php if ($nr != -1) {
 ?>
    <tr>
    <td>Opis</td><td colspan=2>
    <textarea name="opis" rows="4" cols="50"><?=$opis?></textarea></td>
    </tr>
    <tr>
    <td>Diagnoza</td><td colspan=2>
    <textarea name="diagnoza" rows="4" cols="50"><?=$diagnoza?></textarea></td>
    </tr>
    <tr>
    <td>Zalecenia</td><td colspan=2>
    <textarea name="zalecenia" rows="4" cols="50"><?=$zalecenia?></textarea></td>
    </tr>
    <tr>
    <td>Leki przepisane</td><td colspan=2>
    <textarea name="leki_przepisane" rows="4" cols="50"><?=$leki_przepisane?></textarea></td>
    </tr>
    <tr>
        <?php } ?>
    <td colspan=2>
    <input type="submit" name="przycisk[<?=$nr?>]" value="Zapisz" style='100%'></td>
    <td>
    <input type="button" value="Anuluj" onclick="window.location.href='wizyty.php'" style="width: 100%;">
 </td>
    
    </tr>
    </table></form>
    <?php
    
}
//usuwanie danej wizyty zgodnie z jej numerem
function usun_wizyte($nr) {
    global $polaczenie;	
        $rozkaz = "delete from wizyty where numer=$nr";
        mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);
        header("Location: wizyty.php");
}
//zapisywanie badz edycja gotowej wizyty
function zapisz_wizyte($nr) {
    global $polaczenie;
    $data = $_POST['data'];
    $godzina = $_POST['godzina'];
    $pesel_pacjenta = $_POST['pesel_pacjenta'];
    $wybranyPacjent = $_POST['wybrany_pacjent'];
    $lekarz = $_POST['lekarz'];
    $opis = isset($_POST['opis']) ? $_POST['opis'] : null;
    $diagnoza = isset($_POST['diagnoza']) ? $_POST['diagnoza'] : null;
    $zalecenia = isset($_POST['zalecenia']) ? $_POST['zalecenia'] : null;
    $leki_przepisane = isset($_POST['leki_przepisane']) ? $_POST['leki_przepisane'] : null;
    if(empty($data) || empty($godzina) || empty($pesel_pacjenta) || empty($lekarz)) return;

    if($nr != -1) {
        $rozkaz = "update wizyty SET data='$data', godzina='$godzina', pacjent='$wybranyPacjent', lekarz='$lekarz', opis='$opis', diagnoza='$diagnoza', zalecenia='$zalecenia', leki_przepisane='$leki_przepisane' where numer=$nr";
    } else {
        $rozkaz = "insert into wizyty (data, godzina, pacjent, lekarz) VALUES ('$data', '$godzina', '$wybranyPacjent', '$lekarz')";
    }

    mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);	
    header("Location: wizyty.php");
}
?>


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
        <table>
            <tr>
                <td><input type="text" id="pesel_filter" name="pesel_filter" placeholder="PESEL" value="<?=$pesel_filter?>"></td>
                <td><input type="date" id="data_od" name="data_od" value="<?=$data_od?>"></td>
                <td><input type="date" id="data_do" name="data_do" value="<?=$data_do?>"></td>
                <td><input type="submit" value="Szukaj"></td>
                <td><input type="button" value="Wyczyść" onclick="window.location.href='wizyty.php'"></td>
            </tr>
        </table>
    </form>
    <?php
        $polecenie = ''; //oblusgiwanie polecenia
        if(isset($_POST['przycisk'])) {	
            $nr = key($_POST['przycisk']);
            $polecenie = $_POST['przycisk'][$nr];
        }

        switch($polecenie) {
            case 'Edytuj': edytuj_wizyte($nr); break;
            case 'Dodaj nową': edytuj_wizyte(); break;
            case 'Zapisz': zapisz_wizyte($nr); break;
            case 'Usuń': usun_wizyte($nr); break;
        }

        if (!empty($_POST['wybrany'])) {
            $key = array_keys($_POST['wybrany'])[0];
            list($pacjent, $nr) = explode(',', $key);
            $pacjent = trim($pacjent);
            $nr = trim($nr);
            edytuj_wizyte($nr, $pacjent);
        }

        zamknij_polaczenie();
    ?>
        <form action="" method="post">
        <table border="1">
            <?=$wizyty?>
        </table>
        </form>
    </div>
    </div>
</body>
</html>