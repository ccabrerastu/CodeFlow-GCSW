<h2 class="section-title">Información General del Proyecto</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="detail-item"><span class="detail-label">ID Proyecto:</span> <?= htmlspecialchars($proyecto['id_proyecto']) ?></p>
                    <p class="detail-item"><span class="detail-label">Nombre:</span> <?= htmlspecialchars($proyecto['nombre_proyecto']) ?></p>
                    <p class="detail-item"><span class="detail-label">Descripción:</span> <?= nl2br(htmlspecialchars($proyecto['descripcion'] ?? 'N/A')) ?></p>
                </div>
                <div>
                    <p class="detail-item"><span class="detail-label">Metodología:</span> <?= htmlspecialchars($proyecto['nombre_metodologia'] ?? 'No asignada') ?></p>
                    <p class="detail-item"><span class="detail-label">Product Owner:</span> <?= htmlspecialchars($proyecto['nombre_product_owner'] ?? 'No asignado') ?></p>
                    <p class="detail-item"><span class="detail-label">Fecha Inicio Planificada:</span> <?= htmlspecialchars($proyecto['fecha_inicio_planificada'] ? date('d/m/Y', strtotime($proyecto['fecha_inicio_planificada'])) : 'N/A') ?></p>
                    <p class="detail-item"><span class="detail-label">Fecha Fin Planificada:</span> <?= htmlspecialchars($proyecto['fecha_fin_planificada'] ? date('d/m/Y', strtotime($proyecto['fecha_fin_planificada'])) : 'N/A') ?></p>
                    <p class="detail-item"><span class="detail-label">Estado:</span> <?= htmlspecialchars($proyecto['estado_proyecto']) ?></p>
                </div>
            </div>
            <div class="mt-6">
                <a href="index.php?c=Proyecto&a=mostrarFormularioProyecto&id_proyecto=<?= $proyecto['id_proyecto'] ?>" class="btn btn-primary">
                    <i class="fas fa-edit mr-1"></i> Editar Datos Generales
                </a>
            </div>