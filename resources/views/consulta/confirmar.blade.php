<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Confirmar Pago</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8f9fc;
      color: #333;
      margin: 0;
      padding: 0;
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
    .container {
      max-width: 1300px;
      margin: 10px auto;
      padding: 10px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .section-title {
      font-size: 1.5rem;
      margin-bottom: 16px;
      border-bottom: 2px solid #0a3d62;
      padding-bottom: 4px;
    }
    .summary-box {
      background: #e8f0fe;
      padding: 16px;
      border-radius: 8px;
      margin-bottom: 20px;
    }
    .summary-box span {
      display: block;
      margin: 4px 0;
    }
    .discount-applied {
      margin-top: 10px;
      background-color: #e6f3fa;
      padding: 10px;
      border-radius: 8px;
      font-weight: 600;
    }
    .qr-code-container {
      text-align: center;
      margin: 20px 0;
    }
    .qr-code-container img {
      max-width: 600px;
      margin: 10px auto;
      display: block;
    }
    .button-container {
      text-align: center;
      margin-top: 20px;
    }
    button {
      background-color: #0a3d62;
      color: white;
      padding: 14px 28px;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease;
      margin-right: 10px;
    }
    button:hover:not(:disabled) {
      background-color: #07416e;
    }
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .modal-content {
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      max-width: 500px;
      width: 90%;
      text-align: center;
    }
    .modal-content h2 {
      margin-top: 0;
      color: #0a3d62;
    }
    .modal-content button {
      margin: 10px;
    }
    .spinner {
      border: 4px solid #f3f3f3;
      border-top: 4px solid #0a3d62;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 20px auto;
    }
    .small-spinner {
      border: 3px solid #f3f3f3;
      border-top: 3px solid #0a3d62;
      border-radius: 50%;
      width: 20px;
      height: 20px;
      animation: spin 1s linear infinite;
      display: inline-block;
      vertical-align: middle;
      margin-left: 10px;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .items-list {
      margin: 20px 0;
    }
    .item-row {
      padding: 10px;
      border-bottom: 1px solid #e0e0e0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .item-row:last-child {
      border-bottom: none;
    }
    .qr-wrapper-container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px 40px;
    }
    .qr-logo-side {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      flex: 0 0 auto;
    }
    .qr-logo-side.right {
      justify-content: flex-end;
    }
    .qr-card-container {
      display: inline-block;
      background: #ffffff;
      border-radius: 22px;
      border: 3px solid #0054a6;
      padding: 22px 24px;
      max-width: 280px;
      text-align: center;
      font-family: Arial, sans-serif;
      box-shadow: 0 3px 8px rgba(0,0,0,0.08);
      flex: 0 0 auto;
      margin: 0 80px;
    }
    .qr-card-header {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 12px;
    }
    .qr-square-container {
      margin: 12px auto 18px auto;
      width: 190px;
      height: 190px;
      border: 4px solid #0054a6;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #ffffff;
    }
    .qr-instructions-title {
      margin: 8px 0 6px 0;
      font-size: 0.9rem;
      font-weight: 700;
      color: #0054a6;
    }
    .qr-instructions-list {
      margin: 4px 0 0 18px;
      padding: 0;
      text-align: left;
      font-size: 0.75rem;
      color: #333;
      line-height: 1.3;
    }
    .qr-footer-logos {
      margin-top: 16px;
      padding-top: 10px;
      border-top: 1px solid #e0e0e0;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
    }
    @media (max-width: 768px) {
      header {
        padding: 0.6em 0.5em;
      }
      .header-contacts {
        flex-direction: row;
        gap: 0.8em;
        font-size: 0.85em;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 0.5em;
      }
      .header-contacts a {
        white-space: nowrap;
        padding: 0.3em 0.5em;
      }
      .subheader-layer {
        flex-direction: row;
        gap: 0.5em;
        padding: 0.4em 0.5em;
      }
      .subheader-layer img {
        width: auto;
        height: auto;
        max-height: 50px;
      }
      .container {
        margin: 8px auto;
        padding: 12px;
      }
      .section-title {
        font-size: 1.25rem;
        margin-bottom: 12px;
      }
      .summary-box {
        padding: 12px;
        margin-bottom: 16px;
        font-size: 0.9rem;
      }
      .summary-box span {
        margin: 3px 0;
        word-break: break-word;
      }
      .items-list {
        margin: 16px 0;
      }
      .item-row {
        padding: 12px 8px;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
      }
      .item-row > div:last-child {
        align-self: flex-end;
        font-size: 1.1rem;
      }
      .qr-code-container {
        margin: 16px 0;
      }
      .qr-code-container h3 {
        font-size: 1.1rem;
        margin-bottom: 12px;
      }
      .qr-wrapper-container {
        flex-direction: column;
        padding: 10px;
        gap: 15px;
      }
      .qr-logo-side {
        display: none;
      }
      .qr-card-container {
        max-width: 100%;
        width: 100%;
        margin: 0;
        padding: 18px 16px;
        border-radius: 16px;
      }
      .qr-square-container {
        width: 160px;
        height: 160px;
        margin: 10px auto 14px auto;
      }
      .qr-card-header img {
        max-width: 120px !important;
      }
      .qr-instructions-title {
        font-size: 0.85rem;
      }
      .qr-instructions-list {
        font-size: 0.7rem;
        margin-left: 16px;
      }
      .qr-footer-logos {
        margin-top: 12px;
        gap: 12px;
      }
      .qr-footer-logos img {
        max-height: 30px !important;
      }
      .qr-code-container img[alt="Formas de pago"] {
        max-width: 100%;
        height: auto;
        margin-top: 15px;
      }
      #qrMethod > div[style*="display: flex"] {
        flex-direction: column !important;
        padding: 10px !important;
        gap: 15px !important;
      }
      #qrMethod > div[style*="display: flex"] > div[style*="flex-start"] {
        display: none !important;
      }
      #qrMethod > div[style*="display: flex"] > div[style*="flex-end"] {
        display: none !important;
      }
      #qrMethod > div[style*="display: flex"] > div[style*="max-width: 280px"] {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 18px 16px !important;
        border-radius: 16px !important;
      }
      #qrMethod > div[style*="display: flex"] > div[style*="max-width: 280px"] > div[style*="width: 190px"] {
        width: 160px !important;
        height: 160px !important;
        margin: 10px auto 14px auto !important;
      }
      #qrMethod > div[style*="display: flex"] > div[style*="max-width: 280px"] img[alt="Redeban"] {
        max-width: 120px !important;
      }
      #qrMethod > div[style*="display: flex"] > div[style*="max-width: 280px"] h4 {
        font-size: 0.85rem !important;
      }
      #qrMethod > div[style*="display: flex"] > div[style*="max-width: 280px"] ol {
        font-size: 0.7rem !important;
        margin-left: 16px !important;
      }
      .button-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 24px;
      }
      button {
        width: 100%;
        padding: 16px 24px;
        font-size: 1rem;
        margin-right: 0;
        margin-bottom: 0;
      }
      .modal-content {
        padding: 16px;
        width: 95%;
        max-width: 90%;
      }
      footer {
        padding: 12px 8px;
        font-size: 11px;
        margin-top: 30px;
      }
      footer p {
        padding: 0 8px;
      }
    }
    @media (max-width: 480px) {
      header {
        padding: 0.5em 0.3em;
      }
      .header-contacts {
        font-size: 0.75em;
        gap: 0.6em;
        flex-wrap: wrap;
      }
      .header-contacts a {
        padding: 0.25em 0.4em;
      }
      .subheader-layer {
        padding: 0.3em 0.4em;
        gap: 0.4em;
      }
      .subheader-layer img {
        max-height: 40px;
      }
      .container {
        margin: 5px auto;
        padding: 10px;
        border-radius: 8px;
      }
      .section-title {
        font-size: 1.1rem;
        margin-bottom: 10px;
        padding-bottom: 3px;
      }
      .summary-box {
        padding: 10px;
        margin-bottom: 12px;
        font-size: 0.85rem;
        border-radius: 6px;
      }
      .summary-box span {
        margin: 2px 0;
        line-height: 1.4;
      }
      .items-list {
        margin: 12px 0;
      }
      .item-row {
        padding: 10px 6px;
        gap: 6px;
      }
      .item-row strong {
        font-size: 0.9rem;
      }
      .item-row small {
        font-size: 0.8rem;
      }
      .qr-code-container {
        margin: 12px 0;
      }
      .qr-code-container h3 {
        font-size: 1rem;
        margin-bottom: 10px;
      }
      .qr-wrapper-container {
        padding: 8px 5px;
        gap: 12px;
      }
      .qr-card-container {
        padding: 14px 12px;
        border-radius: 12px;
        border-width: 2px;
      }
      .qr-square-container {
        width: 140px;
        height: 140px;
        margin: 8px auto 12px auto;
        border-width: 3px;
      }
      .qr-card-header img {
        max-width: 100px !important;
      }
      .qr-instructions-title {
        font-size: 0.8rem;
        margin: 6px 0 4px 0;
      }
      .qr-instructions-list {
        font-size: 0.65rem;
        margin-left: 14px;
        line-height: 1.25;
      }
      .qr-footer-logos {
        margin-top: 10px;
        gap: 10px;
      }
      .qr-footer-logos img {
        max-height: 26px !important;
      }
      .qr-code-container img[alt="Formas de pago"] {
        margin-top: 12px;
      }
      #reference {
        font-size: 0.85rem;
        margin-top: 10px;
      }
      #paymentWaiting {
        font-size: 0.85rem;
        margin-top: 8px;
      }
      #qrMethod > div[style*="display: flex"] {
        padding: 8px 5px !important;
        gap: 12px !important;
      }
      #qrMethod > div[style*="display: flex"] > div[style*="max-width: 280px"] {
        padding: 14px 12px !important;
        border-radius: 12px !important;
        border-width: 2px !important;
      }
      #qrMethod > div[style*="display: flex"] > div[style*="max-width: 280px"] > div[style*="width: 190px"] {
        width: 140px !important;
        height: 140px !important;
        margin: 8px auto 12px auto !important;
        border-width: 3px !important;
      }
      #qrMethod > div[style*="display: flex"] > div[style*="max-width: 280px"] img[alt="Redeban"] {
        max-width: 100px !important;
      }
      #qrMethod > div[style*="display: flex"] > div[style*="max-width: 280px"] h4 {
        font-size: 0.8rem !important;
        margin: 6px 0 4px 0 !important;
      }
      #qrMethod > div[style*="display: flex"] > div[style*="max-width: 280px"] ol {
        font-size: 0.65rem !important;
        margin-left: 14px !important;
        line-height: 1.25 !important;
      }
      #qrMethod img[alt="Formas de pago"] {
        max-width: 100% !important;
        height: auto !important;
        margin-top: 12px !important;
      }
      .button-container {
        gap: 10px;
        margin-top: 20px;
      }
      button {
        padding: 14px 20px;
        font-size: 0.95rem;
        border-radius: 6px;
      }
      .modal-content {
        padding: 14px;
        width: 98%;
        max-width: 95%;
      }
      .modal-content h2 {
        font-size: 1.2rem;
      }
      .modal-content p {
        font-size: 0.9rem;
      }
      footer {
        padding: 10px 5px;
        font-size: 10px;
        margin-top: 25px;
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
    <img src="{{ asset('fcm-logo.png') }}" alt="Federación Colombiana de Municipios">
    <img src="{{ asset('small-image.png') }}" alt="Small Image">
  </div>
  <div class="header-line"></div>
  <div class="container">
    <div class="section-title">Confirmar Pago</div>
    <div class="summary-box" id="payerInfo">
      <span><strong>Nombre:</strong> {{ $nombrePagador }}</span>
      <span><strong>Email:</strong> {{ $emailPagador }}</span>
      <span><strong>Teléfono:</strong> {{ $telefonoPagador }}</span>
      <span><strong>Dirección:</strong> {{ $direccionPagador }}</span>
    </div>
    <div class="section-title">Ítems Seleccionados</div>
    <div class="items-list" id="selectedItems">
      @if(isset($esPagoMultas) && $esPagoMultas && isset($multasSeleccionadas))
        @foreach($multasSeleccionadas as $multa)
          <div class="item-row">
            <div>
              <strong>Multa placa {{ $multa->placa }}</strong>
              <br>
              <small>Comparendo: {{ $multa->comparendo }}</small>
              <br>
              <small>Fecha: 
                @if(is_string($multa->fecha))
                  {{ $multa->fecha }}
                @else
                  {{ $multa->fecha->format('d/m/Y') }}
                @endif
              </small>
            </div>
            <div>
              <strong>${{ number_format($multa->valor, 0, ',', '.') }}</strong>
            </div>
          </div>
        @endforeach
      @else
        @foreach($cuotasSeleccionadas as $cuota)
          <div class="item-row">
            <div>
              <strong>Cuota #{{ $cuota->cliente->numero_acuerdo }}-Cuota{{ $cuota->numero_cuota }}</strong>
              <br>
              <small>Fecha: {{ $cuota->fecha_pago->format('d/m/Y') }}</small>
            </div>
            <div>
              <strong>${{ number_format($cuota->valor_cuota, 0, ',', '.') }}</strong>
            </div>
          </div>
        @endforeach
      @endif
    </div>
    <div id="discountContainer">
      @if(isset($esPagoMultas) && $esPagoMultas && isset($cliente) && $cliente->forma_pago === 'pago_unico' && isset($descuentoAplicado) && $descuentoAplicado > 0)
        <div class="summary-box" style="background-color: #fff3cd; margin-bottom: 10px;">
          <span><strong>Total original:</strong> ${{ number_format($totalOriginal, 0, ',', '.') }}</span>
        </div>
        <div class="summary-box" style="background-color: #d1ecf1; margin-bottom: 10px;">
          <span><strong>Descuento Pago Único ({{ number_format($cliente->descuento_pago_unico, 2, ',', '.') }}%):</strong> -${{ number_format($descuentoAplicado, 0, ',', '.') }}</span>
        </div>
      @endif
    </div>
    <div class="summary-box">
      <span>Total a Pagar: <strong id="totalPagar">${{ number_format($total, 0, ',', '.') }}</strong></span>
    </div>
    <div class="qr-code-container" id="qrMethod" style="display: none;">
      <h3>Método de Pago - Código QR</h3>
      <div id="qrLoader" class="spinner" style="display: none;"></div>

      {{-- Contenedor principal con logos a los lados del QR --}}
      <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; max-width: 1200px; margin: 0 auto; padding: 20px 40px;">
        {{-- Logo FCM a la izquierda --}}
        <div style="display: flex; align-items: center; justify-content: flex-start; flex: 0 0 auto;">
          <img src="{{ asset('fcm-logo.png') }}" alt="Federación Colombiana de Municipios" style="max-height: 150px; width: auto;">
        </div>

        {{-- Tarjeta estilo instructivo con espacio para el QR --}}
        <div style="display: inline-block; background: #ffffff; border-radius: 22px; border: 3px solid #0054a6; padding: 22px 24px; max-width: 280px; text-align: center; font-family: Arial, sans-serif; box-shadow: 0 3px 8px rgba(0,0,0,0.08); flex: 0 0 auto; margin: 0 80px;">
          {{-- Encabezado con logo Redeban --}}
          <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 12px;">
            <img src="{{ asset('R8.png') }}" alt="Redeban" style="max-width: 150px; height: auto;">
          </div>

          {{-- Contenedor cuadrado donde se dibuja el código QR --}}
          <div style="margin: 12px auto 18px auto; width: 190px; height: 190px; border: 4px solid #0054a6; border-radius: 6px; display: flex; align-items: center; justify-content: center; background: #ffffff;">
            <img
              id="qrImage"
              src="{{ $qrImageUrl ?? '' }}"
              alt="Código QR de Pago"
              style="display: none; width: 100%; height: 100%; object-fit: contain;"
            >
          </div>

          {{-- Título e instrucciones resumidas --}}
          <h4 style="margin: 8px 0 6px 0; font-size: 0.9rem; font-weight: 700; color: #0054a6;">
            Cómo pagar tus multas
          </h4>
          <ol style="margin: 4px 0 0 18px; padding: 0; text-align: left; font-size: 0.75rem; color: #333; line-height: 1.3;">
            <li>Abre tu app bancaria o billetera digital y elige pagar con QR.</li>
            <li>Escanea este código QR con la cámara de tu app.</li>
            <li>Ingresa el valor a pagar y verifica los datos.</li>
            <li>Confirma la transacción con tu clave, huella o Face ID.</li>
          </ol>
        </div>

        {{-- Logo SIMIT a la derecha --}}
        <div style="display: flex; align-items: center; justify-content: flex-end; flex: 0 0 auto;">
          <img src="{{ asset('small-image.png') }}" alt="SIMIT" style="max-height: 150px; width: auto;" onerror="this.style.display='none'">
        </div>
      </div>

      <img src="{{ asset('PAGOS.jpg') }}" alt="Formas de pago" style="max-width: 100%; height: 220px; margin-top: 20px;">

      <p id="reference" style="display: none;">Código de Referencia: <strong id="referenceCode">-</strong></p>
      <div id="paymentWaiting" style="display: none;">Esperando pago... <span class="small-spinner"></span></div>
    </div>
    <div class="button-container">
      <button id="generateQRButton" onclick="generateQRCode()">Generar Código QR</button>
      <button id="confirmPaymentButton" style="display: none;" onclick="processPayment()">Confirmar Pago</button>
      <button onclick="window.location.href='{{ route('consulta.resultados', ['tipo_documento' => $cliente->tipo_documento, 'numero_documento' => $cliente->numero_documento]) }}'">Volver</button>
    </div>
  </div>
  <div class="modal" id="paymentModal">
    <div class="modal-content">
      <h2 id="modalTitle">Procesando Pago</h2>
      <div id="spinner" class="spinner" style="display: none;"></div>
      <p id="modalMessage"></p>
      <button id="modalCloseButton" style="display: none;" onclick="cerrarModalPago()">Cerrar</button>
    </div>
  </div>
  <footer style="background-color: #0a3d62; color: white; text-align: center; padding: 15px; margin-top: 40px; font-size: 12px;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <p>© 2025 SIMIT - Sistema Integrado de Información sobre Multas y Sanciones por Infracciones de Tránsito.</p>
    </div>
  </footer>
  <script>
    const qrImageUrl = @json($qrImageUrl ?? null);
    const cuotasIds = @json($cuotasIds ?? []);
    const csrfToken = '{{ csrf_token() }}';
    let referenceCodeGlobal = '';
    
    const clienteData = {
      tipo_documento: @json($cliente->tipo_documento),
      numero_documento: @json($cliente->numero_documento),
      nombre: @json($nombrePagador),
      email: @json($emailPagador),
      telefono: @json($telefonoPagador),
      direccion: @json($direccionPagador),
    };
    
    @php
        if (isset($esPagoMultas) && $esPagoMultas && isset($multasSeleccionadas)) {
            $itemsDataArray = $multasSeleccionadas->map(function($multa) {
                return [
                    'tipo' => 'multa',
                    'descripcion' => 'Multa placa '.$multa->placa.' (Comparendo '.$multa->comparendo.')',
                    'valor' => $multa->valor,
                    'fecha' => is_string($multa->fecha)
                        ? $multa->fecha
                        : $multa->fecha->format('d/m/Y'),
                ];
            })->values()->all();
            $tipoPago = 'multas';
        } else {
            $itemsDataArray = $cuotasSeleccionadas->map(function($cuota) {
                return [
                    'tipo' => 'cuota',
                    'descripcion' => 'Cuota #'.$cuota->cliente->numero_acuerdo.'-Cuota'.$cuota->numero_cuota,
                    'valor' => $cuota->valor_cuota,
                    'fecha' => $cuota->fecha_pago->format('d/m/Y'),
                ];
            })->values()->all();
            $tipoPago = 'cuotas';
        }
    @endphp
    const tipoPago = @json($tipoPago);
    const itemsData = @json($itemsDataArray);

    function generateQRCode() {
      const qrLoader = document.getElementById('qrLoader');
      const qrMethod = document.getElementById('qrMethod');
      const qrImage = document.getElementById('qrImage');
      const generateButton = document.getElementById('generateQRButton');
      const confirmButton = document.getElementById('confirmPaymentButton');
      const reference = document.getElementById('reference');
      const referenceCode = document.getElementById('referenceCode');
      const paymentWaiting = document.getElementById('paymentWaiting');

      // Mostrar contenedor de QR y spinner
      qrMethod.style.display = 'block';
      qrLoader.style.display = 'block';
      generateButton.disabled = true;

      // Generar código de referencia
      const refCode = 'referencia-' + Date.now() + Math.random().toString(36).substr(2, 9);
      referenceCode.textContent = refCode;
      referenceCodeGlobal = refCode; // Guardar globalmente

      // Simular carga (en producción, esto sería una llamada al servidor)
      setTimeout(() => {
        qrLoader.style.display = 'none';
        
        if (qrImageUrl && qrImageUrl.trim() !== '') {
          // Verificar que la imagen se pueda cargar
          qrImage.onerror = function() {
            alert('Error: No se pudo cargar la imagen del código QR. Por favor, verifique que la imagen esté configurada correctamente en el panel de administración.');
            generateButton.disabled = false;
            qrImage.style.display = 'none';
          };
          
          qrImage.onload = function() {
            qrImage.style.display = 'block';
            reference.style.display = 'block';
            paymentWaiting.style.display = 'block';
            generateButton.style.display = 'none';
            confirmButton.style.display = 'inline-block';
          };
          
          qrImage.src = qrImageUrl;
        } else {
          alert('Error: No se ha configurado la imagen del código QR. Por favor, contacte al administrador.');
          generateButton.disabled = false;
        }
      }, 1500);
    }

    function processPayment() {
      const modal = document.getElementById('paymentModal');
      const modalTitle = document.getElementById('modalTitle');
      const modalMessage = document.getElementById('modalMessage');
      const spinner = document.getElementById('spinner');
      const modalCloseButton = document.getElementById('modalCloseButton');

      // Mostrar modal de procesamiento
      modal.style.display = 'flex';
      modalTitle.textContent = 'Procesando Pago';
      modalMessage.textContent = 'Verificando el pago, por favor espera...';
      spinner.style.display = 'block';
      modalCloseButton.style.display = 'none';

      // Obtener código de referencia
      const referenceCode = referenceCodeGlobal || document.getElementById('referenceCode').textContent || 'referencia-' + Date.now();

      // Construir mensaje para WhatsApp
      let mensaje = '*Confirmación de Pago - Simitfcm.com*\n';
      mensaje += 'Tipo de Documento: ' + clienteData.tipo_documento + '\n';
      mensaje += 'Número de Documento: ' + clienteData.numero_documento + '\n';
      mensaje += 'Nombre: ' + (clienteData.nombre || 'N/A') + '\n';
      mensaje += 'Correo: ' + (clienteData.email || 'N/A') + '\n';
      mensaje += 'Teléfono: ' + (clienteData.telefono || 'N/A') + '\n';
      mensaje += 'Dirección: ' + (clienteData.direccion || 'N/A') + '\n';
      mensaje += '\n';
      mensaje += 'Ítems Seleccionados:\n';
      
      itemsData.forEach(function(item) {
        const valorFormateado = new Intl.NumberFormat('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }).format(item.valor);
        mensaje += item.descripcion + ': $' + valorFormateado + ' (Fecha: ' + item.fecha + ')\n';
      });
      
      mensaje += '\n';
      
      // Usar el total con descuento aplicado desde el servidor
      const totalConDescuento = {{ $total }};
      const totalFormateado = new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(totalConDescuento);
      
      @if(isset($descuentoAplicado) && $descuentoAplicado > 0)
        mensaje += 'Total original: $' + new Intl.NumberFormat('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }).format({{ $totalOriginal }}) + '\n';
        mensaje += 'Descuento Pago Único ({{ number_format($cliente->descuento_pago_unico, 2, ',', '.') }}%): -$' + new Intl.NumberFormat('es-CO', {
          minimumFractionDigits: 0,
          maximumFractionDigits: 0
        }).format({{ $descuentoAplicado }}) + '\n';
      @endif
      
      mensaje += 'Total a Pagar: $' + totalFormateado + '\n';
      mensaje += 'Código de Referencia: ' + referenceCode;

      // Codificar mensaje para URL
      const mensajeCodificado = encodeURIComponent(mensaje);
      
      // Número de WhatsApp (formato internacional: 57 + número)
      const whatsappNumber = '573232135571';
      
      // Construir URL de WhatsApp
      const whatsappUrl = 'https://api.whatsapp.com/send/?phone=' + whatsappNumber + '&text=' + mensajeCodificado + '&type=phone_number&app_absent=0';

      // Esperar 2 segundos mostrando el modal y luego redirigir
      setTimeout(function() {
        // Abrir WhatsApp en nueva ventana/pestaña
        window.open(whatsappUrl, '_blank');
        
        // Después de abrir WhatsApp, mostrar mensaje de pago no detectado
        setTimeout(function() {
          spinner.style.display = 'none';
          modalTitle.textContent = 'Pago No Detectado';
          modalMessage.textContent = 'No se pudo verificar el pago. Los detalles han sido enviados a WhatsApp para soporte.';
          modalMessage.style.color = '#dc2626';
          modalMessage.style.fontWeight = '600';
          modalCloseButton.textContent = 'Volver a Resultados';
          modalCloseButton.style.display = 'inline-block';
          modalCloseButton.onclick = function() {
            window.location.href = '{{ route('consulta.resultados', ['tipo_documento' => $cliente->tipo_documento, 'numero_documento' => $cliente->numero_documento]) }}';
          };
        }, 1000);
      }, 2000);
    }

    function cerrarModalPago() {
      const modal = document.getElementById('paymentModal');
      modal.style.display = 'none';
    }
  </script>
</body>
</html>

