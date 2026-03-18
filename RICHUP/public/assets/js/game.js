// Taille du plateau et paramètres de départ
var BOARD_SIZE     = 40;
var START_BUDGET   = 1500;
var PASS_GO_BONUS  = 200;
var QUARANTINE_POS = 10;
var RANSOMWARE_POS = 30;
var CERT_POS       = 20;
var VICTORY_RATIO  = 0.6;

// Liste des incidents de sécurité avec leur coût
var INCIDENTS = [
    { title: 'Phishing Attack',      desc: 'Un employé a cliqué sur un email malveillant. Des identifiants ont été compromis.',                                            cost: 150 },
    { title: 'Ransomware',           desc: 'Vos fichiers sont chiffrés ! Le rançongiciel s\'est propagé via un poste non patché.',                                         cost: 300 },
    { title: 'Attaque DDoS',         desc: 'Vos serveurs sont saturés. Un botnet génère 10 Gbps de trafic illégitime.',                                                    cost: 200 },
    { title: 'Fuite de données',     desc: 'Une base de données mal configurée était accessible publiquement depuis 48 heures.',                                            cost: 250 },
    { title: 'Intrusion réseau',     desc: 'Un attaquant a compromis votre VPN en exploitant une CVE non corrigée.',                                                        cost: 180 },
    { title: 'Credential Stuffing',  desc: '80 000 tentatives de connexion automatisées ont abouti à la compromission de 42 comptes.',                                      cost: 120 },
    { title: 'Insider Threat',       desc: 'Un employé mécontent a exfiltré des données clients avant son départ.',                                                         cost: 220 },
    { title: 'Supply Chain Attack',  desc: 'Un fournisseur logiciel compromis a injecté du code malveillant dans votre pipeline CI/CD.',                                    cost: 280 },
    { title: 'SQL Injection',        desc: 'Un formulaire non protégé a permis l\'exfiltration complète de votre base de données.',                                          cost: 160 },
    { title: 'Zero-Day Exploitation',desc: 'Une vulnérabilité non publiée dans votre pare-feu a été activement exploitée.',                                                 cost: 350 },
];

// Liste des bonnes pratiques avec bonus score et budget
var PRACTICES = [
    { title: 'Formation Anti-Phishing',      desc: 'Toute l\'équipe a terminé une simulation de phishing avec un score de 92 % de réussite.',                              score: 50,  bonus: 100 },
    { title: 'Authentification Multi-Facteurs',desc: 'MFA activé sur tous les comptes. Les attaques par credential stuffing sont neutralisées à 99,9 %.',                   score: 75,  bonus: 0   },
    { title: 'Patch Management',             desc: 'Tous les systèmes sont patchés en moins de 72 h. Aucune CVE critique non corrigée.',                                    score: 60,  bonus: 50  },
    { title: 'Chiffrement de bout en bout',  desc: 'Les données sensibles sont chiffrées au repos (AES-256) et en transit (TLS 1.3).',                                      score: 80,  bonus: 0   },
    { title: 'Stratégie Backup 3-2-1',       desc: '3 copies, 2 supports différents, 1 hors site. Temps de rétablissement : 4 heures.',                                     score: 55,  bonus: 150 },
    { title: 'Pentest réussi',               desc: 'Votre équipe Red Team n\'a trouvé aucune vulnérabilité critique lors de l\'audit.',                                      score: 100, bonus: 200 },
    { title: 'Zero Trust Architecture',      desc: 'Chaque connexion est vérifiée, chaque accès est limité au strict nécessaire.',                                           score: 90,  bonus: 0   },
    { title: 'SIEM opérationnel',            desc: 'Votre SIEM supervise 50 000 événements/seconde et détecte les anomalies en temps réel.',                                 score: 70,  bonus: 0   },
    { title: 'Bug Bounty Program',           desc: '3 vulnérabilités critiques trouvées et corrigées grâce à des chercheurs indépendants.',                                  score: 65,  bonus: 100 },
    { title: 'Politique BYOD & MDM',         desc: 'Tous les appareils mobiles sont gérés via MDM. Données d\'entreprise isolées.',                                          score: 45,  bonus: 0   },
];

