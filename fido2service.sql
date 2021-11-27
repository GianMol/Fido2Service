-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 27, 2019 alle 00:01
-- Versione del server: 10.1.36-MariaDB
-- Versione PHP: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `homework`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `contenuti`
--

CREATE TABLE `contenuti` (
  `Id` varchar(13) NOT NULL,
  `Titolo` text,
  `Sottotitolo` text,
  `Descrizione` text,
  `Url_immagine` text,
  `Autore` text,
  `Casa_editrice` text,
  `Pagine` int(11) DEFAULT NULL,
  `Genere` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `contenuti`
--

INSERT INTO `contenuti` (`Id`, `Titolo`, `Sottotitolo`, `Descrizione`, `Url_immagine`, `Autore`, `Casa_editrice`, `Pagine`, `Genere`) VALUES
('0pxHDwAAQBAJ', 'Eragon (versione italiana)', '', 'UN RAGAZZO. UN DRAGO. UN MONDO DI AVVENTURE. Quando Eragon trova una pietra blu nella foresta, è convinto che gli sia toccata una grande fortuna: potrà venderla e nutrire la sua famiglia per tutto l\'inverno. Ma la pietra è in realtà un uovo che, schiudendosi, rivela un contenuto straordinario: un cucciolo di drago. È così che Eragon scopre di essere destinato a raccogliere un\'eredità antichissima. Forte di una spada magica e dei consigli di un vecchio cantastorie, dovrà cavarsela in un universo magico pieno d\'insidie e dimostrare di essere il degno erede dei Cavalieri dei Draghi...', 'http://books.google.com/books/content?id=0pxHDwAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&imgtk=AFLRE70-FTmjAm24Hn62e0u75KL7dCZb9ppPm9nIEEk8XNyrXgbglBnJ50iFJSlFKg9MOjznH5tVzRMN5vwx9oLNETpZq7CFAhnNOAwOqWq-DrRJpBgx15AyOuVUSNQAeUsPJJEDQni8&source=gbs_api', 'Christopher Paolini', 'Rizzoli', 602, 'Juvenile Fiction / General'),
('8mdZMAEACAAJ', 'Eldest. L\'eredità', '', 'Salvata la città dei ribelli dall\'assalto dello sterminato esercito di Re Galbatorix, Eragon e Saphira si mettono in viaggio per raggiungere Ellesméra, la terra degli elfi. È lì che Eragon proseguirà il suo apprendistato nell\'arte della magia e della spada. Nel frattempo, Carvahall viene attaccata dai Ra\'zac. Roran, il cugino di Eragon, convince gli abitanti del villaggio a fuggire con lui nel Surda per cercare l\'aiuto dei Varden. Dopo un lungo periodo d\'addestramento con Oromis, l\'ultimo Cavaliere, e il suo drago, Glaedr, Eragon ritorna dai Varden per aiutarli a fronteggiare l\'esercito nemico. Non sa che ad attenderlo, oltre alla battaglia, ci sono nuove, stupefacenti rivelazioni...', 'http://books.google.com/books/content?id=8mdZMAEACAAJ&printsec=frontcover&img=1&zoom=1&imgtk=AFLRE73eraeE4Uzv5oWdiYZ0Ux8z9vH3TkidP3k46ZbR9_mrIfdzT7bsm6fHadKSb_DEOkl_fahvLVexhHRoEfo-h6d9cGI42SVep6EpcMqS5Zpv4bkGcMl3ezI3JOvVkPPeeWz45Rbc&source=gbs_api', 'Christopher Paolini', 'Bureau Biblioteca Univ. Rizzoli', 803, 'Fiction / Fantasy / General'),
('LdwKAAAAYAAJ', 'Saggio delle cose ascolane e de\'vescovi di Ascoli nel Piceno dalla fondazione della città sino al corrente secolo decimottavo, e precisamente all\'anno 1766 dell\'era volgare publicato da un abate ascolano', '', '', 'http://books.google.com/books/content?id=LdwKAAAAYAAJ&printsec=frontcover&img=1&zoom=1&edge=curl&imgtk=AFLRE72zRppSuS70f-7oo1FiPCKfCJLafn3Uig9dKyu1jZvRT2seV3hp-LmeooqWzS-Ec3DORvFZcILPpcQVzHxSVN8O0BX9gczy1Vv08bhNtaneUAaP_TaVbNEDS-cP2E0_7JxL3zWP&source=gbs_api', 'Antonio Marcucci (abate.)', 'Consorti, e Felcini', 364, ''),
('N6WZZwEACAAJ', 'Inheritance. L\'eredità', '', 'Sembrano appartenere a un\'altra vita i giorni in cui Eragon era solo un ragazzo nella fattoria dello zio, e Saphira una pietra azzurra in una radura della foresta. Da allora, Cavaliere e dragonessa hanno festeggiato insperate vittorie nel Farthen Dûr, assistito ad antiche cerimonie a Ellesméra, pianto terribili perdite a Feinster. Una sola cosa è rimasta identica: il legame indissolubile che li unisce, e la speranza di deporre Galbatorix. Non sono gli unici a essere cambiati: Roran ha perso il villaggio in cui è cresciuto, ma in battaglia si è guadagnato rispetto e un soprannome, Fortemartello; Nasuada ha assunto il ruolo di un padre morto troppo presto; il destino ha donato a Murtagh un drago, ma gli ha strappato la libertà. E ora, per la prima volta nella storia, umani, elfi, nani e Urgali marciano uniti verso Urû\'baen, la fortezza del traditore Galbatorix. Nell\'ultima, terribile battaglia che li attende rischiano di perdere ciò che hanno di più caro, ma poco importa: in gioco c\'è una nuova Alagaèsia, e l\'occasione di lasciare in eredità al suo popolo un futuro in cui la tirannia del re nero sembrerà soltanto un orribile sogno. Tutto è iniziato con \"Eragon\", tutto finisce con \"Inheritance\".', 'http://books.google.com/books/content?id=N6WZZwEACAAJ&printsec=frontcover&img=1&zoom=1&imgtk=AFLRE70M_7NhdlJzn-h7ZNpiZilxNFRWdCpGvRapnE0VG6cisobkpwTtR4ZmwyVFOZBsA0XcLj573RzO3Ry19IxILDYz9E8RJ-qPQuZ852B8c5VvZVs5V5geEHCTnHasO3GvgV-K9EEJ&source=gbs_api', 'Christopher Paolini', 'Rizzoli', 834, 'Fiction / Fantasy / General'),
('sIxbAYd-88UC', 'Saggio per ben sonare il flauto traverso con alcune notizie... per qualunque strumento...', '', '', 'http://books.google.com/books/content?id=sIxbAYd-88UC&printsec=frontcover&img=1&zoom=1&edge=curl&imgtk=AFLRE70K68LeNpZXkHuIqMqGUUygy0Zj8aBpJ2Y8V7eJGLdzMBU6eCaJcDfS6y8tX6hMHFtSGbvJ31_QxmBnMXZEMvKnqd-GX95SCxuQjfv_qzjZ4xWeJiiFYtQCm16we0bEChvgChhT&source=gbs_api', 'Antonio Lorenzoni', '', 96, ''),
('X3NQDwAAQBAJ', 'L\'eredità', 'Harmony Destiny', 'Mason Harrington ha avuto una vita turbolenta e quando un\'eredità gli regala l\'opportunità di tornare al suo paese d\'origine si appresta a prendersi le proprie rivincite. Prima fra tutte, impossessarsi della rinomata scuderia che appartiene a EvaMarie, la donna che tempo addietro gli ha spezzato il cuore. Una volta raggiunto l\'obiettivo, Mason permette a EvaMarie di continuare a lavorare per lui e ben presto diventa evidente che ciò che Mason cerca, lì, non è soltanto una rivalsa. Più sorprendente per lei è rendersi conto di sentirsi felice all\'idea di assecondarlo... Le incomprensioni che inevitabilmente sorgeranno tra i due finiranno con il rovinare la rinnovata passione, o questa seconda occasione regalata loro dal destino porterà a un lieto fine?', 'http://books.google.com/books/content?id=X3NQDwAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&imgtk=AFLRE72WF_NK3Mf1tWghxSCUsAbyLZA2URuGvsmxxtXrx34m6pkhTsW4hJkXdelqrb2RMU5HjvBCqRg4cMrrg2dgDwulPnNa1dEnHihz0bzoDf55OmU86qesW76VmAB6W6YmHCXvkXPI&source=gbs_api', 'Dani Wade', 'Harper Collins Italia', 160, 'Fiction / Romance / General'),
('yGPtLwEACAAJ', 'Brisingr. L\'eredità', '', 'Molte cose sono cambiate nella vita di Eragon da quando l\'uovo di Saphira è comparso sulla Grande Dorsale: suo zio è stato ucciso, Brom si è sacrificato per proteggerlo dai Ra\'zac, il fratello che non sapeva di avere si è rivelato uno dei suoi peggiori nemici. Molte cose sono cambiate, altre no: Galbatorix opprime ancora Alagaësia e il giovane Cavaliere e la sua dragonessa rimangono l\'ultima speranza di detronizzarlo. Ma Eragon è davvero all\'altezza di questo compito? Murtagh e Castigo si sono dimostrati avversari pericolosi; il sangue di cui si è macchiato tormenta le sue notti insonni; l\'arma che gli era stata donata non è più nelle sue mani. E non c\'è tempo per tornare dagli elfi, non c\'è tempo per riposare, non c\'è tempo per trovare una nuova spada: Katrina è nelle mani di Galbatorix, e per salvarla bisogna entrare nell\'Helgrind, dove ogni giorno si compiono orribili sacrifici umani...', 'http://books.google.com/books/content?id=yGPtLwEACAAJ&printsec=frontcover&img=1&zoom=1&imgtk=AFLRE70UrNcRLEgh5fx6mYE28M3BUeUo_KEfFvxXhoPh8s9HD8COa9CN2Y-Oy2Iwv_ePWMDyr1n2ClHIuayLvlAVOnpkXU0r3frACOyU-aNeO5-Jjj2oAmGoQKwe6mrB1tP7jSy8Zvbt&source=gbs_api', 'Christopher Paolini', 'Bureau Biblioteca Univ. Rizzoli', 838, 'Fiction / Fantasy / General');

-- --------------------------------------------------------

--
-- Struttura della tabella `raccolta_contenuti`
--

CREATE TABLE `raccolta_contenuti` (
  `Contenuto` varchar(13) NOT NULL,
  `Raccolta` varchar(20) NOT NULL,
  `Utente` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `raccolta_contenuti`
--

INSERT INTO `raccolta_contenuti` (`Contenuto`, `Raccolta`, `Utente`) VALUES
('0pxHDwAAQBAJ', 'il ciclo', 'Benfi'),
('8mdZMAEACAAJ', 'il ciclo', 'Benfi'),
('LdwKAAAAYAAJ', 'saggi', 'Benfi'),
('N6WZZwEACAAJ', 'il ciclo', 'Benfi'),
('sIxbAYd-88UC', 'saggi', 'Benfi'),
('yGPtLwEACAAJ', 'il ciclo', 'Benfi');

-- --------------------------------------------------------

--
-- Struttura della tabella `raccolte`
--

CREATE TABLE `raccolte` (
  `Titolo` varchar(20) NOT NULL,
  `Url_immagine` text,
  `Utente` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `raccolte`
--

INSERT INTO `raccolte` (`Titolo`, `Url_immagine`, `Utente`) VALUES
('il ciclo', 'http://books.google.com/books/content?id=0pxHDwAAQBAJ&printsec=frontcover&img=1&zoom=1&edge=curl&imgtk=AFLRE70-FTmjAm24Hn62e0u75KL7dCZb9ppPm9nIEEk8XNyrXgbglBnJ50iFJSlFKg9MOjznH5tVzRMN5vwx9oLNETpZq7CFAhnNOAwOqWq-DrRJpBgx15AyOuVUSNQAeUsPJJEDQni8&source=gbs_api', 'Benfi'),
('saggi', 'http://books.google.com/books/content?id=LdwKAAAAYAAJ&printsec=frontcover&img=1&zoom=1&edge=curl&imgtk=AFLRE72zRppSuS70f-7oo1FiPCKfCJLafn3Uig9dKyu1jZvRT2seV3hp-LmeooqWzS-Ec3DORvFZcILPpcQVzHxSVN8O0BX9gczy1Vv08bhNtaneUAaP_TaVbNEDS-cP2E0_7JxL3zWP&source=gbs_api', 'Benfi');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `Nome_utente` varchar(13) NOT NULL,
  `Nome` varchar(13) DEFAULT NULL,
  `Cognome` varchar(13) DEFAULT NULL,
  `Mail` text,
  `Pass` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`Nome_utente`, `Nome`, `Cognome`, `Mail`, `Pass`) VALUES
('Benfi', 'Gianluca', 'Moliteo', 'genki-97@hotmail.it', 'genki1997'),
('Vanelys', 'Valentina', 'Moliteo', 'valentina@unict.it', '1234');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `contenuti`
--
ALTER TABLE `contenuti`
  ADD PRIMARY KEY (`Id`);

--
-- Indici per le tabelle `raccolta_contenuti`
--
ALTER TABLE `raccolta_contenuti`
  ADD PRIMARY KEY (`Contenuto`,`Raccolta`,`Utente`),
  ADD KEY `co` (`Contenuto`),
  ADD KEY `rac` (`Raccolta`),
  ADD KEY `ut` (`Utente`);

--
-- Indici per le tabelle `raccolte`
--
ALTER TABLE `raccolte`
  ADD PRIMARY KEY (`Titolo`,`Utente`),
  ADD KEY `ut` (`Utente`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`Nome_utente`);

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `raccolta_contenuti`
--
ALTER TABLE `raccolta_contenuti`
  ADD CONSTRAINT `raccolta_contenuti_ibfk_1` FOREIGN KEY (`Contenuto`) REFERENCES `contenuti` (`Id`),
  ADD CONSTRAINT `raccolta_contenuti_ibfk_2` FOREIGN KEY (`Raccolta`) REFERENCES `raccolte` (`Titolo`),
  ADD CONSTRAINT `raccolta_contenuti_ibfk_3` FOREIGN KEY (`Utente`) REFERENCES `utenti` (`Nome_utente`);

--
-- Limiti per la tabella `raccolte`
--
ALTER TABLE `raccolte`
  ADD CONSTRAINT `raccolte_ibfk_1` FOREIGN KEY (`Utente`) REFERENCES `utenti` (`Nome_utente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
