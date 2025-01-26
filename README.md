# Przychodnia
Aplikacja umożliwia zarządzanie danymi pacjentów, personelu oraz wizyt w przychodni. Administratorzy mają możliwość zarządzania wszystkimi danymi, a pacjenci mogą oceniać swoje wizyty. Dane są przechowywane w bazie danych, a dodatkowe informacje (np. recepty, skierowania) zapisywane są w plikach tekstowych.

Dane do logowania login: admin hasło: admin

Widoki i Moduły Aplikacji

1. index.php  Widok Logowania
Opis:
Strona logowania dla administratora aplikacji.
Weryfikuje dane logowania (login i hasło) z tabeli Admin w bazie danych.
Logowanie:
Formularz wprowadza login i hasło.
Login i hasło weryfikowane z bazą danych.
Wylogowanie:
Dodanie ?wylogowanie do URL niszczy sesję i przekierowuje na stronę logowania.
Walidacja:
Komunikaty błędów:
„Błędny login lub hasło” – niepoprawne dane.
„Wypełnij wszystkie pola” – niekompletny formularz.

2. panel.php  Główny Widok Administracyjny
Opis:
Wyświetla podsumowanie ostatnich wizyt i pacjentów.
Umożliwia szybki dostęp do innych widoków aplikacji.

Funkcjonalności:
Podgląd danych:
Ostatnie wizyty: Maksymalnie 10 wizyt (data, godzina, pacjent, lekarz).
Ostatni pacjenci: Maksymalnie 10 pacjentów (imię, nazwisko, adres, telefon).
Nawigacja:
Linki do: Personel, Pacjenci, Wizyty, Recepta, Skierowanie, Raport.
Opcja wylogowania.

3. pacjenci.php - Zarządzanie Pacjentami
Opis:
Widok umożliwia przeglądanie, filtrowanie, dodawanie, edytowanie i usuwanie pacjentów.

Funkcjonalności:
Wyświetlanie pacjentów: Imię, nazwisko, PESEL, adres, telefon.
Filtrowanie: Wyszukiwanie po imieniu, nazwisku i PESEL.
Dodawanie nowego pacjenta: Formularz umożliwia dodanie nowego pacjenta.
Edycja i usuwanie: Możliwość modyfikacji lub usunięcia danych pacjenta.

4. wizyty.php - Zarządzanie Wizytami
Opis:
Widok do przeglądania, filtrowania, dodawania, edytowania i usuwania wizyt.

Funkcjonalności:
Wyświetlanie wizyt: Data, godzina, pacjent, lekarz, diagnoza, zalecenia, leki.
Filtrowanie wizyt: PESEL pacjenta i zakres dat.
Dodawanie wizyty: Formularz umożliwia tworzenie nowej wizyty.
Edycja wizyty: Modyfikacja istniejącej wizyty.
Usuwanie wizyty: Możliwość usunięcia wizyty.
Wybór pacjenta: Widok korzysta z wybierzpacjenta.php

5. recepta.php - Wystawianie Recepty
Opis:
Widok umożliwia wystawianie recepty i zapisanie jej w pliku tekstowym na serwerze.

Funkcjonalności:
Tworzenie recepty:
Dane pacjenta (imię, nazwisko, PESEL) automatycznie uzupełniane po wyborze.
Wprowadzenie leków, opisu recepty, daty ważności.
Wybór lekarza.
Walidacja:
Wszystkie pola są wymagane.
Data ważności nie może być wcześniejsza niż bieżąca.
Zapisywanie recepty:
Plik: recepta_peselPacjenta_dokladnadatazgodzina.txt.
Widok korzysta z wybierzpacjenta.php.
6. skierowanie.php - Wystawianie Skierowania
Opis:
Widok umożliwia wystawianie skierowań dla pacjentów.

Funkcjonalności:
Tworzenie skierowania:
Dane pacjenta (imię, nazwisko, PESEL) automatycznie uzupełniane po wyborze.
Wprowadzenie rodzaju i opisu skierowania, daty skierowania.
Wybór lekarza.
Walidacja:
Wszystkie pola są wymagane.
Data skierowania nie może być wcześniejsza niż bieżąca.
Zapisywanie skierowania:
Plik: skierowanie_peselPacjenta_dokladnadatazgodzina.txt.
Widok korzysta z wybierzpacjenta.php.
7. raport.php - Generowanie Raportów
Opis:
Widok generuje raport statystyczny na temat pacjentów, wizyt i lekarzy.

Funkcjonalności:
Statystyki ogólne: Liczba pacjentów, wizyt, lekarzy, średnia liczba wizyt na lekarza.
Statystyki lekarzy: Liczba wizyt i średnia ocena dla każdego lekarza.
Filtracja wizyt: Statystyki wizyt w wybranym zakresie dat.

8. ocena.php - Ocena Wizyty
Opis:
Widok umożliwia pacjentom ocenienie wizyty w skali od 0 do 5.

Funkcjonalności:
Formularz oceny:
Pacjent wybiera ocenę i zapisuje ją w bazie danych.
Wyświetlanie istniejącej oceny:
Jeśli wizyta została oceniona, wyświetlana jest jej wartość.


Pliki Funkcjonalne

funkcje.php
Funkcje wspierające aplikację:

otworz_polaczenie(): Nawiązuje połączenie z bazą danych.
zamknij_polaczenie(): Zamyka połączenie z bazą danych.

wybierzpacjenta.php
Opis:
Widok umożliwia wybór pacjenta do przypisania do wizyty, recepty lub skierowania.

Funkcjonalności:
Wyświetlanie pacjentów: Lista pacjentów z możliwością filtrowania.
Filtrowanie: Imię, nazwisko, PESEL.
Wybór pacjenta: Przycisk "Wybierz" przypisuje pacjenta do wywołującego widoku.

Widoki korzystające z wybierzpacjenta.php
wizyty.php
recepta.php
skierowanie.php
Format Przechowywania Danych
Baza danych:

Tabela Admin: login, hasło.
Tabela Pacjenci: numer, imię, nazwisko, PESEL, adres, telefon.
Tabela Personel: numer, imię, nazwisko, stanowisko, specjalizacja.
Tabela Wizyty: numer, data, godzina, pacjent, lekarz, ocena, diagnoza, zalecenia, leki.

Pliki tekstowe:
Recepty: recepta_peselPacjenta_dokladnadatazgodzina.txt.
Skierowania: skierowanie_peselPacjenta_dokladnadatazgodzina.txt.
Schemat Połączeń Widoków

Logowanie (index.php)
Po zalogowaniu → panel.php.

Panel (panel.php)
Linki do: pacjenci.php, wizyty.php, recepta.php, skierowanie.php, raport.php.

Pacjenci (pacjenci.php)
Możliwość edycji, dodania i usunięcia pacjenta.

Wizyty (wizyty.php)
Możliwość edycji, dodania i usunięcia wizyty.

Wybór pacjenta z wybierzpacjenta.php.
Recepta/Skierowanie

Formularz do tworzenia dokumentu.
Wybór pacjenta z wybierzpacjenta.php.

Raporty (raport.php)
Generowanie raportów statystycznych.

Ocena Wizyty (ocena.php)
Formularz oceny wizyty.
