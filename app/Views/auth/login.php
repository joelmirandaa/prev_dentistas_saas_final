<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prev Dentistas | Seu Sorriso, Nossa Prioridade</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/login.css">
</head>
<body>

    <header>
        <div class="container nav-flex">
            <div class="logo">
                <a href="#"><i class="fas fa-tooth"></i> Prev Dentistas</a>
            </div>
            
            <div class="mobile-toggle" id="mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>

            <nav class="nav-links" id="nav-links">
                <a href="#inicio">Início</a>
                <a href="#especialistas">Especialistas</a>
                <a href="#localizacao">Localização</a>
                
                <form action="<?= BASE_URL ?>login" method="POST" class="header-login-form">
                    <?= \App\Helpers\CsrfHelper::input() ?>
                    <!-- [DESATIVADO TEMPORARIAMENTE] Pronto para ser ativado quando o sistema operar com múltiplas clínicas:
                    <input type="text" name="clinica_identificador" placeholder="Clínica (Código/CNPJ)" required style="width: 130px;">
                    -->
                    <input type="text" name="login" placeholder="Usuário" required>
                    <input type="password" name="senha" placeholder="Senha" required>
                    <button type="submit"><i class="fas fa-sign-in-alt"></i> Entrar</button>
                </form>
            </nav>
        </div>
    </header>

    <?php if(isset($_GET['erro'])): ?>
        <div style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); z-index: 1100; background: #e74c3c; color: white; padding: 10px 20px; border-radius: 5px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <i class="fas fa-exclamation-circle"></i> Dados de acesso inválidos.
        </div>
    <?php endif; ?>

    <section id="inicio" class="hero-slider">
        <div class="slide active" style="background-image: url('<?= BASE_URL ?>assets/img/dentista-5.jpeg');"></div>
        <div class="slide" style="background-image: url('<?= BASE_URL ?>assets/img/cadeira.jpeg'); filter: hue-rotate(20deg);"></div> 
        <div class="slide" style="background-image: url('<?= BASE_URL ?>assets/img/card1.jpg'); background-position: center;"></div>

        <div class="container hero-content">
            <div class="hero-text-wrap">
                <h1>Transforme seu sorriso<br>com especialistas!</h1>
                <p>Na <strong>Prev Dentistas</strong>, unimos tecnologia de ponta e atendimento humanizado para devolver sua confiança.</p>
                
                <div class="hero-features">
                    <span class="hero-tag"><i class="fas fa-check-circle"></i> Estrutura Moderna</span>
                    <span class="hero-tag"><i class="fas fa-wifi"></i> Espaço VIP</span>
                    <span class="hero-tag"><i class="far fa-credit-card"></i> Até 10x sem juros</span>
                </div>

                <div class="hero-btn-group">
                    <a href="https://wa.me/5591983067459" target="_blank" class="btn btn-success btn-lg">
                        <i class="fab fa-whatsapp"></i> Agendar Consulta
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="especialistas" class="section-padding">
        <div class="container">
            <h2 class="section-title">Corpo Clínico</h2>
            <p style="text-align: center; color: var(--text-light); max-width: 600px; margin: 0 auto;">
                Nossa equipe é formada por especialistas dedicados a proporcionar o melhor tratamento para você e sua família.
            </p>
            
            <div class="team-grid">
                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="<?= BASE_URL ?>assets/img/dentista-8.jpg" alt="Dra. Luciana Farias">
                    </div>
                    <span class="role-tag">Ortodontia</span>
                    <h3>Dra. Luciana Farias</h3>
                    <p>Especialista em criar sorrisos alinhados e saúde bucal integral. Cuidado e precisão em cada detalhe.</p>
                </div>

                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="<?= BASE_URL ?>assets/img/dentista-2.jpeg" alt="Dra. Vitória Lobato">
                    </div>
                    <span class="role-tag">Saúde Coletiva</span>
                    <h3>Dra. Vitória Lobato</h3>
                    <p>Experiência e humanização no tratamento de pacientes de todas as idades.</p>
                </div>

                <div class="team-card">
                    <div class="img-wrapper">
                        <img src="<?= BASE_URL ?>assets/img/dentista-7.jpeg" class="img-centered" alt="Estética Dental">
                    </div>
                    <span class="role-tag">Especialização em endodontia</span>
                    <h3>Dra. Ana Lopes</h3>
                    <p>Excelência no tratamento de canal e recuperação da saúde dental, preservando a vitalidade e função dos seus dentes.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="localizacao" class="section-padding location-section">
        <div class="container">
            <h2 class="section-title">Onde Estamos</h2>
            
            <div class="contact-grid">
                <div class="contact-info-box">
                    <h3>Visite a Clínica</h3>
                    <p style="margin-bottom: 20px; color: var(--text-light);">Localização privilegiada em Ananindeua com estacionamento fácil.</p>
                    
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Endereço:</strong><br>
                            Rua União 1, Esquina com Rua D<br>
                            Atalaia, Ananindeua - PA, 67013-350
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="fas fa-phone-alt"></i>
                        <div>
                            <strong>Telefone / WhatsApp:</strong><br>
                            (91) 98306-7459
                        </div>
                    </div>

                    <div class="info-item">
                        <i class="far fa-clock"></i>
                        <div>
                            <strong>Horário de Atendimento:</strong><br>
                            Seg - Sex: 08h às 12h e 15h às 18h<br>
                            Sábado: 08h às 12h
                        </div>
                    </div>

                    <a href="https://www.google.com/maps/dir/?api=1&destination=Prev+Dentistas+Ananindeua" target="_blank" class="btn btn-primary" style="margin-top: 20px; width: 100%; text-align: center;">
                        <i class="fas fa-directions"></i> Ver no Google Maps
                    </a>
                </div>

                <div class="map-frame">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.645365860427!2d-48.42899942535724!3d-1.3893519985975165!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x92a48b002f32a7cf%3A0x353ede76a35e88fa!2sPrev%20Dentistas!5e0!3m2!1sen!2sbr!4v1771775822488!5m2!1sen!2sbr" 
                        width="600" 
                        height="450" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p>© <?= date('Y') ?> Prev Dentistas. Feito com <i class="fas fa-heart" style="color: #fab1a0;"></i> para o seu sorriso.</p>
            <div style="margin-top: 10px; font-size: 0.8rem; opacity: 0.7;">
                Responsável Técnico: Dra. Luciana Farias
            </div>
        </div>
    </footer>

    <a href="https://wa.me/5591983067459" class="fab-whatsapp" target="_blank" title="Fale conosco no WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script src="<?= BASE_URL ?>assets/js/modules/login.js"></script>
</body>
</html>
