// Global variables
let currentPage = 'home';
let isAuthenticated = false;
let machinePrograms = {};
let dryingPrograms = {};

// Timers for each equipment - now supports multiple machines
let timers = {
    machine13: { interval: null, startTime: null, pausedTime: 0, state: 'stopped' },
    machine20: { interval: null, startTime: null, pausedTime: 0, state: 'stopped' },
    machine50: { interval: null, startTime: null, pausedTime: 0, state: 'stopped' },
    machine70: { interval: null, startTime: null, pausedTime: 0, state: 'stopped' },
    sechoir: { interval: null, startTime: null, state: 'stopped' }
};

// Page navigation functions
function showPage(pageId) {
    document.querySelectorAll('.page').forEach(page => {
        page.classList.remove('active');
    });
    
    document.getElementById('trs-auth').style.display = 'none';
    
    if (pageId === 'home') {
        document.getElementById('home').classList.add('active');
    } else {
        document.getElementById(pageId).classList.add('active');
        
        // Initialize page-specific data
        if (pageId === 'machines') {
            loadMachinePrograms();
        } else if (pageId === 'sechoirs') {
            loadDryingPrograms();
        } else if (pageId === 'reglages') {
            loadMachinePrograms();
            loadDryingPrograms();
            loadMachineProgramsTable();
            loadDryingProgramsTable();
        }
    }
    
    currentPage = pageId;
}

function showTRSPage() {
    if (!isAuthenticated) {
        document.querySelectorAll('.page').forEach(page => {
            page.classList.remove('active');
        });
        document.getElementById('trs-auth').style.display = 'block';
    } else {
        showPage('trs');
        loadTRSData();
    }
}

// Authentication functions
async function authenticateTRS(event) {
    event.preventDefault();
    
    const username = document.getElementById('trs-username').value;
    const password = document.getElementById('trs-password').value;
    
    try {
        const response = await fetch('ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=login&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            isAuthenticated = true;
            document.getElementById('trs-username').value = '';
            document.getElementById('trs-password').value = '';
            showPage('trs');
            loadTRSData();
        } else {
            alert('Identifiants incorrects');
        }
    } catch (error) {
        console.error('Erreur de connexion:', error);
        alert('Erreur de connexion');
    }
}

async function logout() {
    try {
        await fetch('ajax.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=logout'
        });
    } catch (error) {
        console.error('Erreur de déconnexion:', error);
    }
    
    isAuthenticated = false;
    showPage('home');
}

// Machine functions
async function loadMachinePrograms() {
    try {
        const response = await fetch('ajax.php?action=get_machine_programs');
        const programs = await response.json();
        
        machinePrograms = programs;
        populateProgramSelect();
    } catch (error) {
        console.error('Erreur de chargement des programmes:', error);
    }
}

function populateProgramSelect() {
    // Populate all machine program selects
    const machines = [13, 20, 50, 70];
    
    machines.forEach(machineNumber => {
        const programSelect = document.getElementById(`program-select-${machineNumber}`);
        if (!programSelect) return;
        
        programSelect.innerHTML = '<option value="">Sélectionner un programme</option>';
        
        Object.entries(machinePrograms).forEach(([id, program]) => {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = `${program.program_number} - ${program.name}`;
            programSelect.appendChild(option);
        });
    });
}

function updateProgramInfo(machineNumber) {
    const programSelect = document.getElementById(`program-select-${machineNumber}`);
    const programInfo = document.getElementById(`program-info-${machineNumber}`);
    const programName = document.getElementById(`program-name-${machineNumber}`);
    const programDuration = document.getElementById(`program-duration-${machineNumber}`);
    
    const programId = programSelect.value;
    
    if (programId && machinePrograms[programId]) {
        const program = machinePrograms[programId];
        programName.textContent = program.name;
        
        const hours = Math.floor(program.duration_minutes / 60);
        const minutes = program.duration_minutes % 60;
        programDuration.textContent = `${hours}:${minutes.toString().padStart(2, '0')}:00`;
        
        programInfo.classList.remove('hidden');
    } else {
        programInfo.classList.add('hidden');
    }
}

// Timer functions for machines - now supports multiple machines
function startMachine(machineNumber) {
    const program = document.getElementById(`program-select-${machineNumber}`).value;
    const weight = document.getElementById(`machine-weight-${machineNumber}`).value;
    const operator = document.getElementById(`machine-operator-${machineNumber}`).value;

    if (!program || !weight || !operator) {
        alert('Veuillez remplir tous les champs obligatoires');
        return;
    }

    const timerKey = `machine${machineNumber}`;
    timers[timerKey].startTime = new Date();
    timers[timerKey].state = 'running';
    
    document.getElementById(`machine-status-${machineNumber}`).className = 'status-display running';
    document.getElementById(`machine-state-${machineNumber}`).textContent = 'En cours';
    document.getElementById(`status-indicator-${machineNumber}`).style.color = '#28a745';
    
    document.getElementById(`machine-start-${machineNumber}`).disabled = true;
    document.getElementById(`machine-pause-${machineNumber}`).disabled = false;
    document.getElementById(`machine-stop-${machineNumber}`).disabled = false;
    
    timers[timerKey].interval = setInterval(() => updateMachineTimer(machineNumber), 1000);
}

function pauseMachine(machineNumber) {
    const timerKey = `machine${machineNumber}`;
    
    if (timers[timerKey].state === 'running') {
        timers[timerKey].pausedTime += Date.now() - timers[timerKey].startTime.getTime();
        timers[timerKey].state = 'paused';
        clearInterval(timers[timerKey].interval);
        
        document.getElementById(`machine-status-${machineNumber}`).className = 'status-display paused';
        document.getElementById(`machine-state-${machineNumber}`).textContent = 'En pause';
        document.getElementById(`status-indicator-${machineNumber}`).style.color = '#ffc107';
        document.getElementById(`machine-pause-${machineNumber}`).textContent = '▶️ Reprendre';
    } else if (timers[timerKey].state === 'paused') {
        timers[timerKey].startTime = new Date();
        timers[timerKey].state = 'running';
        
        document.getElementById(`machine-status-${machineNumber}`).className = 'status-display running';
        document.getElementById(`machine-state-${machineNumber}`).textContent = 'En cours';
        document.getElementById(`status-indicator-${machineNumber}`).style.color = '#28a745';
        document.getElementById(`machine-pause-${machineNumber}`).textContent = '⏸️ Pause';
        
        timers[timerKey].interval = setInterval(() => updateMachineTimer(machineNumber), 1000);
    }
}

