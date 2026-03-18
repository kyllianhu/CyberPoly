<div class="game-wrapper">

    <!-- Panneau latéral : stats joueur et boutons d'action -->
    <aside class="game-sidebar" aria-label="Interface joueur">

        <div class="sidebar-card player-card">
            <div class="card-header">
                <i class="fas fa-user-shield" aria-hidden="true"></i>
                <h3><?= htmlspecialchars($pseudo) ?></h3>
                <span class="player-badge-status online">En ligne</span>
            </div>
            <div class="player-stats">
                <div class="stat-row">
                    <div class="stat-icon budget-icon"><i class="fas fa-coins"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Budget</span>
                        <span class="stat-value" id="playerBudget">1 500 €</span>
                    </div>
                </div>
                <div class="stat-row">
                    <div class="stat-icon score-icon"><i class="fas fa-shield-halved"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Score Sécurité</span>
                        <span class="stat-value" id="securityScore">0</span>
                    </div>
                </div>
                <div class="stat-row">
                    <div class="stat-icon systems-icon"><i class="fas fa-server"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Systèmes protégés</span>
                        <span class="stat-value" id="securedSystems">0 / 32</span>
                    </div>
                </div>
                <div class="stat-row">
                    <div class="stat-icon position-icon"><i class="fas fa-location-dot"></i></div>
                    <div class="stat-info">
                        <span class="stat-label">Position</span>
                        <span class="stat-value" id="playerPosition">Case 0 — Départ</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="sidebar-card action-card">
            <div class="card-header">
                <i class="fas fa-gamepad" aria-hidden="true"></i>
                <h3>Actions</h3>
            </div>
            <div class="action-buttons">
                <button class="btn btn-primary"   id="buySystemBtn"     disabled><i class="fas fa-cart-plus"></i> Acheter ce système</button>
                <button class="btn btn-secondary" id="upgradeSystemBtn" disabled><i class="fas fa-arrow-trend-up"></i> Améliorer la sécurité</button>
                <button class="btn btn-info"      id="systemInfoBtn"    disabled><i class="fas fa-circle-info"></i> Informations</button>
            </div>
        </div>

    </aside>

    <!-- Plateau de jeu principal -->
    <section class="board-section" aria-label="Plateau de jeu">
        <div class="board" id="gameBoard">

            <!-- Centre du plateau : logo et dés -->
            <div class="board-center">
                <div class="center-logo" aria-hidden="true">
                    <i class="fas fa-shield-halved center-shield"></i>
                    <h2>CyberSafe</h2>
                    <p>Centre de Sécurité</p>
                </div>
                <div class="dice-zone">
                    <div class="dice-pair">
                        <div class="die" id="dice1"><div class="die-face">?</div></div>
                        <div class="die" id="dice2"><div class="die-face">?</div></div>
                    </div>
                    <button class="roll-btn" id="rollDice">
                        <i class="fas fa-dice"></i> Lancer les dés
                    </button>
                    <div class="dice-total" id="diceTotal" style="display:none;">
                        Total : <strong id="diceTotalValue">0</strong>
                    </div>
                </div>
            </div>

            <!-- Coins du plateau positionnés en absolu aux 4 angles -->
            <div class="cell corner cell-start"      data-position="0">
                <i class="fas fa-home"></i>
                <span class="cell-name">Centre de<br>Sécurité</span>
                <span class="cell-sub">+200 €</span>
            </div>

            <div class="cell corner cell-jail"        data-position="10">
                <i class="fas fa-lock"></i>
                <span class="cell-name">Quarantaine</span>
                <span class="cell-sub">Sys. compromis</span>
            </div>

            <div class="cell corner cell-cert"        data-position="20">
                <i class="fas fa-certificate"></i>
                <span class="cell-name">Certification</span>
                <span class="cell-sub">ISO 27001</span>
            </div>

            <div class="cell corner cell-ransomware"  data-position="30">
                <i class="fas fa-bug"></i>
                <span class="cell-name">Ransomware</span>
                <span class="cell-sub">→ Quarantaine</span>
            </div>

            <!-- Côté bas : cases 1 à 9 -->
            <div class="board-row row-bottom">
                <div class="cell property server"   data-position="1"  data-cost="60"  data-name="Serveur Web"             tabindex="0"><div class="property-bar server-bar"></div><span class="cell-name">Serveur Web</span><span class="cell-price">60 €</span></div>
                <div class="cell special incident"  data-position="2"><i class="fas fa-triangle-exclamation"></i><span class="cell-name">Incident</span></div>
                <div class="cell property database" data-position="3"  data-cost="100" data-name="Base de Données Clients" tabindex="0"><div class="property-bar database-bar"></div><span class="cell-name">BDD Clients</span><span class="cell-price">100 €</span></div>
                <div class="cell special tax"       data-position="4"><i class="fas fa-credit-card"></i><span class="cell-name">Audit Sécu.</span><span class="cell-sub">-100 €</span></div>
                <div class="cell property network"  data-position="5"  data-cost="200" data-name="Firewall Principal"       tabindex="0"><div class="property-bar network-bar"></div><span class="cell-name">Firewall</span><span class="cell-price">200 €</span></div>
                <div class="cell property server"   data-position="6"  data-cost="80"  data-name="Serveur Email"            tabindex="0"><div class="property-bar server-bar"></div><span class="cell-name">Srv. Email</span><span class="cell-price">80 €</span></div>
                <div class="cell special practice"  data-position="7"><i class="fas fa-lightbulb"></i><span class="cell-name">Bonne<br>Pratique</span></div>
                <div class="cell property server"   data-position="8"  data-cost="120" data-name="Serveur de Fichiers"      tabindex="0"><div class="property-bar server-bar"></div><span class="cell-name">Srv. Fichiers</span><span class="cell-price">120 €</span></div>
                <div class="cell property server"   data-position="9"  data-cost="140" data-name="Serveur de Sauvegarde"    tabindex="0"><div class="property-bar server-bar"></div><span class="cell-name">Srv. Backup</span><span class="cell-price">140 €</span></div>
            </div>

            <!-- Côté gauche : cases 11 à 19 -->
            <div class="board-col col-left">
                <div class="cell property endpoint" data-position="11" data-cost="160" data-name="Poste PDG"           tabindex="0"><div class="property-bar endpoint-bar"></div><span class="cell-name">Poste PDG</span><span class="cell-price">160 €</span></div>
                <div class="cell property cloud"    data-position="12" data-cost="150" data-name="Service Cloud"       tabindex="0"><div class="property-bar cloud-bar"></div><span class="cell-name">Cloud</span><span class="cell-price">150 €</span></div>
                <div class="cell property endpoint" data-position="13" data-cost="180" data-name="Poste RH"            tabindex="0"><div class="property-bar endpoint-bar"></div><span class="cell-name">Poste RH</span><span class="cell-price">180 €</span></div>
                <div class="cell property network"  data-position="14" data-cost="200" data-name="VPN d'Entreprise"    tabindex="0"><div class="property-bar network-bar"></div><span class="cell-name">VPN</span><span class="cell-price">200 €</span></div>
                <div class="cell property endpoint" data-position="15" data-cost="200" data-name="Poste Comptabilité"  tabindex="0"><div class="property-bar endpoint-bar"></div><span class="cell-name">Poste Compta</span><span class="cell-price">200 €</span></div>
                <div class="cell special incident"  data-position="16"><i class="fas fa-triangle-exclamation"></i><span class="cell-name">Incident</span></div>
                <div class="cell property database" data-position="17" data-cost="220" data-name="Base Financière"     tabindex="0"><div class="property-bar database-bar"></div><span class="cell-name">BDD Financière</span><span class="cell-price">220 €</span></div>
                <div class="cell special practice"  data-position="18"><i class="fas fa-lightbulb"></i><span class="cell-name">Bonne<br>Pratique</span></div>
                <div class="cell property database" data-position="19" data-cost="240" data-name="Data Warehouse"      tabindex="0"><div class="property-bar database-bar"></div><span class="cell-name">Data Warehouse</span><span class="cell-price">240 €</span></div>
            </div>

            <!-- Côté haut : cases 21 à 29 -->
            <div class="board-row row-top">
                <div class="cell property critical"  data-position="21" data-cost="260" data-name="Serveur de Production"    tabindex="0"><div class="property-bar critical-bar"></div><span class="cell-name">Srv. Production</span><span class="cell-price">260 €</span></div>
                <div class="cell special incident"   data-position="22"><i class="fas fa-triangle-exclamation"></i><span class="cell-name">Incident</span></div>
                <div class="cell property critical"  data-position="23" data-cost="280" data-name="Serveur de Paiement"      tabindex="0"><div class="property-bar critical-bar"></div><span class="cell-name">Srv. Paiement</span><span class="cell-price">280 €</span></div>
                <div class="cell property network"   data-position="24" data-cost="200" data-name="Routeur Principal"        tabindex="0"><div class="property-bar network-bar"></div><span class="cell-name">Routeur</span><span class="cell-price">200 €</span></div>
                <div class="cell property critical"  data-position="25" data-cost="300" data-name="Serveur Active Directory" tabindex="0"><div class="property-bar critical-bar"></div><span class="cell-name">Active Directory</span><span class="cell-price">300 €</span></div>
                <div class="cell special practice"   data-position="26"><i class="fas fa-lightbulb"></i><span class="cell-name">Bonne<br>Pratique</span></div>
                <div class="cell property critical"  data-position="27" data-cost="320" data-name="Centre de Données"        tabindex="0"><div class="property-bar critical-bar"></div><span class="cell-name">Datacenter</span><span class="cell-price">320 €</span></div>
                <div class="cell property cloud"     data-position="28" data-cost="150" data-name="Backup Cloud"             tabindex="0"><div class="property-bar cloud-bar"></div><span class="cell-name">Backup Cloud</span><span class="cell-price">150 €</span></div>
                <div class="cell property critical"  data-position="29" data-cost="350" data-name="Serveur Core Banking"     tabindex="0"><div class="property-bar critical-bar"></div><span class="cell-name">Core Banking</span><span class="cell-price">350 €</span></div>
            </div>

            <!-- Côté droit : cases 31 à 39 -->
            <div class="board-col col-right">
                <div class="cell property mobile"  data-position="31" data-cost="150" data-name="App Mobile"       tabindex="0"><div class="property-bar mobile-bar"></div><span class="cell-name">App Mobile</span><span class="cell-price">150 €</span></div>
                <div class="cell property mobile"  data-position="32" data-cost="160" data-name="API Gateway"      tabindex="0"><div class="property-bar mobile-bar"></div><span class="cell-name">API Gateway</span><span class="cell-price">160 €</span></div>
                <div class="cell special practice" data-position="33"><i class="fas fa-lightbulb"></i><span class="cell-name">Bonne<br>Pratique</span></div>
                <div class="cell property network" data-position="34" data-cost="200" data-name="Switch Principal" tabindex="0"><div class="property-bar network-bar"></div><span class="cell-name">Switch</span><span class="cell-price">200 €</span></div>
                <div class="cell property mobile"  data-position="35" data-cost="180" data-name="Serveur API"      tabindex="0"><div class="property-bar mobile-bar"></div><span class="cell-name">Srv. API</span><span class="cell-price">180 €</span></div>
                <div class="cell special incident" data-position="36"><i class="fas fa-triangle-exclamation"></i><span class="cell-name">Incident</span></div>
                <div class="cell property iot"     data-position="37" data-cost="100" data-name="Capteurs IoT"     tabindex="0"><div class="property-bar iot-bar"></div><span class="cell-name">Capteurs IoT</span><span class="cell-price">100 €</span></div>
                <div class="cell special tax"      data-position="38"><i class="fas fa-magnifying-glass"></i><span class="cell-name">Pentest</span><span class="cell-sub">-75 €</span></div>
                <div class="cell property iot"     data-position="39" data-cost="120" data-name="Caméras Sécurité" tabindex="0"><div class="property-bar iot-bar"></div><span class="cell-name">Caméras</span><span class="cell-price">120 €</span></div>
            </div>

            <!-- Pion du joueur déplacé dynamiquement par JS -->
            <div class="player-token" id="playerToken" aria-label="Pion joueur">
                <i class="fas fa-user-shield"></i>
            </div>

        </div>
    </section>

