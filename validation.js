/**
 * Sistema de Validação para Consulta de Signo Zodiacal
 * Trabalho de Faculdade - Project 2
 * 
 * Funcionalidades:
 * - Validação de data de nascimento
 * - Feedback visual em tempo real
 * - Prevenção de envio com dados inválidos
 * - Melhorias de UX
 */

// ===== CONFIGURAÇÕES =====
const CONFIG = {
    minYear: 1900,
    maxYear: new Date().getFullYear(),
    minAge: 0,
    maxAge: 150
};

// ===== INICIALIZAÇÃO =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔮 Sistema de Validação de Signo Zodiacal iniciado');
    
    // Inicializar validações
    initializeValidations();
    
    // Adicionar animações
    addAnimations();
    
    // Configurar histórico
    setupHistory();
});

// ===== VALIDAÇÕES =====
function initializeValidations() {
    const form = document.querySelector('form');
    const dateInput = document.getElementById('data_nascimento');
    const submitBtn = document.querySelector('.btn-primary');
    
    if (!form || !dateInput || !submitBtn) {
        console.warn('⚠️ Elementos do formulário não encontrados');
        return;
    }
    
    // Validação em tempo real
    dateInput.addEventListener('input', function() {
        validateDateInput(this);
    });
    
    // Validação no envio
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            showError('Por favor, corrija os erros antes de continuar.');
        } else {
            showLoading();
        }
    });
    
    // Validação inicial
    validateDateInput(dateInput);
}

function validateDateInput(input) {
    const value = input.value;
    const feedback = getOrCreateFeedback(input);
    
    // Limpar feedback anterior
    clearFeedback(input);
    
    if (!value) {
        showFeedback(input, 'Selecione sua data de nascimento', 'warning');
        return false;
    }
    
    const date = new Date(value);
    const today = new Date();
    const age = calculateAge(date);
    
    // Validações
    if (isNaN(date.getTime())) {
        showFeedback(input, 'Data inválida', 'error');
        return false;
    }
    
    if (date > today) {
        showFeedback(input, 'Data não pode ser futura', 'error');
        return false;
    }
    
    if (date.getFullYear() < CONFIG.minYear) {
        showFeedback(input, `Ano deve ser maior que ${CONFIG.minYear}`, 'error');
        return false;
    }
    
    if (age < CONFIG.minAge) {
        showFeedback(input, 'Idade muito baixa', 'error');
        return false;
    }
    
    if (age > CONFIG.maxAge) {
        showFeedback(input, 'Idade muito alta', 'error');
        return false;
    }
    
    // Sucesso
    showFeedback(input, `✅ Idade: ${age} anos`, 'success');
    return true;
}

function validateForm() {
    const dateInput = document.getElementById('data_nascimento');
    return validateDateInput(dateInput);
}

// ===== FEEDBACK VISUAL =====
function getOrCreateFeedback(input) {
    let feedback = input.parentNode.querySelector('.validation-feedback');
    if (!feedback) {
        feedback = document.createElement('div');
        feedback.className = 'validation-feedback';
        input.parentNode.appendChild(feedback);
    }
    return feedback;
}

function showFeedback(input, message, type) {
    const feedback = getOrCreateFeedback(input);
    feedback.textContent = message;
    feedback.className = `validation-feedback validation-${type}`;
    
    // Adicionar classe ao input
    input.className = input.className.replace(/is-valid|is-invalid|is-warning/g, '');
    input.classList.add(`is-${type === 'error' ? 'invalid' : type === 'success' ? 'valid' : 'warning'}`);
}

function clearFeedback(input) {
    const feedback = input.parentNode.querySelector('.validation-feedback');
    if (feedback) {
        feedback.textContent = '';
        feedback.className = 'validation-feedback';
    }
}

function showError(message) {
    // Criar ou atualizar alerta de erro
    let alert = document.querySelector('.alert-error');
    if (!alert) {
        alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-error';
        document.querySelector('.container').insertBefore(alert, document.querySelector('.container').firstChild);
    }
    alert.textContent = message;
    alert.style.display = 'block';
    
    // Auto-remover após 5 segundos
    setTimeout(() => {
        alert.style.display = 'none';
    }, 5000);
}

// ===== LOADING STATE =====
function showLoading() {
    const submitBtn = document.querySelector('.btn-primary');
    if (submitBtn) {
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Consultando...';
        submitBtn.disabled = true;
    }
}

// ===== UTILITÁRIOS =====
function calculateAge(birthDate) {
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return age;
}

// ===== ANIMAÇÕES =====
function addAnimations() {
    // Animação de entrada dos elementos
    const elements = document.querySelectorAll('.form-container, .result-card');
    elements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// ===== HISTÓRICO =====
function setupHistory() {
    // Salvar consultas no localStorage
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            const dateInput = document.getElementById('data_nascimento');
            if (dateInput && dateInput.value) {
                saveToHistory(dateInput.value);
            }
        });
    }
    
    // Mostrar histórico se existir
    showHistory();
}

function saveToHistory(date) {
    let history = JSON.parse(localStorage.getItem('zodiacHistory') || '[]');
    const entry = {
        date: date,
        timestamp: new Date().toISOString()
    };
    
    // Adicionar no início e limitar a 10 entradas
    history.unshift(entry);
    history = history.slice(0, 10);
    
    localStorage.setItem('zodiacHistory', JSON.stringify(history));
}

function showHistory() {
    const history = JSON.parse(localStorage.getItem('zodiacHistory') || '[]');
    if (history.length > 0) {
        console.log('📚 Histórico de consultas:', history);
    }
}

// ===== MELHORIAS DE ACESSIBILIDADE =====
function enhanceAccessibility() {
    // Adicionar labels para screen readers
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        if (!input.getAttribute('aria-label')) {
            input.setAttribute('aria-label', input.previousElementSibling?.textContent || 'Campo de entrada');
        }
    });
    
    // Adicionar roles
    const buttons = document.querySelectorAll('button');
    buttons.forEach(button => {
        if (!button.getAttribute('role')) {
            button.setAttribute('role', 'button');
        }
    });
}

// ===== EXPORTAR FUNÇÕES (para uso global) =====
window.ZodiacValidator = {
    validateDateInput,
    validateForm,
    showError,
    showLoading,
    calculateAge
};