// Descriptions et améliorations pour chaque type de propriété
var PROPERTY_INFO = {
    server:   { label: 'Serveur',                desc: 'Serveur exposé sur Internet. Cible privilégiée des attaquants. Nécessite un WAF et un hardening OS.',   upgrade: 'Installer un WAF + Hardening OS'          },
    database: { label: 'Base de données',        desc: 'Stocke des données sensibles. Doit être isolée, chiffrée et soumise à un contrôle d\'accès strict.',    upgrade: 'Chiffrement AES-256 + Audit logs'          },
    network:  { label: 'Infrastructure réseau',  desc: 'Composant réseau critique. Une mauvaise segmentation expose l\'ensemble du SI.',                         upgrade: 'Segmentation VLAN + IPS'                   },
    endpoint: { label: 'Poste de travail',       desc: 'Principal vecteur d\'infection. L\'EDR et la sensibilisation des utilisateurs sont indispensables.',     upgrade: 'EDR + Politique de mots de passe'          },
    cloud:    { label: 'Service Cloud',          desc: 'Service cloud public. Les mauvaises configurations sont la principale cause de fuites de données.',       upgrade: 'CSPM + Cloud Security Posture'             },
    critical: { label: 'Infrastructure critique',desc: 'Incident = arrêt complet des activités. Niveau de protection maximal requis.',                           upgrade: 'HSM + SOC dédié + Red Team'                },
    mobile:   { label: 'Mobile / API',           desc: 'Vecteur d\'attaque via injection, bypass d\'authentification et API exposées non documentées.',          upgrade: 'OWASP Mobile Top 10 + API Gateway'         },
    iot:      { label: 'Objet IoT',              desc: 'Souvent non patchable, firmware obsolète. Entrée courante des attaquants dans le réseau interne.',        upgrade: 'Firmware sécurisé + Réseau isolé'          },
};

// Objet global qui contient toutes les données de la partie en cours
var GameState = {
    position:      0,
    budget:        START_BUDGET,
    securityScore: 0,
    ownedCells:    new Set(),
    securedCells:  new Set(),
    diceRolled:    false,
    inJail:        false,
    jailTurns:     0,
    gameOver:      false,
    startTime:     Date.now(),
};

// Retourne un entier aléatoire entre min et max inclus
function rand(min, max) { return Math.floor(Math.random() * (max - min + 1)) + min; }

// Attend un certain nombre de millisecondes avant de continuer
function sleep(ms) { return new Promise(function(r) { return setTimeout(r, ms); }); }

// Formate un nombre en euros lisible (ex: 1 500 €)
function formatMoney(n) { return n.toLocaleString('fr-FR') + '\u202f€'; }

// Retourne l'heure actuelle au format HH:MM
function timestamp() {
    var t = new Date();
    return String(t.getHours()).padStart(2, '0') + ':' + String(t.getMinutes()).padStart(2, '0');
}

// Retourne la case HTML correspondant à une position du plateau
function getCell(pos) { return document.querySelector('[data-position="' + pos + '"]'); }

// Retourne toutes les cases de type propriété sur le plateau
function getAllProperties() { return document.querySelectorAll('.cell.property'); }

// Retourne le type de propriété (server, cloud, etc.) d'une case
function getPropertyType(cell) {
    var types = ['server','database','network','endpoint','cloud','critical','mobile','iot'];
    var found = types.find(function(t) { return cell.classList.contains(t); });
    return found != null ? found : 'server';
}

