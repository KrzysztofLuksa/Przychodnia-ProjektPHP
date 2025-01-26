<?php
include 'funkcje.php';

// ocena.php?idWizyty=wartosc&idPacjenta=wartosc url do strony
if (!empty($_GET['idWizyty']) && !empty($_GET['idPacjenta'])) {
    $idWizyty = $_GET['idWizyty'];
    $idPacjenta = $_GET['idPacjenta'];
    global $polaczenie;
    otworz_polaczenie();
    $showForm = false;

    // Zapytanie SQL
    $rozkaz = "select ocena from wizyty where numer='$idWizyty' AND pacjent='$idPacjenta'";
    $odpowiedz = mysqli_query($polaczenie, $rozkaz) or exit("Problem z zapytaniem do bazy: " . $rozkaz);

    if ($odpowiedz) {
        // Pobierz pierwszy wiersz wyników jako tablicę
        $row = mysqli_fetch_row($odpowiedz);
        if ($row) {
            if (!empty($row[0])) {
                $ocena = $row[0];
                $message = "Wizyta została już oceniona na: $ocena/5";
            } else {
                $message = "Oceń wizytę:";
                $showForm = true;
            }
        } else {
            header("location: https://google.pl");
        }

        // Zwolnij wynik zapytania
        mysqli_free_result($odpowiedz);
        zamknij_polaczenie();
    } else {
        header("Location: https://google.pl");

    }
} else {
    header("Location: https://google.pl");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Przychodnia - Ocena</title>
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
        #kontenerOceny {
            background-color: whitesmoke;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        form {
            margin-top: 20px;
        }
        input[type="submit"] {
            background-color: green;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: darkgreen;
        }
    </style>
</head>
<body>
    <div id="kontenerOceny">
        <h1><?php echo $message; ?></h1>
        <?php if($showForm) { ?>
            <form method="POST" action="zapisz_ocene.php">
                <input type="hidden" name="idWizyty" value=<?=$idWizyty?>>
                <input type="hidden" name="idPacjenta" value=<?=$idPacjenta?>>
                <table>
                    <tr>
                        <th>Ocena (0-5):</th>
                        <td>
                            <select id="ocena" name="ocena" required>
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: center;">
                            <input type="submit" value="Zapisz ocenę">
                        </td>
                    </tr>
                </table>
            </form>
        <?php } ?>
    </div>
</body>
</html>