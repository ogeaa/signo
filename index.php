<?php include 'header.php'; ?>

<div class="container">
    <!-- Título principal com ícone -->
    <h1 class="mb-4 text-center">
        <i class="fas fa-star"></i>
        Descubra seu Signo Zodiacal
        <i class="fas fa-moon"></i>
    </h1>
    
    <!-- Subtítulo explicativo -->
    <p class="text-center text-white mb-4 opacity-75">
        Digite sua data de nascimento e descubra qual signo do zodíaco rege sua personalidade
    </p>
    
    <!-- Formulário modernizado -->
    <div class="form-container">
        <form action="show_zodiac_sign.php" method="POST" id="zodiacForm">
            <div class="mb-4">
                <label for="data_nascimento" class="form-label">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Data de Nascimento
                </label>
                <input 
                    type="date" 
                    class="form-control" 
                    id="data_nascimento" 
                    name="data_nascimento" 
                    required 
                    aria-describedby="dateHelp"
                />
                <div id="dateHelp" class="form-text">
                    Selecione o dia, mês e ano do seu nascimento
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-magic me-2"></i>
                Consultar Signo
            </button>
        </form>
    </div>
    
    <!-- Informações adicionais -->
    <div class="text-center mt-4">
        <small class="text-white opacity-75">
            <i class="fas fa-info-circle me-1"></i>
            Sistema desenvolvido com PHP, JavaScript e Bootstrap
        </small>
    </div>
</div>

<!-- Scripts do Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
