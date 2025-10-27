#####  **Online Knjižara**



Online Knjižara je veb aplikacija razvijena u PHP-u, sa ciljem da omogući pregled, pretragu i kupovinu knjiga putem interneta.

Aplikacija je deo master projekta na temu personalizovane prodaje knjiga sa content-based preporukama i tip-tolerantnom pretragom.



---



###### &nbsp;**Tehnologije i alati**

\-  PHP (server-side jezik)

\-  MySQL (baza podataka)

\-  XAMPP (Apache server okruženje)

\-  HTML, CSS, JavaScript (frontend)

\-  PhpStorm / Visual Studio Code (IDE)

\-  Git \& GitHub (kontrola verzija)



---



######  **Instalacija i pokretanje**

1\. Instaliraj \[XAMPP](https://www.apachefriends.org/index.html) i pokreni Apache i MySQL servise.  

2\. U folderu `htdocs` kreiraj podfolder:

&nbsp;  C:\\xampp\\htdocs\\online\_knjizara



3\. Kloniraj repozitorijum:



git clone https://github.com/nikicaninkovic/online\_knjizara.git





U phpMyAdmin importuj bazu podataka:



Otvori http://localhost/phpmyadmin



Kreiraj bazu knjizara



Importuj SQL fajl iz projekta (npr. database/knjizara.sql)



U fajlu config.php postavi parametre baze:



$servername = "localhost";

$username = "root";

$password = "";

$dbname = "online\_knjizara";





Pokreni aplikaciju u browseru:

&nbsp;http://localhost/online\_knjizara



###### &nbsp;**Funkcionalnosti**



* &nbsp;Registracija i prijava korisnika



* &nbsp;Pregled i pretraga knjiga



* &nbsp;Kreiranje i uređivanje korpe



* &nbsp;Content-based preporuke (slične knjige po žanru i autoru)



* &nbsp;Tip-tolerantna pretraga (npr. “Tolstoy” → “Tolstoj”)



* &nbsp;Admin panel za upravljanje knjigama, autorima i zalihama



######  **Struktura projekta**

&nbsp;	

&nbsp;   online\_knjizara

&nbsp;    

&nbsp;    public/              # javno dostupni fajlovi

&nbsp;    includes/            # PHP funkcije i konfiguracija

&nbsp;    templates/           # HTML/CSS delovi stranica

&nbsp;    database/            # SQL fajlovi baze podataka

&nbsp;    assets/              # slike, ikone, CSS, JS

&nbsp;    .gitignore

&nbsp;    README.md





###### &nbsp;**Planirana nadogradnja**



* &nbsp;Integracija sistema preporuka pomoću veštačke inteligencije



* &nbsp;Napredna statistika prodaje i personalizacija korisnika



* &nbsp;Prelazak na Laravel okvir u drugoj fazi razvoja



###### &nbsp; **Autor**



&nbsp;Nikica Ninković

&nbsp;Bijeljina, Bosna i Hercegovina

&nbsp;ninkovic.nikica.24@sinergija.ba



&nbsp;Student master studija na Univerzitetu Sinergija – smer Savremene informacione tehnologije



###### &nbsp; **Licenca**



Ovaj projekat je razvijen u edukativne svrhe i nije komercijalne prirode.