// Met à jour les statistiques affichées dans la barre latérale
function updateUI() {
    var totalProps = getAllProperties().length;
    var budgetEl = document.getElementById('playerBudget');
    var scoreEl  = document.getElementById('securityScore');
    var sysEl    = document.getElementById('securedSystems');
    var posEl    = document.getElementById('playerPosition');

    if (budgetEl) {
        budgetEl.textContent = formatMoney(GameState.budget);
        budgetEl.style.color =
            GameState.budget <= 0  ? '#ef4444' :
            GameState.budget < 300 ? '#f59e0b' : '#00d4aa';
    }

    if (scoreEl) scoreEl.textContent = GameState.securityScore.toLocaleString('fr-FR');
    if (sysEl)   sysEl.textContent   = GameState.securedCells.size + ' / ' + totalProps;

    if (posEl) {
        var cell     = getCell(GameState.position);
        var cellName = cell && cell.querySelector('.cell-name') && cell.querySelector('.cell-name').textContent
                        ? cell.querySelector('.cell-name').textContent.replace(/\s+/g, ' ').trim()
                        : 'Case ' + GameState.position;
        posEl.textContent = 'Case\u00a0' + GameState.position + ' \u2014 ' + cellName;
    }

    updateActionButtons();
}

// Active ou désactive les boutons d'action selon la case courante
function updateActionButtons() {
    var pos       = GameState.position;
    var cell      = getCell(pos);
    var isProp    = Boolean(cell && cell.classList.contains('property'));
    var isOwned   = GameState.ownedCells.has(pos);
    var isSecured = GameState.securedCells.has(pos);
    var cost      = parseInt((cell && cell.dataset && cell.dataset.cost) ? cell.dataset.cost : '0', 10);

    var buyBtn     = document.getElementById('buySystemBtn');
    var upgradeBtn = document.getElementById('upgradeSystemBtn');
    var infoBtn    = document.getElementById('systemInfoBtn');

    if (buyBtn)     buyBtn.disabled     = !(isProp && !isOwned && GameState.budget >= cost && !GameState.gameOver);
    if (upgradeBtn) upgradeBtn.disabled = !(isProp && isOwned && !isSecured && !GameState.gameOver);
    if (infoBtn)    infoBtn.disabled    = !isProp;
}

// Déplace visuellement le pion sur la case indiquée
function placeToken(position) {
    var token = document.getElementById('playerToken');
    var cell  = getCell(position);
    var board = document.getElementById('gameBoard');
    if (!token || !cell || !board) return;

    var br = board.getBoundingClientRect();
    var cr = cell.getBoundingClientRect();
    var x  = cr.left - br.left + (cr.width  - 28) / 2;
    var y  = cr.top  - br.top  + (cr.height - 28) / 2;

    token.style.left = x + 'px';
    token.style.top  = y + 'px';

    // Retire la surbrillance des autres cases et l'applique à la case courante
    document.querySelectorAll('.cell.active-cell').forEach(function(el) { el.classList.remove('active-cell'); });
    cell.classList.add('active-cell');
}

// Lance les deux dés avec animation et déclenche le déplacement
async function rollDice() {
    if (GameState.diceRolled || GameState.gameOver) return;

    var rollBtn = document.getElementById('rollDice');
    var d1El    = document.getElementById('dice1') && document.getElementById('dice1').querySelector('.die-face');
    var d2El    = document.getElementById('dice2') && document.getElementById('dice2').querySelector('.die-face');

    rollBtn.disabled     = true;
    GameState.diceRolled = true;

    document.getElementById('dice1') && document.getElementById('dice1').classList.add('rolling');
    document.getElementById('dice2') && document.getElementById('dice2').classList.add('rolling');

    // Anime les faces des dés pendant 650 ms
    var FACES = ['⚀','⚁','⚂','⚃','⚄','⚅'];
    var anim  = setInterval(function() {
        if (d1El) d1El.textContent = FACES[rand(0, 5)];
        if (d2El) d2El.textContent = FACES[rand(0, 5)];
    }, 55);

    await sleep(650);
    clearInterval(anim);

    document.getElementById('dice1') && document.getElementById('dice1').classList.remove('rolling');
    document.getElementById('dice2') && document.getElementById('dice2').classList.remove('rolling');

    // Affiche le résultat final des dés
    var v1 = rand(1, 6);
    var v2 = rand(1, 6);
    if (d1El) d1El.textContent = FACES[v1 - 1];
    if (d2El) d2El.textContent = FACES[v2 - 1];

    var total    = v1 + v2;
    var totalEl  = document.getElementById('diceTotal');
    var totalVal = document.getElementById('diceTotalValue');
    if (totalEl)  totalEl.style.display = 'block';
    if (totalVal) totalVal.textContent  = total;

    await sleep(250);
    await movePlayer(total);
}

