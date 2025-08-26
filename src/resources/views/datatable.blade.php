<div class="rdt-container">
<table data-export-name="{{ $exportname }}" id="{!! $uniqueId !!}" class="table table-striped table-hover"></table>
<script>
    (() => {
        let dt_init = () => {
            if ('DT' in window) {
                window.RDT["{!! $uniqueId !!}"] = DTLaravel("#{!! $uniqueId !!}", @json($data), @json($options));
            } else {
                window.RDT = {
                    "{!! $uniqueId !!}": DTLaravel("#{!! $uniqueId !!}", @json($data), @json($options))
                };
            }
        }

        if (document.readyState !== 'loading') {
            dt_init();
        } else {
            document.addEventListener("DOMContentLoaded", dt_init);
        }
    })();
</script>
</div>
