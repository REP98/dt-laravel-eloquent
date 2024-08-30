<?php
namespace DTLaravelEloquent;

use DTLaravelEloquent\RDTOptions;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Clase Matriz del Datatable
 */
class RDT
{
    /**
     * Datos del Datatable
     *
     * @var \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection
     */
    protected EloquentCollection|Collection|array $data;
    /**
     * Opciones del Datatable
     *
     * @var \DTLaravelEloquent\RDTOptions
     */
    protected RDTOptions $options;
    /**
     * Activa o desactiva los Logs
     *
     * @var bool
     * @default false
     */
    protected bool $logEnabled = false;
    /**
     * Lista de elementos a excluir de los datos
     *
     * @var array
     */
    protected array $excludedFields = [];
    /**
     * Identificador Único para cada Tabla
     *
     * @var string
     */
    protected $uniqueId;

    /**
     * Metódo Estatico que permite contruir un DataTable
     *
     * @param   \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection|array  $data  Datos para trabajar
     *
     * @return  \DTLaravelEloquent\RDT
     */
    public static function make(Collection|EloquentCollection|array $data): self
    {
        return new self($data);
    }
    /**
     * Incializa el Datatable con una colección
     *
     * @param   \Illuminate\Support\Collection  $data  Datos
     *
     * @return  \DTLaravelEloquent\RDT
     */
    public static function collection(Collection $data): self
    {
        return new self($data);
    }
    /**
     * Inicializa el Datatable con una colección Eloquent de Laravel
     *
     * @param   \Illuminate\Database\Eloquent\Collection  $data  Datos
     *
     * @return  \DTLaravelEloquent\RDT
     */
    public static function DB(EloquentCollection $data): self
    {
        return new self($data);
    }
    /**
     * Constructor de Datos
     *
     * @param   \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection|array  $data  Datos
     * @throw   \InvalidArgumentException
     */
    public function __construct(Collection|EloquentCollection|array $data)
    {
        if ($data instanceof Collection || $data instanceof EloquentCollection) {
            $this->data = $data;
        } else if(is_array($data)) {
            $this->data = collect($data);
        } else {
            if ($this->logEnabled) {
                // Log the data and options
                Log::error('RDT Error: Data must be an array, Collection, or EloquentCollection.', $data);
            }
            throw new \InvalidArgumentException('Data must be an array, Collection, or EloquentCollection.');
        }
        $this->uniqueId = Str::uuid()->toString();
        $this->setOption(new RDTOptions());
    }
    /**
     * Establece las opciones del Datatable
     *
     * @param   \DTLaravelEloquent\RDTOptions  $options  Lista de Opcioens
     *
     * @return  \DTLaravelEloquent\RDT
     */
    public function setOption(RDTOptions $options): self
    {
        $this->options = $options;
        return $this;
    }
    /**
     * Obtiene las opciones establecidas
     *
     * @return  \DTLaravelEloquent\RDTOptions
     */
    public function getOptions(): RDTOptions
    {
        return $this->options;
    }
    /**
     * Mezcla opciones
     *
     * @param   \DTLaravelEloquent\RDTOptions|array  $options  Opciones a utilizar
     *
     * @return  \DTLaravelEloquent\RDT
     */
    public function mergeOptions(array|RDTOptions $options): self
    {
        $this->options = $this->options->merge(
            $options instanceof RDTOptions ? $options->toArray() : $options
        );
        return $this;
    }
    public function humanHeaders(array $header) : self 
    {
        return $this->mergeOptions([
            "labels" => [
                "headers" => $header
            ] 
        ]);
    }
    /**
     * Inicializa los registros logs
     *
     * @return  \DTLaravelEloquent\RDTOptions
     */
    public function log(): self
    {
        $this->logEnabled = true;
        return $this;
    }  
    /**
     * Excluye campos del Data
     *
     * @param   array  $fields  Lista de campos
     *
     * @return  \DTLaravelEloquent\RDT
     */
    public function excludeFields(array $fields): self
    {
        $this->excludedFields = $fields;
        return $this;
    }
    /**
     * Procesa los datos a fin de ser compatibles con el JS
     *
     * @return  array
     */
    protected function processData(): array
    {
        $headings = [];
        $data = [];

        if ($this->data instanceof Collection || $this->data instanceof EloquentCollection) {
            $this->data = $this->data->toArray();
        }

        if (!empty($this->data)) {
            $headings = array_keys(array_diff_key($this->data[0], array_flip($this->excludedFields)));
            foreach ($this->data as $item) {
                $data[] = array_values(array_diff_key($item, array_flip($this->excludedFields)));
            }
        }

        return compact('headings', 'data');
    }
    /**
     * Retorna los datos procesados
     *
     * @return  array
     */
    public function getData(): array {
        return $this->processData();
    }
    /**
     * Retorna los datos sin procesar
     *
     * @return  array
     */
    public function getRawData() : array {
        if ($this->data instanceof Collection || $this->data instanceof EloquentCollection) {
            return $this->data->toArray();
        }
        return $this->data;
    }
    /**
     * Permite establecer un ID único para esta tabla
     *
     * @param   string  $id  El ID
     *
     * @return  RDT
     */
    public function set_uniqueID(string $id): self
    {
        $this->uniqueId = $id;
        return $this;
    }
    /**
     * Renderiza la vista, la tabla con todo su contenido
     *
     * @return  \Illuminate\Contracts\View\View
     */
    public function render()
    {
        $processedData = $this->processData();

        if ($this->logEnabled) {
            // Log the data and options
            Log::info('RDT Data-'.$this->uniqueId.':', $this->data->toArray());
            Log::info('RDT Options-'.$this->uniqueId.':', $this->options->toArray());
        }

        // Render the DataTable (this is a placeholder, you need to implement the actual rendering logic)
        return view('datatable::datatable', [
            'data' => $processedData,
            'options' => $this->options->toArray(),
            'uniqueId' => $this->uniqueId,
            "exname" => config("RDataTable.export.name", "RDTExport")
        ]);
    }
}
