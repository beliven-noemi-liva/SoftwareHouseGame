## Laravel Project Manager - Esercitazione
Questa applicazione è stata sviluppata come esercitazione per la gestione di partite (games) che simulano un gestionale di progetti, sviluppatori (devs) e sales.

## Descrizione del gioco
Scopo del gioco: bilanciare le risorse di una software house per evitare la bancarotta, portando a termine progetti e reclutando personale tecnico e commerciale.

All’inizio di ogni partita vengono generati:
- 1 Developer
- 1 Sales
- Patrimonio iniziale: 10.000 €, dovevano essere 5.000 ma perdevo sempre (ho preferito alzare questo piuttosto che abbassare gli stipendi)
Nel tempo di gioco (ogni 10 secondi = 1 mese) avvengono:
- Generazione di nuovi candidati (Developer & Sales) assumibili (con costo mensile).
- Sales portano nuovi progetti. Ogni Sales può portare un solo progetto per volta, valore e tempistiche proporzionali all’esperienza.
- I Developer lavorano ai progetti assegnati e ne abbassano la complessità in base alla propria seniority.
- Ogni progetto completato genera un guadagno (pari al valore progetto).
- Il patrimonio viene scalato automaticamente di tutti i costi mensili del personale.

La partita termina se il patrimonio scende a zero o meno.

## Funzionalità principali
- Registrazione/Login/Logout: tramite form personalizzato (AuthController).
- Gestione partite:
    - Crea una nuova partita.
    - Elenco delle proprie partite.
    - Pausa e riprendi una partita.
    - Visualizza stato e dettagli.
- Gestione risorse e team: 
    - Pagina candidati condivisa tra partite, assunzione Dev/Sales
    - I candidati assunti (per partita) vengono rimossi dalla lista globale e appaiono nel team aziendale
    - Assunti: disponibili anche se la partita è in pausa, iniziano immediatamente a lavorare/procacciare
- Gestione progetti:
    - Sales attivi portano in automatico nuovi progetti (stato "Ready")
    - Possibilità di assegnare Developer ai progetti, anche con gioco in pausa
    - Visualizzazione, avanzamento, completamento (valore/progress)
    - Projects completati mostrano il prezzo e il Sales che li ha procacciati
    - Elenco progetti per partita, filtrati per stato Ready, In Progress, Done
- Economy 
    - Ogni tick (10s) calcolo costi mensili e aggiorna patrimonio
- Extra
    - Ogni giocatore può vedere e gestire solo le proprie partite e risorse
    - Tutti gli stati (team, progetti, patrimonio) sono persistiti e recuperabili dopo la pausa

## Tecnologie e dipendenze
- Laravel 12
- PHP >= 8.2 (richiesto da Laravel 12)
- Composer
- Database compatibile con Laravel
- Node.js (per asset frontend, solo se vuoi buildare gli asset)

## Installazione
Clona il repository
- git clone https://github.com/beliven-noemi-liva/SoftwareHouseGame.git
- cd SoftwareHouseGame

Installa le dipendenze
- composer install

Copia il file di ambiente
- cp .env.example .env

Configura variabili ENV
- Imposta le variabili nel file .env per il tuo database.

Genera la chiave dell'app
- php artisan key:generate

Migra e popola il database
- php artisan migrate --seed

Avvia il server
- php artisan serve
- L’applicazione sarà accessibile su http://localhost:8000

## Flusso base
- Registrazione/Login
- Crea una nuova partita (/games)
- Visualizza o riprendi le tue partite (in corso o in pausa)
- Gestisci il tuo team:
    - Vai nella pagina candidati, assumi Developer o Sales. Gli assunti non compariranno più tra i candidati, ma solo nel tuo team della partita.
- Assegna i progetti:
    - Ogni Developer può lavorare a un solo progetto per volta.
    - I Sales portano progetti in automatico (1 per volta)
- Monitora i progetti:
    - Avanzamento tramite seniority dev
    - Progetti completati con dettaglio: prezzo finale e nome Sales che li ha portati
- Attenzione al patrimonio!
    - Ogni mese (10 secondi) il patrimonio scende per i costi del personale; completa progetti per guadagnare.
- Manda in pausa e riprendi la partita quando vuoi!
- Partita finita quando i fondi scendono a zero.