function stopMachine(machineNumber) {
    const timerKey = `machine${machineNumber}`;
    
    if (timers[timerKey].interval) {
        clearInterval(timers[timerKey].interval);
    }
    
    timers[timerKey].state = 'stopped';
    document.getElementById(`machine-status-${machineNumber}`).className = 'status-display stopped';
    document.getElementById(`machine-state-${machineNumber}`).textContent = 'Arrêtée';
    document.getElementById(`status-indicator-${machineNumber}`).style.color = '#6c757d';
    
    document.getElementById(`machine-start-${machineNumber}`).disabled = false;
    document.getElementById(`machine-pause-${machineNumber}`).disabled = true;
    document.getElementById(`machine-stop-${machineNumber}`).disabled = true;
    document.getElementById(`machine-pause-${machineNumber}`).textContent = '⏸️ Pause';
}

function updateMachineTimer(machineNumber) {
    const timerKey = `machine${machineNumber}`;
    const now = new Date();
    const elapsed = (now.getTime() - timers[timerKey].startTime.getTime() + timers[timerKey].pausedTime) / 1000;
    const hours = Math.floor(elapsed / 3600);
    const minutes = Math.floor((elapsed % 3600) / 60);
    const seconds = Math.floor(elapsed % 60);
    
    document.getElementById(`machine-timer-${machineNumber}`).textContent = 
        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

// Save machine production - updated for multi-machine
async function saveMachineProduction(event, machineNumber) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'save_machine_production');
    
    // Add timer data
    const timerKey = `machine${machineNumber}`;
    const realDuration = timers[timerKey].pausedTime / 60000 + 
        (timers[timerKey].startTime ? (Date.now() - timers[timerKey].startTime.getTime()) / 60000 : 0);
    formData.append('real_duration', realDuration);
    
    try {
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(`Production Machine ${machineNumber} enregistrée avec succès!`);
            
            // Reset form and timer
            event.target.reset();
            document.getElementById(`program-info-${machineNumber}`).classList.add('hidden');
            resetMachineTimer(machineNumber);
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de sauvegarde:', error);
        alert('Erreur de sauvegarde');
    }
}

function resetMachineTimer(machineNumber) {
    const timerKey = `machine${machineNumber}`;
    timers[timerKey].pausedTime = 0;
    timers[timerKey].startTime = null;
    document.getElementById(`machine-timer-${machineNumber}`).textContent = '00:00:00';
    stopMachine(machineNumber);
}

// Dryer functions
async function loadDryingPrograms() {
    try {
        const response = await fetch('ajax.php?action=get_drying_programs');
        const programs = await response.json();
        
        dryingPrograms = programs;
        populateArticleTypeSelect();
    } catch (error) {
        console.error('Erreur de chargement des programmes de séchage:', error);
    }
}

function populateArticleTypeSelect() {
    const articleSelect = document.getElementById('article-type');
    if (!articleSelect) return;
    
    articleSelect.innerHTML = '<option value="">Sélectionner un article</option>';
    
    Object.entries(dryingPrograms).forEach(([id, program]) => {
        const option = document.createElement('option');
        option.value = id;
        option.textContent = program.name;
        articleSelect.appendChild(option);
    });
    
    const otherOption = document.createElement('option');
    otherOption.value = 'autre';
    otherOption.textContent = 'Autre (manuel)';
    articleSelect.appendChild(otherOption);
}

function updateDryingRules() {
    const articleType = document.getElementById('article-type').value;
    const durationField = document.getElementById('sechoir-duration');
    const temperatureField = document.getElementById('sechoir-temperature');

    if (articleType && articleType !== 'autre' && dryingPrograms[articleType]) {
        durationField.value = dryingPrograms[articleType].duration_minutes;
        temperatureField.value = dryingPrograms[articleType].temperature;
        durationField.readOnly = true;
        temperatureField.readOnly = true;
    } else {
        durationField.readOnly = false;
        temperatureField.readOnly = false;
    }
}

// Timer functions for dryers
function startSechoir() {
    const sechoir = document.getElementById('sechoir-select').value;
    const articleType = document.getElementById('article-type').value;
    const duration = document.getElementById('sechoir-duration').value;
    const temperature = document.getElementById('sechoir-temperature').value;
    const weight = document.getElementById('sechoir-weight').value;
    const operator = document.getElementById('sechoir-operator').value;

    if (!sechoir || !articleType || !duration || !temperature || !weight || !operator) {
        alert('Veuillez remplir tous les champs obligatoires');
        return;
    }

    timers.sechoir.startTime = new Date();
    timers.sechoir.state = 'running';
    
    document.getElementById('sechoir-status').className = 'status-display running';
    document.getElementById('sechoir-state').textContent = 'En cours';
    
    document.getElementById('sechoir-start').disabled = true;
    document.getElementById('sechoir-stop').disabled = false;
    
    timers.sechoir.interval = setInterval(updateSechoirTimer, 1000);
}

function stopSechoir() {
    if (timers.sechoir.interval) {
        clearInterval(timers.sechoir.interval);
    }
    
    timers.sechoir.state = 'stopped';
    document.getElementById('sechoir-status').className = 'status-display stopped';
    document.getElementById('sechoir-state').textContent = 'Arrêtée';
    
    document.getElementById('sechoir-start').disabled = false;
    document.getElementById('sechoir-stop').disabled = true;
}

function updateSechoirTimer() {
    const now = new Date();
    const elapsed = (now.getTime() - timers.sechoir.startTime.getTime()) / 1000;
    const hours = Math.floor(elapsed / 3600);
    const minutes = Math.floor((elapsed % 3600) / 60);
    const seconds = Math.floor(elapsed % 60);
    
    document.getElementById('sechoir-timer').textContent = 
        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

// Save dryer production
async function saveSechoirProduction(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'save_sechoir_production');
    
    // Add timer data
    const realDuration = timers.sechoir.startTime ? 
        (Date.now() - timers.sechoir.startTime.getTime()) / 60000 : 0;
    formData.append('real_duration', realDuration);
    
    try {
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Production enregistrée avec succès!');
            
            // Reset form and timer
            event.target.reset();
            resetSechoirTimer();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de sauvegarde:', error);
        alert('Erreur de sauvegarde');
    }
}

function resetSechoirTimer() {
    timers.sechoir.startTime = null;
    document.getElementById('sechoir-timer').textContent = '00:00:00';
    stopSechoir();
}

