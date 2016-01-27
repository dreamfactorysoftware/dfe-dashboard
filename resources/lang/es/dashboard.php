<?php
/**
 * Spanish - Dashboard strings
 */
return [
    //******************************************************************************
    //* Operation results (flash alert settings)
    //******************************************************************************
    'success'                     => ['title' => '¡Éxito!',],
    'failure'                     => ['title' => '¡Fracaso!',],
    //******************************************************************************
    //* Instance status
    //******************************************************************************
    'status-error'                => 'Hubo un error al completar su petición.',
    'status-starting'             => 'Su servidor se está iniciando.',
    'status-stopping'             => 'Su servidor se está cerrando.',
    'status-terminating'          => 'Su ejemplo se termina siendo blanco.',
    'status-up'                   => 'Su servidor está en marcha.',
    'status-down'                 => 'Su servidor se apaga.',
    'status-dead'                 => 'Su servidor ha muerto.',
    'status-other'                => 'Se está procesando su petición.',
    //******************************************************************************
    //* Instance panel text
    //******************************************************************************
    'instance-name-label'         => 'Nuevo Nombre Instancia',
    'instance-or-label'           => 'otro',
    'instance-import-label'       => 'Restaurar una Exportación',
    'instance-import-button-text' => 'Importación',
    'instance-proof-text'         => 'Yo no soy un robot.',
    'instance-create-heading'     => 'Crear Nuevo',
    'instance-create-title'       => 'Crear un Nuevo Servidor',
    'instance-create-button-text' => 'Crear',
    'instance-create-help'        => <<<HTML
<p class="help-block">Nosotros le enviaremos un correo electrónico cuando su plataforma está lista.</p>
HTML

    ,
    'instance-create'             => <<<HTML
<p>Por favor, introduzca el nombre de la nueva servidor de abajo. Letras, números, y guiones son los únicos caracteres permitidos.</p>
HTML

    ,
    'instance-import-heading'     => 'Restaurar una exportación',
    'instance-import-title'       => 'Tener una instantánea existente?',
    //******************************************************************************
    //* Instance panel bodies
    //******************************************************************************
    'instance-default'            => null,
    'instance-import'             => <<<HTML
<p>Por favor, elija una exportación de la lista de abajo. Alternativamente, puede subir su propio con su navegador haciendo clic en el botón <strong>Tu Propia Upload</strong>.</p>
<p class="help-block">Actualmente, sólo se admiten las exportaciones creadas por el Dashboard DreamFactory Enterprise.</p>
HTML

    ,
    //******************************************************************************
    //* Instance operational messages
    //******************************************************************************
    'export-success'              => 'Su exportación se pone en cola. Usted recibirá un correo electrónico cuando se ha completado.',
    'export-failure'              => 'Su solicitud de exportación fracasó. Por favor, inténtelo de nuevo más tarde.',
    //******************************************************************************
    //* Others
    //******************************************************************************
    'session-expired'             => 'Su sesión ha caducado o no es de otra manera válida.',
];
