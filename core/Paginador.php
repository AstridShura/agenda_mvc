<?php

/**
 * ============================================================
 * CLASE Paginador
 * ============================================================
 * Clase reutilizable para paginar cualquier listado.
 *
 * Funcionalidades:
 *   → Botón Primera página  |«
 *   → Botón Anterior        «
 *   → Páginas numeradas con rango
 *   → Botón Siguiente       »
 *   → Botón Última página   »|
 *   → Selector de registros por página (5, 15, 50)
 *   → Info: "Mostrando X - Y de Z registros"
 * ============================================================
 */

class Paginador
{
    private int $total;
    private int $porPagina;
    private int $paginaActual;
    private int $totalPaginas;

    // Opciones disponibles para el selector
    private array $opcionesPorPagina = [5, 15, 50];

    // ─────────────────────────────────────────────────────────
    public function __construct(
        int $total,
        int $porPagina    = 5,
        int $paginaActual = 1
    ) {
        $this->total        = $total;
        $this->porPagina    = $porPagina;
        $this->totalPaginas = (int) ceil($total / $porPagina);
        $this->paginaActual = max(1, min(
            $paginaActual,
            $this->totalPaginas ?: 1
        ));
    }

    // ─────────────────────────────────────────────────────────
    public function getOffset(): int
    {
        return ($this->paginaActual - 1) * $this->porPagina;
    }

    public function getPorPagina(): int    { return $this->porPagina; }
    public function getPaginaActual(): int { return $this->paginaActual; }
    public function getTotalPaginas(): int { return $this->totalPaginas; }
    public function getTotal(): int        { return $this->total; }

