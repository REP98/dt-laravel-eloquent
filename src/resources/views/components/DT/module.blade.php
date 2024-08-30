<script>
    const table = document.querySelector("#datatable-{{ $uniqueId }}");
    if ('DT' in window) {
        window.RDT["datatable-{{ $uniqueId }}"] = DTLaravel(table, @json($data), @json($options));
    } else {
        window.RDT = {
            "datatable-{{ $uniqueId }}": DTLaravel(table, @json($data), @json($options))
        };
    }
</script>
