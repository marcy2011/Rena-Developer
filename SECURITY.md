# Sicurezza

Non inserire credenziali reali nel repository. Configurare il database tramite
variabili d'ambiente (`DB_HOST`, `DB_USER`, `DB_PASSWORD`, `DB_NAME`) o tramite il
segreto del provider di hosting.

Per segnalare una vulnerabilità, usa **Report a vulnerability** nella scheda Security del repository. Non pubblicare dettagli sfruttabili in una issue pubblica.

Le API PHP qui versionate dipendono da un servizio di autenticazione esterno e dalla configurazione DB dell'hosting: la CI locale esegue lint statico, non certifica autenticazione o comportamento in produzione. Non configurare mai credenziali nei workflow o nel repository.
