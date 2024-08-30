<?php
namespace DTLaravelEloquent;

use Illuminate\Support\Collection;

/**
 * Opciones Generales del DataTable
 */
class RDTOptions
{   
    /**
     * Lista de Opciones
     *
     * @var array
     */
    protected array $options = [];
    /**
     * Inicia el sistema de Opciones
     *
     * @param   array  $options  Lista de Opciones a cargar, por defecto carga las opciones base
     *
     */
    public function __construct(array $options = [])
    {
        // Cargar opciones por defecto
        $this->options = array_merge($this->defaultOptions(), $options);
    }
    /**
     * Establece las opciones base, tomadas del componetne JS
     *
     * @return  array   lista de opciones por defecto
     */
    protected function defaultOptions(): array
    {
        return [
            // Sorting options
            'sortable' => true,
            'locale' => config('app.locale', 'en'),
            'numeric' => true,
            'caseFirst' => 'false',

            // Searching options
            'searchable' => true,
            'sensitivity' => 'base',
            'ignorePunctuation' => true,
            'destroyable' => true,
            'searchItemSeparator' => '',
            'searchQuerySeparator' => ' ',
            'searchAnd' => false,

            // Data options
            'data' => [],
            'type' => config('RDataTable.options.type', 'html'),
            'format' => config('RDataTable.options.format', 'YYYY-MM-DD'),
            'columns' => [],

            // Pagination options
            'paging' => true,
            'perPage' => config('RDataTable.options.perPage', 20),
            'perPageSelect' => config('RDataTable.options.perPageSelect', [5, 20, 50, 100, 150]),
            'nextPrev' => true,
            'firstLast' => false,
            'prevText' => '‹',
            'nextText' => '›',
            'firstText' => '«',
            'lastText' => '»',
            'ellipsisText' => '…',
            'truncatePager' => true,
            'pagerDelta' => 2,

            // Scroll options
            'scrollY' => '',

            // Fixed columns and height
            'fixedColumns' => true,
            'fixedHeight' => false,

            // Table layout options
            'footer' => false,
            'header' => true,
            'hiddenHeader' => false,
            'caption' => null,

            // Navigation options
            'rowNavigation' => false,
            'tabIndex' => false,


            // Labels
            'labels' => $this->loadLabels(config('app.locale', 'en')),
            
            // Class names
            'classes' => [
                'active' => 'active',
                'ascending' => 'datatable-ascending',
                'bottom' => 'datatable-bottom',
                'container' => 'datatable-container',
                'cursor' => 'datatable-cursor',
                'descending' => 'datatable-descending',
                'disabled' => 'disabled',
                'dropdown' => 'datatable-dropdown',
                'ellipsis' => 'datatable-ellipsis',
                'filter' => 'datatable-filter',
                'filterActive' => 'datatable-filter-active',
                'empty' => 'datatable-empty',
                'headercontainer' => 'datatable-headercontainer',
                'hidden' => 'datatable-hidden',
                'info' => 'datatable-info',
                'input' => 'datatable-input',
                'loading' => 'datatable-loading',
                'pagination' => 'datatable-pagination',
                'paginationList' => 'pagination',
                'paginationListItem' => 'page-item',
                'paginationListItemLink' => 'page-link',
                'search' => 'datatable-search',
                'selector' => 'form-select',
                'sorter' => 'datatable-sorter',
                'table' => 'datatable-table',
                'top' => 'datatable-top',
                'wrapper' => 'datatable-wrapper',
            ],
        ];
    }
    /**
     * Carga el idioma del proyecto y lo asigna
     * Actualmente solo esta disponible en Español[es] é Ingles[en]
     *
     * @param   string  $locale  Idioma a utilizarl
     *
     * @return  array            lista de labels según idioma
     */
    protected function loadLabels(string $locale): array
    {
        return trans('datatable::datatable', [], $locale);
    }
    /**
     * Establece una opción
     *
     * @param   string  $key    La Clave de la opcion
     * @param   mixed  $value  el valor
     *
     * @return  RDTOptions
     */
    public function set(string $key, $value): self
    {
        $this->options[$key] = $value;
        return $this;
    }
    /**
     * Mezcla las opciones con una nueva matriz
     *
     * @param   array  $options  Matriz de Opciones
     *
     * @return  RDTOptions
     */
    public function merge(array $options): self
    {
        $this->options = DtrecursiveMerge($this->options, $options);
        return $this;
    }
    /**
     * Retorna la lista de opciones en formato de array
     *
     * @return  array
     */
    public function toArray(): array
    {
        return $this->options;
    }
    /**
     * Retorna la lista de opciones como una colección laravel
     *
     * @return  \Illuminate\Support\Collection
     */
    public function toCollect(): Collection
    {
        return collect($this->options);
    }
    
}