// TRS data loading
async function loadTRSData() {
    try {
        const response = await fetch('ajax.php?action=get_trs_data');
        const data = await response.json();
        
        if (data.success) {
            updateTRSKPIs(data.kpis);
            updateProductionsTable(data.productions);
        }
    } catch (error) {
        console.error('Erreur de chargement des données TRS:', error);
    }
}

function updateTRSKPIs(kpis) {
    document.getElementById('trs-disponibilite').textContent = kpis.availability + '%';
    document.getElementById('trs-performance').textContent = kpis.performance + '%';
    document.getElementById('trs-qualite').textContent = kpis.quality + '%';
    document.getElementById('trs-global').textContent = kpis.trs + '%';
}

function updateProductionsTable(productions) {
    const container = document.getElementById('productions-table-container');
    
    if (productions.length === 0) {
        container.innerHTML = '<p>Aucune production enregistrée</p>';
        return;
    }
    
    let tableHTML = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date/Heure</th>
                    <th>Équipement</th>
                    <th>Opérateur</th>
                    <th>Type</th>
                    <th>Poids (kg)</th>
                    <th>Durée Réelle</th>
                    <th>Performance</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    productions.forEach(prod => {
        const date = new Date(prod.timestamp).toLocaleString('fr-FR');
        const weight = prod.weight || '-';
        const duration = Math.round(prod.real_duration || 0);
        const performance = prod.theoretical_duration && prod.real_duration ? 
            Math.round((prod.theoretical_duration / prod.real_duration) * 100) : 100;
        
        tableHTML += `
            <tr>
                <td>${date}</td>
                <td>${prod.equipment}</td>
                <td>${prod.operator}</td>
                <td>${prod.type}</td>
                <td>${weight}</td>
                <td>${duration} min</td>
                <td><span style="color: ${performance > 95 ? 'green' : performance > 80 ? 'orange' : 'red'}; font-weight: bold;">${performance}%</span></td>
            </tr>
        `;
    });
    
    tableHTML += '</tbody></table>';
    container.innerHTML = tableHTML;
}

// Add machine program function
async function addMachineProgram(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'add_machine_program');
    
    // Check if we're editing
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const editingId = submitBtn.getAttribute('data-editing-id');
    if (editingId) {
        formData.append('id', editingId);
    }
    
    try {
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(editingId ? 'Programme machine modifié avec succès!' : 'Programme machine ajouté avec succès!');
            event.target.reset();
            
            // Reset button text and remove editing attribute
            submitBtn.textContent = '➕ Ajouter Programme Machine';
            submitBtn.removeAttribute('data-editing-id');
            
            // Reset the form to normal state
            resetMachineProgramForm();
            
            loadMachinePrograms();
            loadMachineProgramsTable();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur d\'ajout du programme:', error);
        alert('Erreur d\'ajout du programme');
    }
}

// Add drying program function
async function addDryingProgram(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'add_drying_program');
    
    // Check if we're editing
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const editingId = submitBtn.getAttribute('data-editing-id');
    if (editingId) {
        formData.append('id', editingId);
    }
    
    try {
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(editingId ? 'Programme séchoir modifié avec succès!' : 'Programme séchoir ajouté avec succès!');
            event.target.reset();
            
            // Reset button text and remove editing attribute
            submitBtn.textContent = '➕ Ajouter Programme Séchoir';
            submitBtn.removeAttribute('data-editing-id');
            
            loadDryingPrograms();
            loadDryingProgramsTable();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur d\'ajout du programme:', error);
        alert('Erreur d\'ajout du programme');
    }
}

// Save functions for other forms
async function saveArret(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'save_arret');
    
    try {
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Arrêt enregistré avec succès!');
            event.target.reset();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de sauvegarde:', error);
        alert('Erreur de sauvegarde');
    }
}

async function saveNonConformite(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'save_nonconformite');
    
    try {
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Non-conformité enregistrée avec succès!');
            event.target.reset();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de sauvegarde:', error);
        alert('Erreur de sauvegarde');
    }
}

// Functions to load and display existing programs tables
async function loadMachineProgramsTable() {
    try {
        const response = await fetch('ajax.php?action=get_machine_programs');
        const programs = await response.json();
        
        const container = document.getElementById('machine-programs-table-container');
        if (!container) return;
        
        if (Object.keys(programs).length === 0) {
            container.innerHTML = '<p>Aucun programme machine configuré</p>';
            return;
        }
        
        let tableHTML = `
            <table class="data-table">
                <thead>
                    <tr>
                        <th>N° Programme</th>
                        <th>Nom</th>
                        <th>Durée (min)</th>
                        <th>Capacité (kg)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        Object.entries(programs).forEach(([id, program]) => {
            tableHTML += `
                <tr>
                    <td>${program.program_number}</td>
                    <td>${program.name}</td>
                    <td>${program.duration_minutes}</td>
                    <td>${program.capacity_kg || '-'}</td>
                    <td style="display:flex; gap:5px;">
                        <button class="btn btn-sm btn-warning" onclick="editMachineProgram(${id})">✏️ Modifier</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteMachineProgram(${id})">🗑️ Supprimer</button>
                    </td>
                </tr>
            `;
        });
        
        tableHTML += '</tbody></table>';
        container.innerHTML = tableHTML;
        
    } catch (error) {
        console.error('Erreur de chargement des programmes machines:', error);
    }
}

async function loadDryingProgramsTable() {
    try {
        const response = await fetch('ajax.php?action=get_drying_programs');
        const programs = await response.json();
        
        const container = document.getElementById('drying-programs-table-container');
        if (!container) return;
        
        if (Object.keys(programs).length === 0) {
            container.innerHTML = '<p>Aucun programme séchoir configuré</p>';
            return;
        }
        
        let tableHTML = `
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Article Concerné</th>
                        <th>Durée (min)</th>
                        <th>Température (°C)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        Object.entries(programs).forEach(([id, program]) => {
            tableHTML += `
                <tr>
                    <td>${program.name}</td>
                    <td>${program.article_type}</td>
                    <td>${program.duration_minutes}</td>
                    <td>${program.temperature || '-'}</td>
                    <td style="display:flex; gap:5px;">
                        <button class="btn btn-sm btn-warning" onclick="editDryingProgram(${id})">✏️ Modifier</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteDryingProgram(${id})">🗑️ Supprimer</button>
                    </td>
                </tr>
            `;
        });
        
        tableHTML += '</tbody></table>';
        container.innerHTML = tableHTML;
        
    } catch (error) {
        console.error('Erreur de chargement des programmes séchoirs:', error);
    }
}