// Déplace le joueur case par case en animant le pion
async function movePlayer(steps) {
    var token  = document.getElementById('playerToken');

    for (var i = 1; i <= steps; i++) {
        GameState.position = (GameState.position + 1) % BOARD_SIZE;

        if (token) token.classList.add('moving');
        placeToken(GameState.position);
        await sleep(130);
        if (token) token.classList.remove('moving');

        // Verse le bonus de passage par le départ en cours de déplacement
        if (GameState.position == 0 && i < steps) {
            GameState.budget += PASS_GO_BONUS;
            updateUI();
        }
    }

    await sleep(250);
    await landOnCell(GameState.position);
    updateUI();
}

// Gère l'effet de la case sur laquelle le joueur vient d'atterrir
async function landOnCell(pos) {
    var cell = getCell(pos);

    // Envoie le joueur en quarantaine s'il tombe sur Ransomware
    if (pos == RANSOMWARE_POS) {
        await sleep(500);
        GameState.position  = QUARANTINE_POS;
        GameState.inJail    = true;
        GameState.jailTurns = 3;
        placeToken(QUARANTINE_POS);
        endTurn();
        return;
    }

    // Verse le bonus de retour au départ exact
    if (pos == 0) {
        GameState.budget += PASS_GO_BONUS;
        endTurn();
        return;
    }

    // Simple visite de la quarantaine sans pénalité
    if (pos == QUARANTINE_POS && !GameState.inJail) {
        endTurn();
        return;
    }

    // Ajoute un bonus de budget et de score pour la certification ISO 27001
    if (pos == CERT_POS) {
        GameState.budget        += 200;
        GameState.securityScore += 100;
        endTurn();
        return;
    }

    // Déduit la taxe d'audit ou de pentest du budget
    if (cell && cell.classList.contains('tax')) {
        var amount = pos == 4 ? 100 : 75;
        GameState.budget = Math.max(0, GameState.budget - amount);
        updateUI();
        checkGameOver();
        endTurn();
        return;
    }

    // Tire un incident aléatoire et ouvre la modale correspondante
    if (cell && cell.classList.contains('incident')) {
        var inc = INCIDENTS[rand(0, INCIDENTS.length - 1)];
        GameState.budget = Math.max(0, GameState.budget - inc.cost);
        updateUI();

        var budgetAfter = GameState.budget;
        setModalContent('incidentModal', {
            titleId: 'incidentTitle',
            title:   '\u26a0\u202f' + inc.title,
            bodyId:  'incidentBody',
            body:    '<p>' + inc.desc + '</p>'
                   + '<p style="margin-top:14px;">Co\u00fbt de l\'incident\u202f: <span class="cost">-' + formatMoney(inc.cost) + '</span></p>'
                   + '<p style="margin-top:6px; font-size:.8rem; color:#94a3b8;">Budget restant\u202f: <strong>' + formatMoney(budgetAfter) + '</strong></p>',
        });
        openModal('incidentModal');
        return;
    }

    // Tire une bonne pratique aléatoire et ouvre la modale correspondante
    if (cell && cell.classList.contains('practice')) {
        var pr = PRACTICES[rand(0, PRACTICES.length - 1)];
        GameState.securityScore += pr.score;
        if (pr.bonus > 0) GameState.budget += pr.bonus;
        updateUI();

        setModalContent('practiceModal', {
            titleId: 'practiceTitle',
            title:   '\ud83d\udca1\u202f' + pr.title,
            bodyId:  'practiceBody',
            body:    '<p>' + pr.desc + '</p>'
                   + '<p style="margin-top:14px;">Score S\u00e9curit\u00e9\u202f: <span class="gain">+' + pr.score + '\u202fpts</span>'
                   + (pr.bonus > 0 ? '<br>Bonus budget\u202f: <span class="gain">+' + formatMoney(pr.bonus) + '</span>' : '')
                   + '</p>',
        });
        openModal('practiceModal');
        return;
    }

    endTurn();
}

