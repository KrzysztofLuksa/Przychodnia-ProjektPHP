<?php
include 'funkcje.php';
if (!empty($_POST['idWizyty']) && !empty($_POST['idPacjenta']) && isset($_POST['ocena'])) { // jelsi e tablicy post nie ma tych danych to uzytkownik zostaje przekierowany do google
    $idWizyty = $_POST['idWizyty'];
    $idPacjenta = $_POST['idPacjenta'];
    $ocena = $_POST['ocena'];

    global $polaczenie;
    otworz_polaczenie();

    //zapytanie aktualizacji oceny
    $rozkaz = "update wizyty SET ocena='$ocena' where numer='$idWizyty' AND pacjent='$idPacjenta'";
    $odpowiedz = mysqli_query($polaczenie, $rozkaz);

    if ($odpowiedz) {
        $message = "Dziękujemy za ocenę.";
    } else {
        $message = "Problem z zapytaniem do bazy: " . $rozkaz;
    }

   zamknij_polaczenie();
} else {
    header("location: https://google.pl");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ocena zapisana</title>
    <style>
        body, html {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            background-color: lightgray;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            width: 100vw;
        }
        #konternerWiadomosci {
            background-color: whitesmoke;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
    </style>    
</head>
<body>
    <!-- wyswietlanie wiadomosci -->
    <div id="konternerWiadomosci">
        <h1><?php echo $message; ?></h1>
    </div>
</body>
</html>