// Edit machine program function
function editMachineProgram(id) {
    const program = machinePrograms[id];
    if (!program) return;
    
    // Fill the form with existing data
    document.getElementById('new-program-number').value = program.program_number;
    document.getElementById('new-program-name').value = program.name;
    document.getElementById('new-program-duration').value = program.duration_minutes;
    
    // Show machine type selector and make capacity read-only
    showMachineTypeSelector(id);
    
    // Change button text to indicate editing
    const submitBtn = document.querySelector('#machineProgram button[type="submit"]');
    submitBtn.textContent = '✏️ Modifier Programme Machine';
    submitBtn.setAttribute('data-editing-id', id);
}

// Edit drying program function
function editDryingProgram(id) {
    const program = dryingPrograms[id];
    if (!program) return;
    
    // Fill the form with existing data
    document.getElementById('new-drying-name').value = program.name;
    document.getElementById('new-drying-article').value = program.article_type;
    document.getElementById('new-drying-duration').value = program.duration_minutes;
    
    // Show séchoir type selector and make temperature read-only
    showSechoirTypeSelector(id);
    
    // Change button text to indicate editing
    const submitBtn = document.querySelector('#dryingProgram button[type="submit"]');
    submitBtn.textContent = '✏️ Modifier Programme Séchoir';
    submitBtn.setAttribute('data-editing-id', id);
}

// Delete machine program function
async function deleteMachineProgram(id) {
    const program = machinePrograms[id];
    if (!program) return;
    
    if (!confirm(`Êtes-vous sûr de vouloir supprimer le programme "${program.name}" ?`)) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete_machine_program');
        formData.append('id', id);
        
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Programme machine supprimé avec succès!');
            loadMachinePrograms();
            loadMachineProgramsTable();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de suppression:', error);
        alert('Erreur de suppression');
    }
}

// Delete drying program function
async function deleteDryingProgram(id) {
    const program = dryingPrograms[id];
    if (!program) return;
    
    if (!confirm(`Êtes-vous sûr de vouloir supprimer le programme "${program.name}" ?`)) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'delete_drying_program');
        formData.append('id', id);
        
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Programme séchoir supprimé avec succès!');
            loadDryingPrograms();
            loadDryingProgramsTable();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de suppression:', error);
        alert('Erreur de suppression');
    }
}

// New functions for TRS filters and export
async function applyFilters() {
    const dateStart = document.getElementById('filter-date-start').value;
    const dateEnd = document.getElementById('filter-date-end').value;
    const equipment = document.getElementById('filter-equipment').value;
    const operator = document.getElementById('filter-operator').value;
    
    try {
        const formData = new FormData();
        formData.append('action', 'apply_filters');
        formData.append('date_start', dateStart);
        formData.append('date_end', dateEnd);
        formData.append('equipment', equipment);
        formData.append('operator', operator);
        
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            updateProductionsTable(result.productions);
            
            // Also update KPIs based on filtered data if available
            if (result.kpis) {
                updateTRSKPIs(result.kpis);
            }
            
            console.log('Filtres appliqués avec succès:', result.productions.length, 'productions trouvées');
        } else {
            alert('Erreur lors de l\'application des filtres: ' + (result.message || 'Erreur inconnue'));
        }
    } catch (error) {
        console.error('Erreur de filtrage:', error);
        alert('Erreur de filtrage: ' + error.message);
    }
}

function resetFilters() {
    document.getElementById('filter-date-start').value = '';
    document.getElementById('filter-date-end').value = '';
    document.getElementById('filter-equipment').value = '';
    document.getElementById('filter-operator').value = '';
    
    // Reload all data
    loadTRSData();
}

async function exportToExcel() {
    const dateStart = document.getElementById('filter-date-start').value;
    const dateEnd = document.getElementById('filter-date-end').value;
    const equipment = document.getElementById('filter-equipment').value;
    const operator = document.getElementById('filter-operator').value;
    
    try {
        const formData = new FormData();
        formData.append('action', 'export_excel');
        formData.append('date_start', dateStart);
        formData.append('date_end', dateEnd);
        formData.append('equipment', equipment);
        formData.append('operator', operator);
        
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Create and download CSV file
            const blob = new Blob([result.csv_content], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', result.filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } else {
            alert('Erreur lors de l\'export');
        }
    } catch (error) {
        console.error('Erreur d\'export:', error);
        alert('Erreur d\'export');
    }
}

// Recalculate sechoir settings function
async function recalculateSechoirSettings() {
    const articleType = document.getElementById('article-type').value;
    const weight = document.getElementById('sechoir-weight').value;
    
    if (!articleType || !weight) {
        alert('Veuillez sélectionner un type d\'article et saisir le poids');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'recalculate_sechoir');
        formData.append('article_type', articleType);
        formData.append('weight', weight);
        
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('sechoir-duration').value = result.duration;
            document.getElementById('sechoir-temperature').value = result.temperature;
            
            // Update display
            document.getElementById('sechoir-duration-display').textContent = result.duration + ' min';
            document.getElementById('sechoir-temp-display').textContent = result.temperature + ' °C';
            
            alert(`Paramètres recalculés:\nDurée: ${result.duration} min\nTempérature: ${result.temperature}°C\nBasé sur: ${result.program_name}`);
        } else {
            alert('Erreur lors du recalcul');
        }
    } catch (error) {
        console.error('Erreur de recalcul:', error);
        alert('Erreur de recalcul');
    }
}

// Calandre timer functions
let calandreTimer = {
    interval: null,
    startTime: null,
    pausedTime: 0,
    state: 'stopped'
};

function startCalandre() {
    const weight = document.getElementById('calandre-weight').value;
    const pieces = document.getElementById('calandre-pieces').value;
    const operator = document.getElementById('calandre-operator').value;

    if (!weight || !pieces || !operator) {
        alert('Veuillez remplir tous les champs obligatoires');
        return;
    }

    calandreTimer.startTime = new Date();
    calandreTimer.state = 'running';
    
    document.getElementById('calandre-status').className = 'status-display running';
    document.getElementById('calandre-state').textContent = 'En cours';
    
    document.getElementById('calandre-start').disabled = true;
    document.getElementById('calandre-pause').disabled = false;
    document.getElementById('calandre-resume').disabled = true;
    document.getElementById('calandre-stop').disabled = false;
    
    calandreTimer.interval = setInterval(updateCalandreTimer, 1000);
}

