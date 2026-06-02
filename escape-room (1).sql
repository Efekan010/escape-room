-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Gegenereerd op: 02 jun 2026 om 11:14
-- Serverversie: 10.4.32-MariaDB
-- PHP-versie: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `escape-room`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `room_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL COMMENT '1-5 sterren',
  `difficulty` tinyint(4) NOT NULL COMMENT '1-5 moeilijkheid',
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `riddles`
--

CREATE TABLE `riddles` (
  `id` int(11) NOT NULL,
  `riddle` text NOT NULL,
  `answer` varchar(255) NOT NULL,
  `hint` varchar(255) DEFAULT NULL,
  `roomId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `riddles`
--

INSERT INTO `riddles` (`id`, `riddle`, `answer`, `hint`, `roomId`) VALUES
(1, 'Op de keldermuur krassen 13 strepen. Elke dag verdwijnt er één. Na 6 dagen, hoeveel strepen zijn er nog?', '7', 'Trek 6 af van 13.', 1),
(2, 'Een oud hangslot heeft een 3-cijferige code. Aanwijzing op de vloer: \"Het eerste cijfer is het aantal letters in DOOD. Het tweede is het dubbele van 3. Het derde is 10 min 7.\"', '467', 'Dood = 4 letters, 3×2=6, 10-7=3.', 1),
(3, 'Er ligt een dagboek open op de tafel. De laatste zin luidt: \"Ik ben begraven op de dag na vrijdag, drie dagen voor dinsdag.\" Op welke dag werd hij begraven?', 'zaterdag', 'Drie dagen voor dinsdag is zaterdag. De dag na vrijdag is ook zaterdag.', 1),
(4, 'De lamp knippert in een patroon: 2 keer, pauze, 4 keer, pauze, 6 keer, pauze... Hoeveel keer knippert hij daarna?', '8', 'De reeks gaat +2 omhoog: 2, 4, 6, ...?', 1),
(5, 'Op het whiteboard staat een formule: X = 9 × 9 | Y = X − 31 | Z = Y + 6. Wat is Z?', '56', '9×9=81, 81−31=50, 50+6=56.', 2),
(6, 'Er staan 6 injectiespuiten op een rek, genummerd 1 t/m 6. Alleen de spuiten met een priemgetal zijn veilig. Welke nummers zijn dat?', '2, 3, 5', 'Priemgetallen zijn alleen deelbaar door 1 en zichzelf.', 2),
(7, 'Een gecodeerd etiket op de kluis: elk woord is gespiegeld. Het staat er: \"TOOD SI ROOD\". Wat is de boodschap?', 'DOOD IS ROOD', 'Schrijf elk woord achterstevoren.', 2),
(8, 'De nooduitgang vraagt een 4-cijferige code: \"Neem het aantal maanden in een jaar, vermenigvuldig met 5, trek er 10 van af.\"', '50', '12 × 5 = 60, 60 − 10 = 50.', 2),
(9, 'Op een grafsteen staat: \"Ik ben een getal. Tel mij drie keer op bij mezelf en je krijgt 48. Wat ben ik?\"', '12', '3 × x = 48. Deel 48 door 3.', 3),
(10, 'De cijferreeks op het kerkhofhek: 3, 9, 27, 81, ... Wat is het volgende getal?', '243', 'Elk getal wordt vermenigvuldigd met 3.', 3),
(11, 'Er staan 7 zwarte kaarsen in een patroon: aan, uit, aan, aan, uit, aan, aan, uit... Welke staat er op positie 14: aan of uit?', 'aan', 'Het patroon is 3 lang: aan, aan, uit. Positie 14 mod 3 = rest 2 = aan.', 3),
(12, 'Het grafkruis heeft een slot met 3 cijfers. Aanwijzing: \"Eerste cijfer: 7² gedeeld door 7. Tweede: de helft van 16. Derde: het aantal zijden van een ruit.\"', '784', '7²÷7 = 7, 16÷2 = 8, een ruit heeft 4 zijden.', 3);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Gegevens worden geëxporteerd voor tabel `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`) VALUES
(1, 'De Verlaten Kelder', 'Een donkere, vochtige kelder vol geheimen. De muren fluisteren namen van de verdwenen bewoners.'),
(2, 'De Bloedige Operatiekamer', 'Een verlaten ziekenhuis waar de dokter nooit is gestopt. Jij bent zijn volgende patiënt.'),
(3, 'Het Vervloekte Kerkhof', 'De doden rusten hier niet. Los de raadsels op voor middernacht... of sluit je je bij hen aan.');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `team_members`
--

CREATE TABLE `team_members` (
  `id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `member_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `riddles`
--
ALTER TABLE `riddles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_riddles_room` (`roomId`);

--
-- Indexen voor tabel `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);

--
-- Indexen voor tabel `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tm_team` (`team_id`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `riddles`
--
ALTER TABLE `riddles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT voor een tabel `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT voor een tabel `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT voor een tabel `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Beperkingen voor geëxporteerde tabellen
--

--
-- Beperkingen voor tabel `riddles`
--
ALTER TABLE `riddles`
  ADD CONSTRAINT `fk_riddles_room` FOREIGN KEY (`roomId`) REFERENCES `rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Beperkingen voor tabel `team_members`
--
ALTER TABLE `team_members`
  ADD CONSTRAINT `fk_tm_team` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
