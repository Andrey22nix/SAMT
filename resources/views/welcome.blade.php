<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SIMIT - Sistema Integrado de Información sobre Multas y Sanciones</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap">
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      margin: 0;
      background-color: #f5f5f5;
    }
    header {
      background-color: #003087;
      padding: 1em;
      text-align: center;
    }
    .header-contacts {
      display: flex;
      justify-content: center;
      gap: 1.5em;
      color: white;
      font-size: 1em;
      margin-top: 1em;
      flex-wrap: nowrap;
    }
    .header-contacts a {
      color: white;
      text-decoration: none;
      font-weight: 600;
    }
    .header-contacts a:hover {
      color: #00a651;
    }
    .subheader-layer {
      background-color: white;
      padding: 0.5em;
      text-align: center;
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 1em;
      flex-direction: row;
      flex-wrap: nowrap;
    }
    .subheader-layer img {
      width: auto;
      height: auto;
      max-height: 60px;
    }
    .header-line {
      height: 4px;
      background: linear-gradient(
        to right,
        #FFC107 33.33%, /* Yellow */
        #003F8C 33.33%, /* Blue */
        #003F8C 66.66%, /* Blue */
        #D81B60 66.66%  /* Red */
      );
      width: 100%;
    }
    .hero {
      background: url('{{ asset("image-1.png") }}') no-repeat top left / cover;
      padding: 2em 1em;
      text-align: center;
      min-height: 50vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .hero h2 {
      font-size: 2.5em;
      font-weight: 700;
      color: #003087;
      margin-bottom: 0.5em;
    }
    .hero p {
      font-size: 1.2em;
      color: #333;
      margin-bottom: 1.5em;
    }
    .hero form {
      background: transparent;
      padding: 2em;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 100%;
      max-width: 500px;
    }
    select,
    input[type='text'] {
      padding: 0.8em;
      border: 1px solid #ccc;
      border-radius: 5px;
      margin: 0.5em 0;
      font-size: 1em;
      width: 100%;
      max-width: 350px;
      background: rgba(249, 249, 249, 0.9);
    }
    select:focus,
    input:focus {
      border-color: #00a651;
      outline: none;
      box-shadow: 0 0 5px rgba(0, 166, 81, 0.3);
    }
    button[type='submit'] {
      background-color: #00a651;
      border: none;
      padding: 0.8em 2em;
      border-radius: 5px;
      color: white;
      font-size: 1em;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    button[type='submit']:hover {
      background-color: #008c45;
    }
    .process-cover {
      background: #f5f5f5;
      padding: 2em 1em;
      text-align: center;
    }
    .process-cover h2 {
      color: #003087;
      font-size: 1.8em;
      margin-bottom: 1em;
    }
    .process-steps {
      max-width: 1200px;
      margin: auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5em;
    }
    .process-steps div {
      background: white;
      padding: 1.5em;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      text-align: left;
    }
    .process-steps h3 {
      color: #003087;
      font-size: 1.2em;
      margin-bottom: 0.5em;
    }
    .process-steps p {
      color: #333;
      font-size: 1em;
      line-height: 1.5;
    }
    #loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 9999;
      display: none;
      justify-content: center;
      align-items: center;
      flex-direction: column;
    }
    #loading-overlay img {
      width: 80px;
      height: auto;
      margin-bottom: 1em;
    }
    #loading-overlay p {
      color: white;
      font-size: 1.2em;
      font-weight: 600;
    }
    #confirmation-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 10000;
      display: none;
      justify-content: center;
      align-items: center;
    }
    .modal-box {
      background: white;
      padding: 2em;
      border-radius: 8px;
      text-align: center;
      max-width: 500px;
      width: 90%;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }
    .modal-box h2 {
      font-size: 1.5em;
      font-weight: 700;
      color: #003087;
      margin-bottom: 1em;
    }
    .modal-box p {
      font-size: 1em;
      color: #333;
      margin-bottom: 1em;
    }
    .modal-box .modal-image {
      width: 60px;
      height: auto;
      margin-bottom: 1em;
    }
    .modal-box label {
      display: block;
      margin: 1em 0;
      font-size: 0.9em;
      color: #333;
    }
    .modal-box label a {
      color: #00a651;
      text-decoration: underline;
    }
    .modal-buttons {
      display: flex;
      justify-content: center;
      gap: 1em;
      margin-top: 1em;
    }
    .modal-buttons button {
      padding: 0.8em 1.5em;
      border: none;
      border-radius: 5px;
      font-size: 1em;
      font-weight: 600;
      cursor: pointer;
    }
    .modal-buttons .confirm-btn {
      background-color: #00a651;
      color: white;
    }
    .modal-buttons .confirm-btn:hover {
      background-color: #008c45;
    }
    .modal-buttons .confirm-btn:disabled {
      background-color: #ccc;
      cursor: not-allowed;
    }
    .modal-buttons .cancel-btn {
      background-color: #e0e0e0;
      color: #333;
    }
    .modal-buttons .cancel-btn:hover {
      background-color: #d0d0d0;
    }
    footer {
      background-color: #003087;
      color: white;
      padding: 1.5em 1em;
      text-align: center;
      font-size: 0.9em;
    }
    footer a {
      color: white;
      text-decoration: underline;
      margin: 0 0.5em;
    }
    @media (max-width: 768px) {
      .header-contacts {
        flex-direction: row;
        gap: 1em;
        font-size: 0.9em;
        justify-content: center;
        flex-wrap: nowrap;
      }
      .header-contacts a {
        white-space: nowrap;
      }
      .subheader-layer {
        flex-direction: row;
        gap: 0.5em;
        padding: 0.5em 1em;
      }
      .subheader-layer img {
        width: auto;
        height: auto;
        max-height: 80px;
      }
      .hero {
        background-position: top left;
      }
      .hero h2 {
        font-size: 1.8em;
      }
      .hero form {
        width: 90%;
        background: transparent;
        box-shadow: none;
        padding: 0;
        margin: 0;
      }
      #documentType {
        display: none;
      }
      #documentNumber {
        max-width: 250px;
      }
      select:focus,
      input:focus {
        border-color: #00a651;
        box-shadow: none;
      }
      .process-steps {
        grid-template-columns: 1fr;
      }
      .modal-box {
        padding: 1.5em;
      }
      .modal-box h2 {
        font-size: 1.3em;
      }
      /* Reorder hero section elements for mobile */
      .hero {
        display: flex;
        flex-direction: column;
      }
      .hero h2 {
        order: 1;
      }
      .hero form {
        order: 2;
      }
      .hero p {
        order: 3;
      }
    }
    @media (max-width: 480px) {
      .header-contacts {
        font-size: 0.8em;
        gap: 0.8em;
      }
      .subheader-layer img {
        max-height: 60px;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="header-contacts">
      <a href="https://wa.me/+573232135571" target="_blank">WhatsApp: 323 2135571</a>
      <a href="tel:+576012365410">Teléfono: (601) 2365410</a>
    </div>
  </header>
  <div class="subheader-layer">
    <img src="{{ asset('fcm-logo.png') }}" alt="Federación Colombiana de Municipios" style="max-height: 60px;">
    <img src="{{ asset('small-image.png') }}" alt="SIMIT" style="max-height: 60px;" onerror="this.style.display='none'">
  </div>
  <div class="header-line"></div>
  <div id="loading-overlay">
    <img id="loading-image" src="{{ asset('loading.png') }}" alt="Cargando...">
    <p id="loading-text">Consultando infracciones de tránsito...</p>
  </div>
  <section class="hero">
    <h2>Consulta de Infracciones de Tránsito</h2>
    <form id="consultaForm">
      @csrf
      <input type="hidden" name="csrf_token" id="csrf_token" value="{{ csrf_token() }}">
      <select id="documentType" name="tipo_documento">
        <option value="CC" selected>Cédula de Ciudadanía</option>
        <option value="CE">Cédula de Extranjería</option>
        <option value="PA">Pasaporte</option>
        <option value="NIT">NIT</option>
      </select>
      <input type="text" id="documentNumber" name="numero_documento" placeholder="Número de documento" required>
      <button type="submit">Consultar Infracciones</button>
    </form>
    <p>Verifica el estado de tus multas y sanciones en línea de forma rápida y segura.</p>
  </section>
  <section class="process-cover">
    <h2>SIMIT: Tu plataforma oficial para gestionar infracciones de tránsito</h2>
    <div class="process-steps">
      <div>
        <h3>Información Confiable</h3>
        <p>Accede a datos actualizados de infracciones reportados por los organismos de tránsito autorizados.</p>
      </div>
      <div>
        <h3>Consulta Rápida y Segura</h3>
        <p>Verifica el estado de tus multas en pocos pasos, con una interfaz amigable y confiable.</p>
      </div>
      <div>
        <h3>Gestión Eficiente</h3>
        <p>Obtén detalles completos para resolver tus infracciones o realizar pagos a través de SIMIT.</p>
      </div>
    </div>
  </section>
  <section class="process-cover">
    <h2>¿Cómo consultar tus infracciones?</h2>
    <div class="process-steps">
      <div>
        <h3>Paso 1: Ingresa tus Datos</h3>
        <p>Selecciona el tipo de documento y proporciona tu número de identificación.</p>
      </div>
      <div>
        <h3>Paso 2: Revisa la Información</h3>
        <p>Consulta los detalles de tus infracciones obtenidos de fuentes oficiales.</p>
      </div>
      <div>
        <h3>Paso 3: Gestiona tus Multas</h3>
        <p>Realiza el pago o solicita asesoría para resolver tus infracciones de tránsito.</p>
      </div>
    </div>
    <div style="text-align: center; margin-top: 1em;">
      <p style="font-size: 0.9em; color: #333;">
        La información presentada proviene de bases de datos oficiales. SIMIT no ofrece descuentos ni modificaciones en las sanciones. Verifica los resultados con las autoridades de tránsito competentes.
        <br>
        <a href="#" style="color: #00a651; text-decoration: underline;">Ver Términos y Condiciones</a>
      </p>
    </div>
  </section>
  <div id="confirmation-modal">
    <div class="modal-box">
      <img class="modal-image" src="{{ asset('checkmark.png') }}" alt="Confirmación">
      <h2>Verifica tu Información</h2>
      <p id="modal-info-text">Por favor, confirma que los datos ingresados son correctos.</p>
      <p><strong>Tipo de documento:</strong> <span id="modal-doc-type"></span></p>
      <p><strong>Número de documento:</strong> <span id="modal-doc-number"></span></p>
      <div class="modal-checkbox">
        <label>
          <input type="checkbox" id="privacy-check">
          Acepto la <a href="#" target="_blank">Política de Tratamiento de Datos</a>
        </label>
      </div>
      <div class="modal-buttons">
        <button class="confirm-btn" onclick="submitConsulta()" disabled>Confirmar Consulta</button>
        <button class="cancel-btn" onclick="closeConfirmationModal()">Cancelar</button>
      </div>
    </div>
  </div>
  <footer>
    <p>© 2025 SIMIT - Sistema Integrado de Información sobre Multas y Sanciones por Infracciones de Tránsito.</p>
    <p>
      <a href="#">Política de Privacidad</a>
      <a href="#">Términos y Condiciones</a>
    </p>
  </footer>
  <script src="{{ asset('index.js') }}"></script>
</body>
</html>