function pauseCalandre() {
    if (calandreTimer.state === 'running') {
        calandreTimer.pausedTime += Date.now() - calandreTimer.startTime.getTime();
        calandreTimer.state = 'paused';
        clearInterval(calandreTimer.interval);
        
        document.getElementById('calandre-status').className = 'status-display paused';
        document.getElementById('calandre-state').textContent = 'En pause';
        
        document.getElementById('calandre-pause').disabled = true;
        document.getElementById('calandre-resume').disabled = false;
    }
}

function resumeCalandre() {
    if (calandreTimer.state === 'paused') {
        calandreTimer.startTime = new Date();
        calandreTimer.state = 'running';
        
        document.getElementById('calandre-status').className = 'status-display running';
        document.getElementById('calandre-state').textContent = 'En cours';
        
        document.getElementById('calandre-pause').disabled = false;
        document.getElementById('calandre-resume').disabled = true;
        
        calandreTimer.interval = setInterval(updateCalandreTimer, 1000);
    }
}

function stopCalandre() {
    if (calandreTimer.interval) {
        clearInterval(calandreTimer.interval);
    }
    
    calandreTimer.state = 'stopped';
    document.getElementById('calandre-status').className = 'status-display stopped';
    document.getElementById('calandre-state').textContent = 'Arrêtée';
    
    document.getElementById('calandre-start').disabled = false;
    document.getElementById('calandre-pause').disabled = true;
    document.getElementById('calandre-resume').disabled = true;
    document.getElementById('calandre-stop').disabled = true;
}

function updateCalandreTimer() {
    const now = new Date();
    const elapsed = (now.getTime() - calandreTimer.startTime.getTime() + calandreTimer.pausedTime) / 1000;
    const hours = Math.floor(elapsed / 3600);
    const minutes = Math.floor((elapsed % 3600) / 60);
    const seconds = Math.floor(elapsed % 60);
    
    const timeStr = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    document.getElementById('calandre-timer').textContent = timeStr;
    document.getElementById('calandre-net-time').textContent = timeStr;
}

// Repassage timer functions
let repassageTimer = {
    interval: null,
    startTime: null,
    pausedTime: 0,
    state: 'stopped'
};

function startRepassage() {
    const pieces = document.getElementById('repassage-pieces').value;
    const type = document.getElementById('repassage-type').value;
    const operator = document.getElementById('repassage-operator').value;

    if (!pieces || !type || !operator) {
        alert('Veuillez remplir tous les champs obligatoires');
        return;
    }

    repassageTimer.startTime = new Date();
    repassageTimer.state = 'running';
    
    document.getElementById('repassage-status').className = 'status-display running';
    document.getElementById('repassage-state').textContent = 'En cours';
    
    document.getElementById('repassage-start').disabled = true;
    document.getElementById('repassage-pause').disabled = false;
    document.getElementById('repassage-resume').disabled = true;
    document.getElementById('repassage-stop').disabled = false;
    
    repassageTimer.interval = setInterval(updateRepassageTimer, 1000);
}

function pauseRepassage() {
    if (repassageTimer.state === 'running') {
        repassageTimer.pausedTime += Date.now() - repassageTimer.startTime.getTime();
        repassageTimer.state = 'paused';
        clearInterval(repassageTimer.interval);
        
        document.getElementById('repassage-status').className = 'status-display paused';
        document.getElementById('repassage-state').textContent = 'En pause';
        
        document.getElementById('repassage-pause').disabled = true;
        document.getElementById('repassage-resume').disabled = false;
    }
}

function resumeRepassage() {
    if (repassageTimer.state === 'paused') {
        repassageTimer.startTime = new Date();
        repassageTimer.state = 'running';
        
        document.getElementById('repassage-status').className = 'status-display running';
        document.getElementById('repassage-state').textContent = 'En cours';
        
        document.getElementById('repassage-pause').disabled = false;
        document.getElementById('repassage-resume').disabled = true;
        
        repassageTimer.interval = setInterval(updateRepassageTimer, 1000);
    }
}

function stopRepassage() {
    if (repassageTimer.interval) {
        clearInterval(repassageTimer.interval);
    }
    
    repassageTimer.state = 'stopped';
    document.getElementById('repassage-status').className = 'status-display stopped';
    document.getElementById('repassage-state').textContent = 'Arrêtée';
    
    document.getElementById('repassage-start').disabled = false;
    document.getElementById('repassage-pause').disabled = true;
    document.getElementById('repassage-resume').disabled = true;
    document.getElementById('repassage-stop').disabled = true;
}

function updateRepassageTimer() {
    const now = new Date();
    const elapsed = (now.getTime() - repassageTimer.startTime.getTime() + repassageTimer.pausedTime) / 1000;
    const hours = Math.floor(elapsed / 3600);
    const minutes = Math.floor((elapsed % 3600) / 60);
    const seconds = Math.floor(elapsed % 60);
    
    const timeStr = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    document.getElementById('repassage-timer').textContent = timeStr;
    document.getElementById('repassage-net-time').textContent = timeStr;
}

// Save functions for Calandre and Repassage
async function saveCalandreProduction(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'save_calandre_production');
    
    // Add timer data
    const realDuration = calandreTimer.pausedTime / 60000 + 
        (calandreTimer.startTime ? (Date.now() - calandreTimer.startTime.getTime()) / 60000 : 0);
    formData.append('real_duration', realDuration);
    
    try {
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Production calandre enregistrée avec succès!');
            event.target.reset();
            resetCalandreTimer();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de sauvegarde:', error);
        alert('Erreur de sauvegarde');
    }
}

async function saveRepassageProduction(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    formData.append('action', 'save_repassage_production');
    
    // Add timer data
    const realDuration = repassageTimer.pausedTime / 60000 + 
        (repassageTimer.startTime ? (Date.now() - repassageTimer.startTime.getTime()) / 60000 : 0);
    formData.append('real_duration', realDuration);
    
    try {
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Production repassage enregistrée avec succès!');
            event.target.reset();
            resetRepassageTimer();
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de sauvegarde:', error);
        alert('Erreur de sauvegarde');
    }
}

function resetCalandreTimer() {
    calandreTimer.pausedTime = 0;
    calandreTimer.startTime = null;
    document.getElementById('calandre-timer').textContent = '00:00:00';
    document.getElementById('calandre-net-time').textContent = '00:00:00';
    stopCalandre();
}

