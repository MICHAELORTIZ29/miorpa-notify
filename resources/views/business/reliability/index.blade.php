@extends('business.layout')
@section('title','Control y revisión | MIORPA NOTIFY')
@section('business-content')
<h1>Control y revisión</h1>
<p>Revisa mensajes originales, incidentes del lector y diferencias con los movimientos de tus billeteras.</p>
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<h2>Notificaciones de billetera</h2>
<form method="get"><label for="filter">Mostrar</label> <select id="filter" name="filter" onchange="this.form.submit()">
@foreach(['pending'=>'Pendientes de revisión','all'=>'Todas','parsed'=>'Pago identificado','grouped'=>'Resúmenes de Android','ignored'=>'Revisadas sin pago'] as $value=>$label)
<option value="{{ $value }}" @selected($filter===$value)>{{ $label }}</option>@endforeach
</select><noscript><button>Buscar</button></noscript></form>
<p>Un mensaje sin identificar puede ser una promoción, una salida de dinero o un pago con formato nuevo. No se convierte automáticamente en un pago.</p>
@forelse($captures as $capture)
@php $raw=$capture->raw_payload; $reasons=['group_summary'=>'Resumen de Android: no es una transacción individual','outgoing_or_not_completed'=>'Salida de dinero, solicitud o operación no completada','ambiguous_amount'=>'Contiene varios montos: requiere revisión','no_incoming_signal'=>'No indica claramente un pago recibido','missing_amount'=>'No se pudo identificar el monto','unknown_format'=>'Formato todavía no reconocido','parser_exception'=>'Error al interpretar el formato']; @endphp
<article class="card" style="padding:18px;margin:14px 0;overflow-wrap:anywhere">
<strong>{{ $capture->device?->name }} · {{ $capture->occurred_at->timezone(auth()->user()->business->timezone ?: 'America/Lima')->format('d/m/Y H:i:s') }}</strong>
<p>{{ $capture->package_name }} · {{ ['parsed'=>'Pago identificado','unrecognized'=>'Sin identificar','parse_error'=>'Error de formato','grouped'=>'Resumen de Android','captured'=>'Pendiente de interpretar'][$capture->state]??$capture->state }} · {{ $reasons[$capture->reason]??$capture->reason }}</p>
@if($capture->payment)<p>Pago registrado: <a href="{{ route('business.payments.show',$capture->payment) }}">{{ $capture->payment->public_id }}</a></p>@endif
@if($capture->retry_version>$capture->applied_retry_version)<p>Reprocesamiento pendiente de conexión del teléfono.</p>@endif
@if($capture->reviewed_at)<p>Revisada sin registrar un pago.</p>@endif
<details><summary>Ver texto original</summary>
@foreach(['title'=>'Título','text'=>'Texto','big_text'=>'Texto ampliado','sub_text'=>'Subtítulo','summary_text'=>'Resumen','info_text'=>'Información','ticker_text'=>'Texto breve'] as $key=>$label)
@if(!empty($raw[$key]))<p><strong>{{ $label }}</strong></p><pre style="white-space:pre-wrap;font:inherit">{{ $raw[$key] }}</pre>@endif
@endforeach
@foreach($raw['text_lines']??[] as $line)<pre style="white-space:pre-wrap;font:inherit">{{ $line }}</pre>@endforeach
</details>
@if(in_array($capture->state,['unrecognized','parse_error']))
<form method="post" action="{{ route('business.reliability.review',$capture) }}" style="margin-top:12px">@csrf
<button class="button button-secondary" name="action" value="retry">Solicitar reprocesamiento</button>
<button class="button button-secondary" name="action" value="ignore" onclick="return confirm('¿Revisaste el mensaje y confirmas que no debe registrarse como pago?')">Revisada sin pago</button>
</form>@endif
</article>
@empty<p>No hay notificaciones en este filtro. La sincronización requiere APK 1.5 y conexión.</p>@endforelse
{{ $captures->links() }}
<h2>Historial de incidentes</h2>
<p>Se comprueba cada minuto. Sin conexión, el servidor detecta la ausencia del teléfono; los detalles llegan cuando vuelve a conectarse.</p>
<div style="overflow-x:auto"><table style="width:100%;text-align:left"><thead><tr><th>Teléfono</th><th>Problema</th><th>Detectado</th><th>Recuperación</th></tr></thead><tbody>
@forelse($incidents as $incident)<tr><td>{{ $incident->device?->name }}</td><td>{{ $incident->message }}</td><td>{{ $incident->opened_at->timezone('America/Lima')->format('d/m/Y H:i:s') }}</td><td>{{ $incident->resolved_at?->timezone('America/Lima')->format('d/m/Y H:i:s')??'Abierto' }}</td></tr>
@empty<tr><td colspan="4">Sin incidentes registrados.</td></tr>@endforelse
</tbody></table></div>
<h2>Comparar movimientos de la billetera</h2>
<p>Sube un CSV UTF-8 de ingresos en soles con estas columnas: <code>fecha,monto,referencia,nombre</code>. Fecha local: <code>2026-10-08 14:30:00</code>; monto: <code>25.50</code>. Máximo 500 filas. La referencia puede quedar vacía.</p>
<p>Las coincidencias son sugerencias para revisión; no se crean ni confirman pagos con el CSV.</p>
<form method="post" enctype="multipart/form-data" action="{{ route('business.reliability.reconcile') }}">@csrf
<label>Billetera receptora <select name="provider"><option value="yape">Yape</option><option value="plin">Plin</option></select></label>
<label>Archivo <input type="file" name="csv" accept=".csv,text/csv" required></label>
<button class="button button-primary">Comparar movimientos</button></form>
@foreach($reconciliations as $report)<p><a href="{{ route('business.reliability.reconciliation',$report) }}">{{ $report->provider_code }} · {{ $report->total }} movimientos · {{ $report->created_at->timezone('America/Lima')->format('d/m/Y H:i') }}</a></p>@endforeach
@endsection