</div>

<!-- Modales : système, incident, bonne pratique, fin de partie -->

<div class="modal" id="systemModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-overlay" data-close="systemModal"></div>
    <div class="modal-box">
        <button class="modal-close" data-close="systemModal" aria-label="Fermer"><i class="fas fa-xmark"></i></button>
        <div class="modal-icon system-icon"><i class="fas fa-server"></i></div>
        <h3 class="modal-title" id="modalTitle">Informations Système</h3>
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-actions">
            <button class="btn btn-primary"   id="modalBuyBtn"><i class="fas fa-cart-plus"></i> Acheter</button>
            <button class="btn btn-secondary" id="modalUpgradeBtn"><i class="fas fa-arrow-trend-up"></i> Améliorer</button>
            <button class="btn btn-outline"   data-close="systemModal"><i class="fas fa-xmark"></i> Fermer</button>
        </div>
    </div>
</div>

<div class="modal" id="incidentModal" role="dialog" aria-modal="true" aria-labelledby="incidentTitle">
    <div class="modal-overlay" data-close="incidentModal"></div>
    <div class="modal-box modal-box--incident">
        <div class="modal-icon incident-icon"><i class="fas fa-triangle-exclamation"></i></div>
        <h3 class="modal-title" id="incidentTitle">⚠ Incident de Sécurité !</h3>
        <div class="modal-body" id="incidentBody"></div>
        <div class="modal-actions">
            <button class="btn btn-danger" id="incidentOkBtn"><i class="fas fa-check"></i> Compris</button>
        </div>
    </div>
</div>

<div class="modal" id="practiceModal" role="dialog" aria-modal="true" aria-labelledby="practiceTitle">
    <div class="modal-overlay" data-close="practiceModal"></div>
    <div class="modal-box modal-box--practice">
        <div class="modal-icon practice-icon"><i class="fas fa-lightbulb"></i></div>
        <h3 class="modal-title" id="practiceTitle">💡 Bonne Pratique</h3>
        <div class="modal-body" id="practiceBody"></div>
        <div class="modal-actions">
            <button class="btn btn-success" id="practiceOkBtn"><i class="fas fa-check"></i> Appliquer</button>
        </div>
    </div>
</div>

<div class="modal" id="gameOverModal" role="dialog" aria-modal="true" aria-labelledby="gameOverTitle">
    <div class="modal-overlay"></div>
    <div class="modal-box modal-box--gameover">
        <div class="modal-icon" id="gameOverIcon"></div>
        <h3 class="modal-title" id="gameOverTitle">Fin de partie</h3>
        <div class="modal-body" id="gameOverBody"></div>
        <div class="modal-actions">
            <button class="btn btn-primary" id="restartBtn"><i class="fas fa-rotate-right"></i> Nouvelle partie</button>
        </div>
    </div>
</div>