// Termine le tour et re-active le bouton de lancer
function endTurn() {
    updateUI();
    checkGameOver();
    if (GameState.gameOver) return;

    var rollBtn = document.getElementById('rollDice');

    // Décrémente le compteur de quarantaine et libère le joueur si nécessaire
    if (GameState.inJail) {
        GameState.jailTurns--;
        if (GameState.jailTurns <= 0) {
            GameState.inJail = false;
        }
    }

    GameState.diceRolled = false;
    if (rollBtn) rollBtn.disabled = false;
}

// Achète la propriété sur laquelle se trouve le joueur
function buySystem() {
    var pos  = GameState.position;
    var cell = getCell(pos);
    if (!cell || !cell.classList.contains('property')) return;

    var cost = parseInt((cell.dataset && cell.dataset.cost) ? cell.dataset.cost : '0', 10);
    var name = (cell.dataset && cell.dataset.name) ? cell.dataset.name : 'Système';

    if (GameState.ownedCells.has(pos))  return;
    if (GameState.budget < cost)        return;

    GameState.budget -= cost;
    GameState.ownedCells.add(pos);
    cell.classList.add('owned');

    updateUI();
    checkVictory();
}

// Améliore la sécurité de la propriété courante du joueur
function upgradeSystem() {
    var pos  = GameState.position;
    var cell = getCell(pos);
    if (!cell || !cell.classList.contains('property')) return;

    var name        = (cell.dataset && cell.dataset.name) ? cell.dataset.name : 'Système';
    var upgradeCost = Math.floor(parseInt((cell.dataset && cell.dataset.cost) ? cell.dataset.cost : '0', 10) * 0.5);

    if (!GameState.ownedCells.has(pos))  return;
    if (GameState.securedCells.has(pos)) return;
    if (GameState.budget < upgradeCost)  return;

    GameState.budget -= upgradeCost;
    GameState.securedCells.add(pos);
    GameState.securityScore += 30;
    cell.classList.remove('owned');
    cell.classList.add('secured');

    updateUI();
    checkVictory();
}

// Affiche la modale d'informations d'une propriété (case courante ou forcée)
function showSystemInfo(forcedPos) {
    var pos  = (forcedPos != null) ? forcedPos : GameState.position;
    var cell = getCell(pos);
    if (!cell || !cell.classList.contains('property')) return;

    var name        = (cell.dataset && cell.dataset.name) ? cell.dataset.name : 'Système';
    var cost        = parseInt((cell.dataset && cell.dataset.cost) ? cell.dataset.cost : '0', 10);
    var upgradeCost = Math.floor(cost * 0.5);
    var type        = getPropertyType(cell);
    var info        = PROPERTY_INFO[type] ? PROPERTY_INFO[type] : {};
    var isOwned     = GameState.ownedCells.has(pos);
    var isSecured   = GameState.securedCells.has(pos);
    var isCurrentPos = (pos == GameState.position);

    // Prépare le badge de statut selon l'état de la propriété
    var statusBadge;
    if (isSecured)    statusBadge = '<span style="color:#00d4aa;font-weight:700;">\ud83d\udd12 S\u00e9curis\u00e9</span>';
    else if (isOwned) statusBadge = '<span style="color:#ffd43b;font-weight:700;">\ud83d\udc51 Vous poss\u00e9dez ce syst\u00e8me</span>';
    else              statusBadge = '<span style="color:#94a3b8;">\ud83c\udfea Disponible \u00e0 l\'achat</span>';

    document.getElementById('modalTitle').textContent = name;
    document.getElementById('modalBody').innerHTML =
        '<p style="line-height:1.7;">' + (info.desc ? info.desc : 'Composant informatique de l\'entreprise.') + '</p>'
      + '<div style="margin-top:14px; padding:12px 16px; background:rgba(255,255,255,0.04); border-radius:8px; font-size:.85rem; line-height:1.9;">'
      + '<div>Statut\u202f: ' + statusBadge + '</div>'
      + '<div>Prix d\'achat\u202f: <strong>' + formatMoney(cost) + '</strong></div>'
      + '<div>Co\u00fbt d\'am\u00e9lioration\u202f: <strong>' + formatMoney(upgradeCost) + '</strong></div>'
      + '<div>Am\u00e9lioration recommand\u00e9e\u202f: <em>' + (info.upgrade ? info.upgrade : 'N/A') + '</em></div>'
      + '</div>';

    var buyBtn     = document.getElementById('modalBuyBtn');
    var upgradeBtn = document.getElementById('modalUpgradeBtn');

    // N'affiche les boutons d'achat/amélioration que si c'est la case courante
    buyBtn.style.display     = isCurrentPos ? '' : 'none';
    upgradeBtn.style.display = isCurrentPos ? '' : 'none';

    if (isCurrentPos) {
        buyBtn.disabled     = isOwned || GameState.budget < cost;
        upgradeBtn.disabled = !isOwned || isSecured;
        buyBtn.onclick     = function() { closeModal('systemModal'); buySystem(); };
        upgradeBtn.onclick = function() { closeModal('systemModal'); upgradeSystem(); };
    }

    openModal('systemModal');
}

