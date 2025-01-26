<?php
include '../funkcje.php';
session_start();
if(!$_SESSION['admin']) {
    header("location: index.php");
} else {
    otworz_polaczenie();
    global $polaczenie;
    $personel = "";
    $wynik_personel = mysqli_query($polaczenie, "select * from personel") or exit("Błąd w pobieraniu listy personelu");
    if($wynik_personel) $personel .= "<tr><th>Imie</th><th>Nazwisko</th><th>Stanowisko</th><th>Specjalizacja</th><th>Godziny pracy</th><th><input type='submit' name='przycisk[-1]' value='Dodaj nowego'></th></tr>";
    while($wiersz = mysqli_fetch_row($wynik_personel)) { // wrzucanie wynikow zapytania do tabeli
        $personel .= "<tr><td>".$wiersz[1]."</td><td>".$wiersz[2]."</td><td>".$wiersz[3]."</td><td>".$wiersz[4]."</td><td>".$wiersz[5]."</td><td align='center'><input type='submit' name='przycisk[$wiersz[0]]' value='Edytuj'>
                                      <input type='submit' name='przycisk[$wiersz[0]]' value='Usuń'></td></tr>";
    }
    mysqli_free_result($wynik_personel);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personel</title>
    <style>
    body, html {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            overflow: hidden;
            width: 100vw;
            height: 100vh;
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
//edytowanie badz tworzenie nowego czlonka personelu
function edytuj_personel($nr = -1) {
    global $polaczenie;    
    if($nr != -1) { 
        $rozkaz = "select imie, nazwisko, stanowisko, specjalizacja, godziny_pracy from personel where numer=$nr";
        $rekord = mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);
                
        $personel = mysqli_fetch_row($rekord);
        $imie = $personel[0];
        $nazwisko = $personel[1];
        $stanowisko = $personel[2];
        $specjalizacja = $personel[3];   
        $godzinyPracy = $personel[4];
        mysqli_free_result($rekord); 
    }
    else {
        $imie=''; $nazwisko=''; $stanowisko=''; $specjalizacja=''; $godzinyPracy='';
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
    <td>Stanowisko</td><td colspan=2>
    <input type=text name='stanowisko' value='<?=$stanowisko?>' size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td>Specjalizacja</td><td colspan=2>
    <input type=text name='specjalizacja' value='<?=$specjalizacja?>' size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td>Godziny pracy</td><td colspan=2>
    <input type=text name='godziny' value='<?=$godzinyPracy?>' size=15 style='text-align: left'></td>
    </tr>
    <tr>
    <td colspan=3>
    <input type=submit name='przycisk[<?=$nr?>]' value='Zapisz' style='100%'></td>
    </tr>
    </table></form>
    
<?php 
}

//usuwanie personelu
function usun_personel($nr) {
    global $polaczenie;    
        $rozkaz = "delete from personel where numer=$nr";
        mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);
        header("Location: personel.php");
}

//zapisywanie badz aktualizowanie danych personlu
function zapisz_personel($nr) {
    global $polaczenie;
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $stanowisko = $_POST['stanowisko'];
    $specjalizacja = $_POST['specjalizacja'];
    $godziny = $_POST['godziny'];
    if(empty($imie) || empty($nazwisko) || empty($stanowisko)) return;
    if($nr != -1)
        $rozkaz = "update personel set imie='$imie', nazwisko='$nazwisko', stanowisko='$stanowisko', specjalizacja='$specjalizacja', godziny_pracy='$godziny' where numer=$nr";
    else $rozkaz = "insert into personel values(null, '$imie', '$nazwisko', '$stanowisko', '$specjalizacja', '$godziny')";        
    mysqli_query($polaczenie, $rozkaz) or exit("Błąd w zapytaniu: ".$rozkaz);    
    header("Location: personel.php");
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
    <?php
        //obslugiwanie polecenia
        $polecenie = '';
        if(isset($_POST['przycisk'])) {    
            $nr = key($_POST['przycisk']);
            $polecenie = $_POST['przycisk'][$nr];
        }

        switch($polecenie) {
            case 'Edytuj': edytuj_personel($nr); break;
            case 'Dodaj nowego': edytuj_personel(); break;
            case 'Zapisz': zapisz_personel($nr); break;
            case 'Usuń': usun_personel($nr); break;
        }

        zamknij_polaczenie();
    ?>
        <form action="" method="post">
        <table border="1">
            <?=$personel?>
        </table>
        </form>
    </div>
    </div>
</body>
</html>