function resetRepassageTimer() {
    repassageTimer.pausedTime = 0;
    repassageTimer.startTime = null;
    document.getElementById('repassage-timer').textContent = '00:00:00';
    document.getElementById('repassage-net-time').textContent = '00:00:00';
    stopRepassage();
}

// Load data tables for Arrêts and Non-conformités
async function loadArretsTable() {
    try {
        const response = await fetch('ajax.php?action=get_arrets');
        const result = await response.json();
        
        if (result.success) {
            const container = document.getElementById('arrets-table-container');
            if (result.arrets.length === 0) {
                container.innerHTML = '<p>Aucun arrêt enregistré</p>';
                return;
            }
            
            let tableHTML = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Équipement</th>
                            <th>Raison</th>
                            <th>Durée</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            result.arrets.forEach(arret => {
                const date = new Date(arret.created_at).toLocaleDateString('fr-FR');
                tableHTML += `
                    <tr>
                        <td>${date}</td>
                        <td>${arret.equipment}</td>
                        <td>${arret.reason}</td>
                        <td>${arret.duration_minutes} min</td>
                        <td>${arret.comment || '-'}</td>
                    </tr>
                `;
            });
            
            tableHTML += '</tbody></table>';
            container.innerHTML = tableHTML;
        }
    } catch (error) {
        console.error('Erreur de chargement des arrêts:', error);
    }
}

async function loadNonConformitesTable() {
    try {
        const response = await fetch('ajax.php?action=get_nonconformites');
        const result = await response.json();
        
        if (result.success) {
            const container = document.getElementById('nc-table-container');
            if (result.nonconformites.length === 0) {
                container.innerHTML = '<p>Aucune non-conformité enregistrée</p>';
                return;
            }
            
            let tableHTML = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Équipement</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Gravité</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            result.nonconformites.forEach(nc => {
                const date = new Date(nc.created_at).toLocaleDateString('fr-FR');
                const severityColor = nc.severity === 'critique' ? 'red' : nc.severity === 'majeure' ? 'orange' : 'green';
                tableHTML += `
                    <tr>
                        <td>${date}</td>
                        <td>${nc.equipment}</td>
                        <td>${nc.type}</td>
                        <td>${nc.quantity}</td>
                        <td><span style="color: ${severityColor}; font-weight: bold;">${nc.severity}</span></td>
                        <td>${nc.description}</td>
                    </tr>
                `;
            });
            
            tableHTML += '</tbody></table>';
            container.innerHTML = tableHTML;
        }
    } catch (error) {
        console.error('Erreur de chargement des non-conformités:', error);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check if user is already logged in
    fetch('ajax.php?action=check_auth')
        .then(response => response.json())
        .then(result => {
            isAuthenticated = result.authenticated;
        })
        .catch(error => console.error('Erreur de vérification de l\'authentification:', error));
    
    // Load data tables when pages are shown
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const target = mutation.target;
                if (target.classList.contains('active')) {
                    if (target.id === 'arrets') {
                        loadArretsTable();
                    } else if (target.id === 'nonconformites') {
                        loadNonConformitesTable();
                    }
                }
            }
        });
    });
    
    // Observe all page elements
    document.querySelectorAll('.page').forEach(page => {
        observer.observe(page, { attributes: true });
    });
});

// Machine/Program Capacity Configuration Functions
async function loadMachineProgramCapacities() {
    try {
        const response = await fetch('ajax.php?action=get_machine_program_capacities');
        const result = await response.json();
        
        if (result.success) {
            populateMachineProgramCapacityConfig(result.capacities, result.programs);
        } else {
            console.error('Erreur de chargement des capacités:', result.message);
        }
    } catch (error) {
        console.error('Erreur de chargement des capacités:', error);
    }
}