// Vérifie si le budget est à 0 et déclenche la fin de partie par défaite
function checkGameOver() {
    if (GameState.gameOver) return;
    if (GameState.budget <= 0) {
        GameState.budget   = 0;
        GameState.gameOver = true;
        document.getElementById('rollDice').disabled = true;
        updateUI();
        openGameOverModal(false,
            'Votre budget est épuisé. Vous n\'avez pas pu résister aux cybermenaces.',
            'Score sécurité\u202f: <span class="gain">' + GameState.securityScore + '\u202fpts</span> — Systèmes protégés\u202f: <strong>' + GameState.securedCells.size + '</strong>'
        );
    }
}

// Vérifie si 60% des systèmes sont sécurisés et déclenche la victoire
function checkVictory() {
    if (GameState.gameOver) return;
    var total = getAllProperties().length;
    if (GameState.securedCells.size >= Math.ceil(total * VICTORY_RATIO)) {
        GameState.gameOver = true;
        document.getElementById('rollDice').disabled = true;
        openGameOverModal(true,
            'Félicitations\u202f! Vous avez sécurisé <strong>' + GameState.securedCells.size + '</strong> systèmes sur <strong>' + total + '</strong>.',
            'Score sécurité\u202f: <span class="gain">' + GameState.securityScore + '\u202fpts</span> — Budget restant\u202f: <span class="gain">' + formatMoney(GameState.budget) + '</span>'
        );
    }
}

// Ouvre la modale de fin de partie avec le bon message victoire/défaite
function openGameOverModal(victory, headline, stats) {
    var icon  = document.getElementById('gameOverIcon');
    var title = document.getElementById('gameOverTitle');
    var body  = document.getElementById('gameOverBody');

    if (icon)  icon.innerHTML  = victory
        ? '<i class="fas fa-trophy" style="font-size:2rem;color:#feca57;"></i>'
        : '<i class="fas fa-skull"  style="font-size:2rem;color:#ef4444;"></i>';
    if (title) title.textContent = victory ? '\ud83c\udfc6 Victoire !' : '\ud83d\udcb8 Faillite !';
    if (body)  body.innerHTML   = '<p>' + headline + '</p><p style="margin-top:10px;">' + stats + '</p>';

    openModal('gameOverModal');
}

// Réinitialise complètement la partie et remet le plateau à zéro
function restartGame() {
    Object.assign(GameState, {
        position: 0, budget: START_BUDGET, securityScore: 0,
        ownedCells: new Set(), securedCells: new Set(),
        diceRolled: false, inJail: false, jailTurns: 0, gameOver: false,
    });

    document.querySelectorAll('.cell.owned, .cell.secured, .cell.active-cell, .cell.under-attack')
        .forEach(function(c) { c.classList.remove('owned','secured','active-cell','under-attack'); });

    var d1 = document.getElementById('dice1') && document.getElementById('dice1').querySelector('.die-face');
    var d2 = document.getElementById('dice2') && document.getElementById('dice2').querySelector('.die-face');
    if (d1) d1.textContent = '?';
    if (d2) d2.textContent = '?';

    var totalEl = document.getElementById('diceTotal');
    if (totalEl) totalEl.style.display = 'none';

    closeModal('gameOverModal');
    document.getElementById('rollDice').disabled = false;
    placeToken(0);
    updateUI();
}

