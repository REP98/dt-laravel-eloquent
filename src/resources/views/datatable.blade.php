{{-- DataTable --}}
<x-dt::table :uniqueId="$uniqueId" :exportname="$exname"/>

{{-- Script --}}
<x-dt::module :data="$data" :options="$options" :uniqueId="$uniqueId"/>

