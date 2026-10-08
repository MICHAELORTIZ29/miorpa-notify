@extends('business.layout')
@section('title','Comparación de movimientos | MIORPA NOTIFY')
@section('business-content')
<h1>Comparación de movimientos · {{ ucfirst($reconciliation->provider_code) }}</h1>
<p><a href="{{ route('business.reliability.index') }}">Volver a control y revisión</a></p>
<p>Resultados de comparación, pendientes de verificación. Un mismo monto y una hora cercana no garantizan que sea el mismo pago. No se modificaron pagos.</p>
@php $labels=['missing'=>'Sin coincidencia','candidate'=>'Posible coincidencia','ambiguous'=>'Coincidencia ambigua','duplicate_reference'=>'Referencia repetida en el CSV']; $result=$reconciliation->result; @endphp
@foreach($result['summary'] as $state=>$count)<p><strong>{{ $labels[$state] }}: {{ $count }}</strong></p>@endforeach
<div style="overflow-x:auto"><table style="width:100%;text-align:left"><thead><tr><th>Fecha del movimiento</th><th>Monto</th><th>Nombre / referencia</th><th>Resultado</th><th>Pagos candidatos</th></tr></thead><tbody>
@foreach($result['rows'] as $row)<tr><td>{{ $row['date'] }}</td><td>S/ {{ $row['amount'] }}</td><td>{{ $row['name'] }}<br>{{ $row['reference'] }}</td><td>{{ $labels[$row['status']] }}</td><td>
@foreach($row['candidates'] as $candidate)<p><a href="{{ route('business.payments.show',$candidate['id']) }}">{{ $candidate['name']?:'No identificado' }} · {{ $candidate['date'] }}</a></p>@endforeach
</td></tr>@endforeach
</tbody></table></div>
@endsection