// Injecte le titre et le corps dans une modale donnée
function setModalContent(modalId, opts) {
    var t = document.getElementById(opts.titleId);
    var b = document.getElementById(opts.bodyId);
    if (t) t.textContent = opts.title;
    if (b) b.innerHTML   = opts.body;
}

// Rend une modale visible et donne le focus au premier bouton
function openModal(id) {
    var modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.add('active');
    modal.removeAttribute('aria-hidden');
    var firstBtn = modal.querySelector('.btn:not([style*="display: none"])');
    setTimeout(function() { if (firstBtn) firstBtn.focus(); }, 60);
}

// Cache une modale et déclenche endTurn si c'était un incident ou une pratique
function closeModal(id) {
    var modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('active');
    modal.setAttribute('aria-hidden', 'true');

    if (id == 'incidentModal' || id == 'practiceModal') {
        checkGameOver();
        endTurn();
    }

    // Remet les boutons de la modale système à leur état par défaut
    if (id == 'systemModal') {
        var buyBtn     = document.getElementById('modalBuyBtn');
        var upgradeBtn = document.getElementById('modalUpgradeBtn');
        if (buyBtn)     buyBtn.style.display     = '';
        if (upgradeBtn) upgradeBtn.style.display = '';
    }
}

// Initialise le jeu : place le pion, attache tous les écouteurs d'événements
function init() {
    placeToken(0);
    updateUI();

    // Attache les boutons principaux de la sidebar
    document.getElementById('rollDice')        && document.getElementById('rollDice').addEventListener('click', rollDice);
    document.getElementById('buySystemBtn')    && document.getElementById('buySystemBtn').addEventListener('click', buySystem);
    document.getElementById('upgradeSystemBtn')&& document.getElementById('upgradeSystemBtn').addEventListener('click', upgradeSystem);
    document.getElementById('systemInfoBtn')   && document.getElementById('systemInfoBtn').addEventListener('click', function() { showSystemInfo(null); });

    // Attache les boutons de confirmation des modales
    document.getElementById('incidentOkBtn') && document.getElementById('incidentOkBtn').addEventListener('click', function() { closeModal('incidentModal'); });
    document.getElementById('practiceOkBtn') && document.getElementById('practiceOkBtn').addEventListener('click', function() { closeModal('practiceModal'); });
    document.getElementById('restartBtn')    && document.getElementById('restartBtn').addEventListener('click', restartGame);

    // Ferme la modale active quand on clique sur l'overlay ou un bouton data-close
    document.querySelectorAll('[data-close]').forEach(function(el) {
        el.addEventListener('click', function() { closeModal(el.dataset.close); });
    });

    // Ferme la modale active avec la touche Echap
    document.addEventListener('keydown', function(e) {
        if (e.key == 'Escape') {
            document.querySelectorAll('.modal.active').forEach(function(m) { closeModal(m.id); });
        }
    });

    // Ouvre les infos d'une propriété au clic ou à la touche Entrée/Espace
    document.querySelectorAll('.cell.property').forEach(function(cell) {
        cell.addEventListener('click', function() {
            showSystemInfo(parseInt(cell.dataset.position, 10));
        });
        cell.addEventListener('keydown', function(e) {
            if (e.key == 'Enter' || e.key == ' ') {
                e.preventDefault();
                showSystemInfo(parseInt(cell.dataset.position, 10));
            }
        });
    });

    // Replace le pion correctement si la fenêtre est redimensionnée
    var resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() { placeToken(GameState.position); }, 100);
    });
}

// Lance init() dès que le DOM est prêt
if (document.readyState == 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
