<?php
include '../funkcje.php';
session_start();
if(!$_SESSION['admin']) { //sprawdzenie czy uzytkownik posiada zmienna w sesji admin jesli nie przekierowywany jest na index.php
    header("location: index.php");
} else {
    otworz_polaczenie();
    global $polaczenie;
    $pacjenci = "";
    $imie_filter = isset($_POST['imie_filter']) ? $_POST['imie_filter'] : '';
    $nazwisko_filter = isset($_POST['nazwisko_filter']) ? $_POST['nazwisko_filter'] : '';
    $pesel_filter = isset($_POST['pesel_filter']) ? $_POST['pesel_filter'] : '';

    //tworzenie zapytania z ewentualnymi filtrami 
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
    //wstawianie danych do tabeli wraz z przyciskami z danym id pacjenta
    $odpowiedz = mysqli_query($polaczenie, $zapytanie) or exit("Błąd w pobieraniu listy pacjentow");
    if($odpowiedz) $pacjenci .= "<tr><th>Imie</th><th>Nazwisko</th><th>Pesel</th><th>Adres</th><th>Telefon</th><th><input type='submit' name='przycisk[-1]' value='Dodaj nowego'></th></tr>";
    while($wiersz = mysqli_fetch_row($odpowiedz)) {
        $pacjenci .= "<tr><td>".$wiersz[1]."</td><td>".$wiersz[2]."</td><td>".$wiersz[3]."</td><td>".$wiersz[4]."</td><td>".$wiersz[5]."</td><td align='center'><input type='submit' name='przycisk[$wiersz[0]]' value='Edytuj'>
                                      <input type='submit' name='przycisk[$wiersz[0]]' value='Usuń'></td></tr>";
    }
    mysqli_free_result($odpowiedz);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacjenci</title>
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
//funkcja dzieki ktorej mozemy edytowac badz dodawac nowego pacjenta
function edytuj_pacjenta($nr = -1) {
    global $polaczenie;    
    if($nr != -1) { 
        $rozkaz = "select imie, nazwisko, pesel, adres, telefon from pacjenci where numer=$nr";
        $rekord = mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);
                
        $pacjent = mysqli_fetch_row($rekord);
        $imie = $pacjent[0];
        $nazwisko = $pacjent[1];
        $pesel = $pacjent[2];
        $adres = $pacjent[3];   
        $telefon = $pacjent[4];  
        mysqli_free_result($rekord);
    }
    else {
        $imie=''; $nazwisko=''; $pesel=''; $adres=''; $telefon='';
    }
    
    ?>
    <form method=POST action=''> 
    <table border=0>
    <tr>
    <td>Imie</td><td colspan=2>
    <input type=text name='imie' value='<?=$imie?>' size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td>Nazwisko</td><td colspan=2>
    <input type=text name='nazwisko' value='<?=$nazwisko?>' size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td>Pesel</td><td colspan=2>
    <input type=text name='pesel' value='<?=$pesel?>' size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td>Adres</td><td colspan=2>
    <input type=text name='adres' value='<?=$adres?>' size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td>Telefon</td><td colspan=2>
    <input type=text name='telefon' value='<?=$telefon?>' size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td colspan=3>
    <input type=submit name='przycisk[<?=$nr?>]' value='Zapisz' style="width: 100%;"></td>
    <tr>
    <td colspan="3">
    <input type="button" value="Anuluj" onclick="window.location.href='pacjenci.php'" style="width: 100%;"></td>
    </tr>
    
    </tr>
    </table></form>
    
<?php 
}
//funkcja ktora odpowiada za usuwanie pacjenta
function usun_pacjenta($nr) {
    global $polaczenie;    
        $rozkaz = "delete from pacjenci where numer=$nr";
        mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);
        header("Location: pacjenci.php");
}

//funckja odpowiadajaca za zapisywanie badz edycji juz istniejacego pacjenta
function zapisz_pacjenta($nr) {
    global $polaczenie;
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $pesel = $_POST['pesel'];
    $adres = $_POST['adres'];
    $telefon = $_POST['telefon'];
    if(empty($imie) || empty($nazwisko) || empty($pesel)) return;
    if($nr != -1)
        $rozkaz = "update pacjenci set imie='$imie', nazwisko='$nazwisko', pesel='$pesel', adres='$adres', telefon='$telefon' where numer=$nr";
    else $rozkaz = "insert into pacjenci values(null, '$imie', '$nazwisko', '$pesel', '$adres', '$telefon')";        
    mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);    
    header("Location: pacjenci.php");
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
                <td><input type="text" id="imie_filter" name="imie_filter" placeholder="Imię" value="<?=$imie_filter?>"></td>
                <td><input type="text" id="nazwisko_filter" name="nazwisko_filter" placeholder="Nazwisko" value="<?=$nazwisko_filter?>"></td>
                <td><input type="text" id="pesel_filter" name="pesel_filter" placeholder="PESEL" value="<?=$pesel_filter?>"></td>
                <td><input type="submit" value="Szukaj"></td>
                <td><input type="button" value="Wyczyść" onclick="window.location.href='pacjenci.php'"></td>
            </tr>
        </table>
    </form>
    <?php
        //odczytywanie polecenia 
        $polecenie = '';
        if(isset($_POST['przycisk'])) {    
            $nr = key($_POST['przycisk']);
            $polecenie = $_POST['przycisk'][$nr];
        }

        switch($polecenie) {
            case 'Edytuj': edytuj_pacjenta($nr); break;
            case 'Dodaj nowego': edytuj_pacjenta(); break;
            case 'Zapisz': zapisz_pacjenta($nr); break;
            case 'Usuń': usun_pacjenta($nr); break;
        }

        zamknij_polaczenie();
    ?>
        <form action="" method="post">
        <table border="1">
            <?=$pacjenci?>
        </table>
        </form>
    </div>
    </div>
</body>
</html>