function populateMachineProgramCapacityConfig(capacities, programs) {
    const machines = [13, 20, 50, 70]; // Machine types
    
    machines.forEach(machineNumber => {
        const container = document.getElementById(`machine-${machineNumber}-programs-config`);
        if (!container) return;
        
        let html = '<div class="programs-grid">';
        
        programs.forEach(program => {
            // Find existing capacity for this machine/program combination
            const existingCapacity = capacities.find(c => 
                c.machine_number == machineNumber && c.program_id == program.id
            );
            
            const currentCapacity = existingCapacity ? existingCapacity.optimal_capacity : '';
            
            html += `
                <div class="program-capacity-item">
                    <label>Programme ${program.program_number} - ${program.name}:</label>
                    <div class="capacity-input-group">
                        <input type="number" 
                               id="capacity-${machineNumber}-${program.id}"
                               data-machine="${machineNumber}"
                               data-program="${program.id}"
                               value="${currentCapacity}"
                               placeholder="Capacité optimale (kg)"
                               min="0"
                               step="0.1"
                               class="capacity-input">
                        <span class="unit">kg</span>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    });
}

async function saveMachineProgramCapacities() {
    const capacities = [];
    
    // Collect all capacity inputs
    document.querySelectorAll('.capacity-input').forEach(input => {
        const machineNumber = input.getAttribute('data-machine');
        const programId = input.getAttribute('data-program');
        const optimalCapacity = parseFloat(input.value);
        
        if (optimalCapacity > 0) { // Only save if capacity is specified
            capacities.push({
                machine_number: parseInt(machineNumber),
                program_id: parseInt(programId),
                optimal_capacity: optimalCapacity
            });
        }
    });
    
    if (capacities.length === 0) {
        alert('Veuillez spécifier au moins une capacité');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'save_machine_program_capacities');
        formData.append('capacities', JSON.stringify(capacities));
        
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Configuration des capacités sauvegardée avec succès!');
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de sauvegarde des capacités:', error);
        alert('Erreur de sauvegarde des capacités');
    }
}

// Load machine/program capacities when settings page is shown
function loadMachineProgramCapacitiesIfNeeded() {
    if (currentPage === 'reglages') {
        loadMachineProgramCapacities();
    }
}

// Update the showPage function to load capacities when needed
const originalShowPage = showPage;
showPage = function(pageId) {
    originalShowPage(pageId);
    
    if (pageId === 'reglages') {
        // Load machine/program capacities after a short delay to ensure DOM is ready
        setTimeout(() => {
            loadMachineProgramCapacities();
        }, 100);
    }
};

// Equipment Capacity Configuration Functions
async function saveAllEquipmentCapacities() {
    const equipmentCapacities = {
        // Machines à Laver
        machine_13_capacity: parseFloat(document.getElementById('machine-13-capacity').value),
        machine_20_capacity: parseFloat(document.getElementById('machine-20-capacity').value),
        machine_50_capacity: parseFloat(document.getElementById('machine-50-capacity').value),
        machine_70_capacity: parseFloat(document.getElementById('machine-70-capacity').value),
        
        // Séchoirs
        sechoir_1_capacity: parseFloat(document.getElementById('sechoir-1-capacity').value),
        sechoir_2_capacity: parseFloat(document.getElementById('sechoir-2-capacity').value),
        sechoir_3_capacity: parseFloat(document.getElementById('sechoir-3-capacity').value),
        sechoir_4_capacity: parseFloat(document.getElementById('sechoir-4-capacity').value),
        
        // Calandre
        calandre_capacity: parseFloat(document.getElementById('calandre-capacity').value),
        calandre_target_rate: parseInt(document.getElementById('calandre-target-rate').value),
        
        // Repassage
        repassage_capacity: parseFloat(document.getElementById('repassage-capacity').value),
        repassage_target_rate: parseInt(document.getElementById('repassage-target-rate').value),
        
        // Arrêts
        arrets_alert_threshold: parseInt(document.getElementById('arrets-alert-threshold').value),
        arrets_planned_max: parseInt(document.getElementById('arrets-planned-max').value),
        
        // Non-conformités
        nc_alert_threshold: parseInt(document.getElementById('nc-alert-threshold').value),
        nc_average_weight: parseFloat(document.getElementById('nc-average-weight').value)
    };
    
    try {
        const formData = new FormData();
        formData.append('action', 'save_equipment_config');
        
        // Add all equipment capacities to form data
        Object.keys(equipmentCapacities).forEach(key => {
            formData.append(key, equipmentCapacities[key]);
        });
        
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Configuration des capacités équipements sauvegardée avec succès!');
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de sauvegarde des capacités équipements:', error);
        alert('Erreur de sauvegarde des capacités équipements');
    }
}

function resetEquipmentCapacitiesToDefault() {
    if (!confirm('Êtes-vous sûr de vouloir remettre toutes les capacités aux valeurs par défaut ?')) {
        return;
    }
    
    // Reset to default values
    document.getElementById('machine-13-capacity').value = 13;
    document.getElementById('machine-20-capacity').value = 20;
    document.getElementById('machine-50-capacity').value = 50;
    document.getElementById('machine-70-capacity').value = 70;
    
    document.getElementById('sechoir-1-capacity').value = 25;
    document.getElementById('sechoir-2-capacity').value = 25;
    document.getElementById('sechoir-3-capacity').value = 25;
    document.getElementById('sechoir-4-capacity').value = 25;
    
    document.getElementById('calandre-capacity').value = 15;
    document.getElementById('calandre-target-rate').value = 60;
    
    document.getElementById('repassage-capacity').value = 10;
    document.getElementById('repassage-target-rate').value = 25;
    
    document.getElementById('arrets-alert-threshold').value = 30;
    document.getElementById('arrets-planned-max').value = 120;
    
    document.getElementById('nc-alert-threshold').value = 5;
    document.getElementById('nc-average-weight').value = 0.5;
    
    alert('Valeurs par défaut restaurées');
}

// Manual Stations Configuration (updated to work with new structure)
async function saveManualStationsConfig() {
    const calandreRate = parseInt(document.getElementById('calandre-target-rate').value);
    const repassageRate = parseInt(document.getElementById('repassage-target-rate').value);
    
    try {
        const formData = new FormData();
        formData.append('action', 'save_manual_stations_config');
        formData.append('calandre_rate', calandreRate);
        formData.append('repassage_rate', repassageRate);
        
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Configuration des cadences sauvegardée avec succès!');
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de sauvegarde des cadences:', error);
        alert('Erreur de sauvegarde des cadences');
    }
}

// Calendar Configuration
async function saveCalendarConfig() {
    const workingHours = parseFloat(document.getElementById('working-hours-per-day').value);
    const workingDays = parseInt(document.getElementById('working-days-per-week').value);
    const shiftStart = document.getElementById('shift-start').value;
    const shiftEnd = document.getElementById('shift-end').value;
    
    try {
        const formData = new FormData();
        formData.append('action', 'save_calendar_config');
        formData.append('working_hours', workingHours);
        formData.append('working_days', workingDays);
        formData.append('shift_start', shiftStart);
        formData.append('shift_end', shiftEnd);
        
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Configuration du calendrier sauvegardée avec succès!');
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de sauvegarde du calendrier:', error);
        alert('Erreur de sauvegarde du calendrier');
    }
}

// Thresholds Configuration
async function saveThresholdsConfig() {
    const thresholds = {
        disponibilite_red: parseInt(document.getElementById('disponibilite-red').value),
        disponibilite_amber: parseInt(document.getElementById('disponibilite-amber').value),
        performance_red: parseInt(document.getElementById('performance-red').value),
        performance_amber: parseInt(document.getElementById('performance-amber').value),
        qualite_red: parseInt(document.getElementById('qualite-red').value),
        qualite_amber: parseInt(document.getElementById('qualite-amber').value),
        trs_red: parseInt(document.getElementById('trs-red').value),
        trs_amber: parseInt(document.getElementById('trs-amber').value)
    };
    
    try {
        const formData = new FormData();
        formData.append('action', 'save_thresholds_config');
        
        Object.keys(thresholds).forEach(key => {
            formData.append(key, thresholds[key]);
        });
        
        const response = await fetch('ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Seuils TRS sauvegardés avec succès!');
        } else {
            alert('Erreur: ' + result.message);
        }
    } catch (error) {
        console.error('Erreur de sauvegarde des seuils:', error);
        alert('Erreur de sauvegarde des seuils');
    }
}

function resetThresholdsToDefault() {
    if (!confirm('Êtes-vous sûr de vouloir remettre les seuils aux valeurs par défaut ?')) {
        return;
    }
    
    document.getElementById('disponibilite-red').value = 80;
    document.getElementById('disponibilite-amber').value = 90;
    document.getElementById('performance-red').value = 75;
    document.getElementById('performance-amber').value = 85;
    document.getElementById('qualite-red').value = 85;
    document.getElementById('qualite-amber').value = 95;
    document.getElementById('trs-red').value = 60;
    document.getElementById('trs-amber').value = 75;
    
    alert('Seuils remis aux valeurs par défaut');
}

// Show machine type selector for capacity configuration
function showMachineTypeSelector(programId) {
    const capacityGroup = document.querySelector('#machineProgram .form-group:last-child');
    
    // Create machine type selector if it doesn't exist
    let machineSelector = document.getElementById('machine-type-selector');
    if (!machineSelector) {
        const selectorHTML = `
            <div class="form-group" id="machine-type-selector">
                <label for="machine-type-select">Type de Machine :</label>
                <select id="machine-type-select" onchange="updateCapacityFromExcel(${programId})">
                    <option value="">Sélectionner le type de machine</option>
                    <option value="13">Machine 13kg</option>
                    <option value="20">Machine 20kg</option>
                    <option value="50">Machine 50kg</option>
                    <option value="70">Machine 70kg</option>
                </select>
            </div>
        `;
        capacityGroup.insertAdjacentHTML('beforebegin', selectorHTML);
    }
    
    // Make capacity field read-only and add explanation
    const capacityField = document.getElementById('new-program-capacity');
    capacityField.readOnly = true;
    capacityField.placeholder = 'Sélectionnez d\'abord le type de machine';
    capacityField.title = 'Capacité automatiquement définie selon les données Excel';
    
    // Add explanation text
    let explanationText = document.getElementById('capacity-explanation');
    if (!explanationText) {
        const explanation = document.createElement('small');
        explanation.id = 'capacity-explanation';
        explanation.style.color = '#666';
        explanation.style.fontStyle = 'italic';
        explanation.textContent = 'La capacité est automatiquement définie selon les données Excel pour chaque type de machine.';
        capacityField.parentNode.appendChild(explanation);
    }
}

// Update capacity from Excel data based on machine type and program
async function updateCapacityFromExcel(programId) {
    const machineType = document.getElementById('machine-type-select').value;
    const capacityField = document.getElementById('new-program-capacity');
    
    if (!machineType) {
        capacityField.value = '';
        capacityField.placeholder = 'Sélectionnez d\'abord le type de machine';
        return;
    }
    
    try {
        const response = await fetch(`ajax.php?action=get_program_capacity&program_id=${programId}&machine_type=${machineType}`);
        const result = await response.json();
        
        if (result.success && result.capacity) {
            capacityField.value = result.capacity;
            capacityField.placeholder = `Capacité Excel: ${result.capacity} kg`;
        } else {
            capacityField.value = '';
            capacityField.placeholder = 'Aucune capacité définie dans Excel pour cette combinaison';
        }
    } catch (error) {
        console.error('Erreur lors de la récupération de la capacité:', error);
        capacityField.value = '';
        capacityField.placeholder = 'Erreur de récupération des données';
    }
}

// Reset form when not editing
function resetMachineProgramForm() {
    // Remove machine type selector
    const machineSelector = document.getElementById('machine-type-selector');
    if (machineSelector) {
        machineSelector.remove();
    }
    
    // Reset capacity field
    const capacityField = document.getElementById('new-program-capacity');
    capacityField.readOnly = false;
    capacityField.placeholder = 'Capacité recommandée (kg)';
    capacityField.title = '';
    
    // Remove explanation text
    const explanationText = document.getElementById('capacity-explanation');
    if (explanationText) {
        explanationText.remove();
    }
}

// Show séchoir type selector for temperature configuration
function showSechoirTypeSelector(programId) {
    const temperatureGroup = document.querySelector('#dryingProgram .form-group:last-child');
    
    // Create séchoir type selector if it doesn't exist
    let sechoirSelector = document.getElementById('sechoir-type-selector');
    if (!sechoirSelector) {
        const selectorHTML = `
            <div class="form-group" id="sechoir-type-selector">
                <label for="sechoir-type-select">Type de Séchoir :</label>
                <select id="sechoir-type-select" onchange="updateTemperatureFromExcel(${programId})">
                    <option value="">Sélectionner le type de séchoir</option>
                    <option value="1">Séchoir 1</option>
                    <option value="2">Séchoir 2</option>
                    <option value="3">Séchoir 3</option>
                    <option value="4">Séchoir 4</option>
                </select>
            </div>
        `;
        temperatureGroup.insertAdjacentHTML('beforebegin', selectorHTML);
    }
    
    // Make temperature field read-only and add explanation
    const temperatureField = document.getElementById('new-drying-temperature');
    temperatureField.readOnly = true;
    temperatureField.placeholder = 'Sélectionnez d\'abord le type de séchoir';
    temperatureField.title = 'Température automatiquement définie selon les données Excel';
    
    // Add explanation text
    let explanationText = document.getElementById('temperature-explanation');
    if (!explanationText) {
        const explanation = document.createElement('small');
        explanation.id = 'temperature-explanation';
        explanation.style.color = '#666';
        explanation.style.fontStyle = 'italic';
        explanation.textContent = 'La température est automatiquement définie selon les données Excel pour chaque type de séchoir.';
        temperatureField.parentNode.appendChild(explanation);
    }
}

// Update temperature from Excel data based on séchoir type and program
async function updateTemperatureFromExcel(programId) {
    const sechoirType = document.getElementById('sechoir-type-select').value;
    const temperatureField = document.getElementById('new-drying-temperature');
    
    if (!sechoirType) {
        temperatureField.value = '';
        temperatureField.placeholder = 'Sélectionnez d\'abord le type de séchoir';
        return;
    }
    
    try {
        const response = await fetch(`ajax.php?action=get_drying_program_temperature&program_id=${programId}&sechoir_type=${sechoirType}`);
        const result = await response.json();
        
        if (result.success && result.temperature) {
            temperatureField.value = result.temperature;
            temperatureField.placeholder = `Température Excel: ${result.temperature} °C`;
        } else {
            temperatureField.value = '';
            temperatureField.placeholder = 'Aucune température définie dans Excel pour cette combinaison';
        }
    } catch (error) {
        console.error('Erreur lors de la récupération de la température:', error);
        temperatureField.value = '';
        temperatureField.placeholder = 'Erreur de récupération des données';
    }
}

// Reset drying program form when not editing
function resetDryingProgramForm() {
    // Remove séchoir type selector
    const sechoirSelector = document.getElementById('sechoir-type-selector');
    if (sechoirSelector) {
        sechoirSelector.remove();
    }
    
    // Reset temperature field
    const temperatureField = document.getElementById('new-drying-temperature');
    temperatureField.readOnly = false;
    temperatureField.placeholder = 'Température consigne (°C)';
    temperatureField.title = '';
    
    // Remove explanation text
    const explanationText = document.getElementById('temperature-explanation');
    if (explanationText) {
        explanationText.remove();
    }
}
