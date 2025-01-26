-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sty 26, 2025 at 07:37 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `przychodnia`
--
CREATE DATABASE IF NOT EXISTS `przychodnia` DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci;
USE `przychodnia`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `admin`
--

CREATE TABLE `admin` (
  `numer` int(11) NOT NULL,
  `login` varchar(32) DEFAULT NULL,
  `haslo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`numer`, `login`, `haslo`) VALUES
(1, 'admin', '$2y$10$Ro82IL.8i8bUd1YT/z6F7u84v0PYqqHSk3RterTbUkoUZZ/SdfZKS');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pacjenci`
--

CREATE TABLE `pacjenci` (
  `numer` int(11) NOT NULL,
  `imie` varchar(32) DEFAULT NULL,
  `nazwisko` varchar(32) DEFAULT NULL,
  `pesel` varchar(11) DEFAULT NULL,
  `adres` varchar(128) DEFAULT NULL,
  `telefon` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `pacjenci`
--

INSERT INTO `pacjenci` (`numer`, `imie`, `nazwisko`, `pesel`, `adres`, `telefon`) VALUES
(1, 'Jan', 'Kowalski', '12345678901', 'ul. Przykładowa 1, 00-001 Warszawa', '123456789'),
(2, 'Anna', 'Nowak', '09876543210', 'ul. Testowa 2, 00-002 Kraków', '987654321');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `personel`
--

CREATE TABLE `personel` (
  `numer` int(11) NOT NULL,
  `imie` varchar(32) DEFAULT NULL,
  `nazwisko` varchar(32) DEFAULT NULL,
  `stanowisko` varchar(32) DEFAULT NULL,
  `specjalizacja` varchar(64) DEFAULT NULL,
  `godziny_pracy` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `personel`
--

INSERT INTO `personel` (`numer`, `imie`, `nazwisko`, `stanowisko`, `specjalizacja`, `godziny_pracy`) VALUES
(1, 'Jan', 'Kowalski', 'Lekarz', 'Kardiolog', 'Pon-Pt 8:00-16:00'),
(2, 'Anna', 'Nowak', 'Lekarz', 'Dermatolog', 'Pon-Pt 9:00-17:00'),
(3, 'Piotr', 'Zieliński', 'Księgowy', '', 'Pon-Pt 8:00-16:00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `wizyty`
--

CREATE TABLE `wizyty` (
  `numer` int(11) NOT NULL,
  `data` date DEFAULT NULL,
  `godzina` time DEFAULT NULL,
  `pacjent` int(11) DEFAULT NULL,
  `lekarz` int(11) DEFAULT NULL,
  `ocena` int(11) DEFAULT NULL,
  `opis` text DEFAULT NULL,
  `diagnoza` text DEFAULT NULL,
  `zalecenia` text DEFAULT NULL,
  `leki_przepisane` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Dumping data for table `wizyty`
--

INSERT INTO `wizyty` (`numer`, `data`, `godzina`, `pacjent`, `lekarz`, `ocena`, `opis`, `diagnoza`, `zalecenia`, `leki_przepisane`) VALUES
(1, '2023-10-01', '10:00:00', 1, 1, 4, 'Opis wizyty 1', 'Diagnoza 1', 'Zalecenia 1', 'Leki 1'),
(2, '2023-10-02', '11:00:00', 2, 2, 4, 'Opis wizyty 2', 'Diagnoza 2', 'Zalecenia 2', 'Leki 2'),
(3, '2023-12-01', '09:00:00', 1, 1, 4, NULL, NULL, NULL, NULL),
(4, '2023-12-02', '10:00:00', 2, 2, NULL, NULL, NULL, NULL, NULL),
(5, '2023-12-03', '11:00:00', 1, 2, NULL, NULL, NULL, NULL, NULL),
(6, '2023-12-04', '12:00:00', 2, 1, NULL, NULL, NULL, NULL, NULL);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`numer`);

--
-- Indeksy dla tabeli `pacjenci`
--
ALTER TABLE `pacjenci`
  ADD PRIMARY KEY (`numer`);

--
-- Indeksy dla tabeli `personel`
--
ALTER TABLE `personel`
  ADD PRIMARY KEY (`numer`);

--
-- Indeksy dla tabeli `wizyty`
--
ALTER TABLE `wizyty`
  ADD PRIMARY KEY (`numer`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `numer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pacjenci`
--
ALTER TABLE `pacjenci`
  MODIFY `numer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `personel`
--
ALTER TABLE `personel`
  MODIFY `numer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wizyty`
--
ALTER TABLE `wizyty`
  MODIFY `numer` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