    public function necesitaPaginacion(): bool
    {
        return $this->totalPaginas > 1;
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Genera el HTML completo del paginador.
     *
     * @param string $urlBase URL base sin parámetros
     *                        Ej: http://agenda.local/contactos
     */
    public function renderizar(string $urlBase): string
    {
        $html = '<div class="d-flex flex-column flex-md-row
                             align-items-center justify-content-between
                             gap-3 mt-4">';

        // ── Columna izquierda: selector de registros ─────────
        $html .= $this->renderizarSelector($urlBase);

        // ── Columna centro: info de registros ────────────────
        $html .= $this->renderizarInfo();

        // ── Columna derecha: navegación de páginas ───────────
        $html .= $this->renderizarNavegacion($urlBase);

        $html .= '</div>';

        return $html;
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Selector de registros por página.
     * Muestra: Mostrar [5 ▼] registros por página
     */
    private function renderizarSelector(string $urlBase): string
    {
        $html  = '<div class="d-flex align-items-center gap-2">';
        $html .= '<span class="text-muted" style="font-size:.85rem;white-space:nowrap">';
        $html .= '<i class="bi bi-list-ol me-1"></i>Mostrar:';
        $html .= '</span>';
        $html .= '<div class="btn-group btn-group-sm" role="group">';

        foreach ($this->opcionesPorPagina as $opcion) {
            $activo = $this->porPagina === $opcion ? 'btn-primary' : 'btn-outline-primary';
            $url    = $urlBase . '?pagina=1&porpagina=' . $opcion;

            $html .= '<a href="' . $url . '" class="btn btn-sm ' . $activo . '">';
            $html .= $opcion;
            $html .= '</a>';
        }

        $html .= '</div>';
        $html .= '<span class="text-muted" style="font-size:.85rem">por página</span>';
        $html .= '</div>';

        return $html;
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Info de registros mostrados.
     * Muestra: "Mostrando 1 - 5 de 25 registros"
     */
    private function renderizarInfo(): string
    {
        if ($this->total === 0) {
            return '<span class="text-muted" style="font-size:.85rem">
                        Sin registros
                    </span>';
        }

        $desde = $this->getOffset() + 1;
        $hasta = min($this->getOffset() + $this->porPagina, $this->total);

        return '<span class="text-muted text-center" style="font-size:.85rem">' .
               'Mostrando <strong>' . $desde . '</strong> — ' .
               '<strong>' . $hasta . '</strong> de ' .
               '<strong>' . $this->total . '</strong> registros' .
               '</span>';
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Navegación de páginas con todos los botones.
     * |« « 1 2 [3] 4 5 » »|
     */
    private function renderizarNavegacion(string $urlBase): string
    {
        if (!$this->necesitaPaginacion()) {
            return '<div></div>'; // espacio vacío para mantener layout
        }

        $pp   = '&porpagina=' . $this->porPagina;
        $html = '<nav aria-label="Paginación">';
        $html .= '<ul class="pagination pagination-sm mb-0">';

        // ── |« Primera página ────────────────────────────────
        $disabled = $this->paginaActual <= 1 ? 'disabled' : '';
        $url      = $urlBase . '?pagina=1' . $pp;
        $html .= '<li class="page-item ' . $disabled . '">';
        $html .= '<a class="page-link" href="' . $url . '"
                     title="Primera página">
                     <i class="bi bi-chevron-double-left"></i>
                  </a>';
        $html .= '</li>';

        // ── « Anterior ───────────────────────────────────────
        $url  = $urlBase . '?pagina=' . ($this->paginaActual - 1) . $pp;
        $html .= '<li class="page-item ' . $disabled . '">';
        $html .= '<a class="page-link" href="' . $url . '"
                     title="Página anterior">
                     <i class="bi bi-chevron-left"></i>
                  </a>';
        $html .= '</li>';

        // ── Páginas numeradas ────────────────────────────────
        $rango  = 2;
        $inicio = max(1, $this->paginaActual - $rango);
        $fin    = min($this->totalPaginas, $this->paginaActual + $rango);

        // Muestra "1 ..." si el inicio no es la primera
        if ($inicio > 1) {
            $html .= $this->itemPagina(1, $urlBase, $pp);
            if ($inicio > 2) {
                $html .= '<li class="page-item disabled">';
                $html .= '<span class="page-link">…</span>';
                $html .= '</li>';
            }
        }

        // Rango de páginas
        for ($i = $inicio; $i <= $fin; $i++) {
            $html .= $this->itemPagina($i, $urlBase, $pp);
        }

        // Muestra "... N" si el fin no es la última
        if ($fin < $this->totalPaginas) {
            if ($fin < $this->totalPaginas - 1) {
                $html .= '<li class="page-item disabled">';
                $html .= '<span class="page-link">…</span>';
                $html .= '</li>';
            }
            $html .= $this->itemPagina($this->totalPaginas, $urlBase, $pp);
        }

        // ── » Siguiente ──────────────────────────────────────
        $disabled = $this->paginaActual >= $this->totalPaginas ? 'disabled' : '';
        $url      = $urlBase . '?pagina=' . ($this->paginaActual + 1) . $pp;
        $html .= '<li class="page-item ' . $disabled . '">';
        $html .= '<a class="page-link" href="' . $url . '"
                     title="Página siguiente">
                     <i class="bi bi-chevron-right"></i>
                  </a>';
        $html .= '</li>';

        // ── »| Última página ─────────────────────────────────
        $url  = $urlBase . '?pagina=' . $this->totalPaginas . $pp;
        $html .= '<li class="page-item ' . $disabled . '">';
        $html .= '<a class="page-link" href="' . $url . '"
                     title="Última página">
                     <i class="bi bi-chevron-double-right"></i>
                  </a>';
        $html .= '</li>';

        $html .= '</ul>';
        $html .= '</nav>';

        return $html;
    }

    // ─────────────────────────────────────────────────────────
    /**
     * Genera un item <li> individual.
     */
    private function itemPagina(
        int    $numero,
        string $urlBase,
        string $pp
    ): string {
        $activa = $this->paginaActual === $numero ? 'active' : '';
        $url    = $urlBase . '?pagina=' . $numero . $pp;

        return '<li class="page-item ' . $activa . '">' .
               '<a class="page-link" href="' . $url . '">' .
               $numero .
               '</a></li>';
    }
}